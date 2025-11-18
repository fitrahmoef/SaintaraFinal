<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Midtrans\Transaction;
use Exception;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        $this->configureMidtrans();
    }

    /**
     * Configure Midtrans settings
     */
    private function configureMidtrans(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = config('midtrans.is_sanitized', true);
        Config::$is3ds = config('midtrans.is_3ds', true);
    }

    /**
     * Create Snap Token for payment
     *
     * @param array $orderDetails
     * @return array
     * @throws Exception
     */
    public function createSnapToken(array $orderDetails): array
    {
        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $orderDetails['order_id'],
                    'gross_amount' => $orderDetails['gross_amount'],
                ],
                'customer_details' => [
                    'first_name' => $orderDetails['customer']['first_name'],
                    'last_name' => $orderDetails['customer']['last_name'] ?? '',
                    'email' => $orderDetails['customer']['email'],
                    'phone' => $orderDetails['customer']['phone'] ?? '',
                ],
                'item_details' => $orderDetails['items'],
                'callbacks' => [
                    'finish' => config('midtrans.finish_url'),
                    'error' => config('midtrans.error_url'),
                ],
            ];

            // Optional: Add enabled payments
            if (isset($orderDetails['enabled_payments'])) {
                $params['enabled_payments'] = $orderDetails['enabled_payments'];
            }

            $snapToken = Snap::getSnapToken($params);

            Log::info('Midtrans Snap Token Created', [
                'order_id' => $orderDetails['order_id'],
                'amount' => $orderDetails['gross_amount']
            ]);

            return [
                'success' => true,
                'snap_token' => $snapToken,
                'redirect_url' => $this->getSnapRedirectUrl($snapToken),
            ];
        } catch (Exception $e) {
            Log::error('Midtrans Snap Token Creation Failed', [
                'error' => $e->getMessage(),
                'order_id' => $orderDetails['order_id'] ?? 'unknown'
            ]);

            throw new Exception('Failed to create payment: ' . $e->getMessage());
        }
    }

    /**
     * Get Snap Redirect URL
     *
     * @param string $snapToken
     * @return string
     */
    private function getSnapRedirectUrl(string $snapToken): string
    {
        $baseUrl = config('midtrans.is_production')
            ? 'https://app.midtrans.com/snap/v2/vtweb/'
            : 'https://app.sandbox.midtrans.com/snap/v2/vtweb/';

        return $baseUrl . $snapToken;
    }

    /**
     * Handle notification from Midtrans
     *
     * @return array
     * @throws Exception
     */
    public function handleNotification(): array
    {
        try {
            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;
            $orderId = $notification->order_id;
            $paymentType = $notification->payment_type;
            $grossAmount = $notification->gross_amount;

            Log::info('Midtrans Notification Received', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'payment_type' => $paymentType,
            ]);

            $status = $this->determinePaymentStatus($transactionStatus, $fraudStatus);

            return [
                'order_id' => $orderId,
                'status' => $status,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'payment_type' => $paymentType,
                'gross_amount' => $grossAmount,
                'transaction_time' => $notification->transaction_time ?? now(),
            ];
        } catch (Exception $e) {
            Log::error('Midtrans Notification Handling Failed', [
                'error' => $e->getMessage(),
            ]);

            throw new Exception('Failed to handle notification: ' . $e->getMessage());
        }
    }

    /**
     * Determine payment status based on transaction and fraud status
     *
     * @param string $transactionStatus
     * @param string|null $fraudStatus
     * @return string
     */
    private function determinePaymentStatus(string $transactionStatus, ?string $fraudStatus): string
    {
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'accept') {
                return 'dibayar';
            }
            return 'pending';
        } elseif ($transactionStatus == 'settlement') {
            return 'dibayar';
        } elseif ($transactionStatus == 'pending') {
            return 'menunggu_pembayaran';
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            return 'gagal';
        }

        return 'pending';
    }

    /**
     * Get transaction status from Midtrans
     *
     * @param string $orderId
     * @return array
     * @throws Exception
     */
    public function getTransactionStatus(string $orderId): array
    {
        try {
            $status = Transaction::status($orderId);

            Log::info('Midtrans Transaction Status Retrieved', [
                'order_id' => $orderId,
                'status' => $status->transaction_status ?? 'unknown',
            ]);

            return [
                'order_id' => $status->order_id,
                'transaction_status' => $status->transaction_status,
                'fraud_status' => $status->fraud_status ?? null,
                'payment_type' => $status->payment_type ?? null,
                'gross_amount' => $status->gross_amount ?? 0,
                'transaction_time' => $status->transaction_time ?? null,
            ];
        } catch (Exception $e) {
            Log::error('Midtrans Get Transaction Status Failed', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
            ]);

            throw new Exception('Failed to get transaction status: ' . $e->getMessage());
        }
    }

    /**
     * Cancel transaction
     *
     * @param string $orderId
     * @return array
     * @throws Exception
     */
    public function cancelTransaction(string $orderId): array
    {
        try {
            $result = Transaction::cancel($orderId);

            Log::info('Midtrans Transaction Cancelled', [
                'order_id' => $orderId,
            ]);

            return [
                'success' => true,
                'message' => 'Transaction cancelled successfully',
                'order_id' => $orderId,
            ];
        } catch (Exception $e) {
            Log::error('Midtrans Cancel Transaction Failed', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
            ]);

            throw new Exception('Failed to cancel transaction: ' . $e->getMessage());
        }
    }

    /**
     * Refund transaction
     *
     * @param string $orderId
     * @param int|null $amount
     * @param string|null $reason
     * @return array
     * @throws Exception
     */
    public function refundTransaction(string $orderId, ?int $amount = null, ?string $reason = null): array
    {
        try {
            $params = [];
            if ($amount) {
                $params['amount'] = $amount;
            }
            if ($reason) {
                $params['reason'] = $reason;
            }

            $result = Transaction::refund($orderId, $params);

            Log::info('Midtrans Transaction Refunded', [
                'order_id' => $orderId,
                'amount' => $amount,
            ]);

            return [
                'success' => true,
                'message' => 'Transaction refunded successfully',
                'order_id' => $orderId,
            ];
        } catch (Exception $e) {
            Log::error('Midtrans Refund Transaction Failed', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
            ]);

            throw new Exception('Failed to refund transaction: ' . $e->getMessage());
        }
    }

    /**
     * Verify signature from notification
     *
     * @param string $orderId
     * @param string $statusCode
     * @param string $grossAmount
     * @param string $signatureKey
     * @return bool
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $serverKey = config('midtrans.server_key');
        $input = $orderId . $statusCode . $grossAmount . $serverKey;
        $signature = hash('sha512', $input);

        return $signature === $signatureKey;
    }
}
