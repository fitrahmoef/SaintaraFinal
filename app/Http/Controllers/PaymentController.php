<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TokenPurchase;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Handle Midtrans notification callback
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleNotification(Request $request)
    {
        try {
            Log::info('Payment notification received', $request->all());

            // Handle notification dari Midtrans
            $notification = $this->midtransService->handleNotification();

            // Find transaksi berdasarkan kode_transaksi (order_id)
            $transaksi = Transaction::where('kode_transaksi', $notification['order_id'])->first();

            if (!$transaksi) {
                Log::error('Transaction not found', ['order_id' => $notification['order_id']]);
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found',
                ], 404);
            }

            // Update transaksi status
            DB::beginTransaction();
            try {
                $oldStatus = $transaksi->status_pembayaran;
                $newStatus = $notification['status'];

                // Prepare payment metadata
                $paymentMetadata = $transaksi->payment_metadata ?? [];
                $paymentMetadata['payment_type'] = $notification['payment_type'];
                $paymentMetadata['transaction_status'] = $notification['transaction_status'];
                $paymentMetadata['fraud_status'] = $notification['fraud_status'];
                $paymentMetadata['transaction_time'] = $notification['transaction_time'];

                $updateData = [
                    'status_pembayaran' => $newStatus,
                    'metode_pembayaran' => $notification['payment_type'],
                    'payment_metadata' => $paymentMetadata,
                ];

                // Update waktu_dibayar jika pembayaran berhasil
                if ($newStatus === 'dibayar') {
                    $updateData['waktu_dibayar'] = $notification['transaction_time'];
                }

                $transaksi->update($updateData);

                Log::info('Transaction status updated', [
                    'order_id' => $notification['order_id'],
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ]);

                // Jika pembayaran berhasil, create token purchase
                if ($newStatus === 'dibayar' && $oldStatus !== 'dibayar') {
                    $this->createTokenPurchase($transaksi);

                    // TODO: Trigger email notification untuk pembayaran berhasil
                    // event(new PaymentSuccessEvent($transaksi));
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Notification processed successfully',
                ], 200);

            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Failed to update transaction', [
                    'error' => $e->getMessage(),
                    'order_id' => $notification['order_id'],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update transaction',
                ], 500);
            }

        } catch (Exception $e) {
            Log::error('Payment notification handling failed', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Notification handling failed',
            ], 500);
        }
    }

    /**
     * Create token purchase after successful payment
     *
     * @param Transaction $transaksi
     * @return void
     */
    private function createTokenPurchase(Transaction $transaksi): void
    {
        try {
            // Check if token purchase already exists
            $existingPurchase = TokenPurchase::where('transaction_id', $transaksi->id)->first();

            if ($existingPurchase) {
                Log::warning('Token purchase already exists for transaction', [
                    'transaction_id' => $transaksi->id,
                ]);
                return;
            }

            // Get package details
            $package = $transaksi->package;

            if (!$package) {
                throw new Exception('Package not found for transaction');
            }

            // Create token purchase
            $kodeToken = TokenPurchase::generateKodeToken();
            $tanggalKadaluarsa = $package->masa_aktif_hari
                ? now()->addDays($package->masa_aktif_hari)
                : null;

            TokenPurchase::create([
                'customer_id' => $transaksi->customer_id,
                'transaction_id' => $transaksi->id,
                'package_id' => $transaksi->package_id,
                'kode_token' => $kodeToken,
                'jumlah_token' => $package->jumlah_token,
                'jumlah_terpakai' => 0,
                'status' => 'aktif',
                'tanggal_pembelian' => now(),
                'tanggal_kadaluarsa' => $tanggalKadaluarsa,
            ]);

            Log::info('Token purchase created', [
                'customer_id' => $transaksi->customer_id,
                'transaction_id' => $transaksi->id,
                'kode_token' => $kodeToken,
                'jumlah_token' => $package->jumlah_token,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to create token purchase', [
                'error' => $e->getMessage(),
                'transaction_id' => $transaksi->id,
            ]);
            throw $e;
        }
    }

    /**
     * Get transaction status
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransactionStatus(Request $request)
    {
        try {
            $orderId = $request->input('order_id');

            if (!$orderId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order ID is required',
                ], 400);
            }

            // Get status from Midtrans
            $status = $this->midtransService->getTransactionStatus($orderId);

            // Find local transaction
            $transaksi = Transaction::where('kode_transaksi', $orderId)->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'midtrans_status' => $status,
                    'local_transaction' => $transaksi,
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Get transaction status failed', [
                'error' => $e->getMessage(),
                'order_id' => $request->input('order_id'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get transaction status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel transaction
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelTransaction(Request $request)
    {
        try {
            $orderId = $request->input('order_id');

            if (!$orderId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order ID is required',
                ], 400);
            }

            // Cancel at Midtrans
            $result = $this->midtransService->cancelTransaction($orderId);

            // Update local transaction
            DB::beginTransaction();
            try {
                $transaksi = Transaction::where('kode_transaksi', $orderId)->first();

                if ($transaksi) {
                    $paymentMetadata = $transaksi->payment_metadata ?? [];
                    $paymentMetadata['transaction_status'] = 'cancel';

                    $transaksi->update([
                        'status_pembayaran' => 'dibatalkan',
                        'payment_metadata' => $paymentMetadata,
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Transaction cancelled successfully',
                    'data' => $result,
                ]);

            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            Log::error('Cancel transaction failed', [
                'error' => $e->getMessage(),
                'order_id' => $request->input('order_id'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel transaction: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle payment success page
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function paymentSuccess(Request $request)
    {
        $orderId = $request->query('order_id');
        $transaksi = null;

        if ($orderId) {
            $transaksi = Transaction::where('kode_transaksi', $orderId)
                ->with(['customer', 'package', 'tokenPurchase'])
                ->first();
        }

        return inertia('Personal/PaymentSuccess', [
            'transaction' => $transaksi,
        ]);
    }

    /**
     * Handle payment error page
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function paymentError(Request $request)
    {
        $orderId = $request->query('order_id');
        $transaksi = null;

        if ($orderId) {
            $transaksi = Transaction::where('kode_transaksi', $orderId)
                ->with(['customer', 'package'])
                ->first();
        }

        return inertia('Personal/PaymentError', [
            'transaction' => $transaksi,
        ]);
    }

    /**
     * Verify payment webhook signature
     *
     * @param Request $request
     * @return bool
     */
    private function verifyWebhookSignature(Request $request): bool
    {
        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        $signatureKey = $request->input('signature_key');

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey) {
            return false;
        }

        return $this->midtransService->verifySignature(
            $orderId,
            $statusCode,
            $grossAmount,
            $signatureKey
        );
    }
}
