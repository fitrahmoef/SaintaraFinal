<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\TokenPurchase;
use App\Models\Transaction;
use App\Models\Customer;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TokenController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function index()
    {
        $customer = auth()->user()->customer;

        if (!$customer) {
            return response()->json([
                'tokenBalance' => 0,
                'transactions' => [],
                'message' => 'Customer profile not found'
            ]);
        }

        $tokenPurchases = TokenPurchase::where('customer_id', $customer->id)
            ->with(['package', 'transaction'])
            ->active()
            ->get();

        $totalTokens = $tokenPurchases->sum('jumlah_token');
        $usedTokens = $tokenPurchases->sum('jumlah_terpakai');
        $remainingTokens = $totalTokens - $usedTokens;

        $transactions = Transaction::where('customer_id', $customer->id)
            ->with('package')
            ->latest('waktu_dibuat')
            ->limit(10)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'kode' => $transaction->kode_transaksi,
                    'tanggal' => $transaction->waktu_dibuat->format('d M Y H:i'),
                    'paket' => $transaction->package->nama_paket ?? 'N/A',
                    'jumlah' => 'Rp ' . number_format($transaction->jumlah_bayar, 0, ',', '.'),
                    'status' => $transaction->status_pembayaran,
                    'metode' => $transaction->metode_pembayaran ?? 'N/A',
                ];
            });

        return response()->json([
            'tokenBalance' => $remainingTokens,
            'totalTokens' => $totalTokens,
            'usedTokens' => $usedTokens,
            'transactions' => $transactions,
        ]);
    }

    public function packages()
    {
        $packages = Package::active()
            ->orderBy('harga', 'asc')
            ->get()
            ->map(function ($package) {
                return [
                    'id' => $package->id,
                    'nama' => $package->nama_paket,
                    'harga' => (float) $package->harga,
                    'harga_formatted' => 'Rp ' . number_format($package->harga, 0, ',', '.'),
                    'deskripsi' => $package->deskripsi,
                    'tipe' => $package->tipe_paket,
                    'jumlah_token' => $package->jumlah_token,
                    'masa_aktif' => $package->masa_aktif_hari . ' hari',
                ];
            });

        return response()->json(['packages' => $packages]);
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'payment_gateway_id' => 'nullable|exists:payment_gateways,id',
        ]);

        $user = auth()->user();
        $customer = $user->customer;

        if (!$customer) {
            // Create customer profile if not exists
            $customer = Customer::create([
                'user_id' => $user->id,
                'nama_lengkap' => $user->name,
                'nama_panggilan' => $user->namapanggilan,
                'nomor_telepon' => $user->notelp,
                'negara' => $user->negara,
                'kota' => $user->kota,
            ]);
        }

        $package = Package::findOrFail($request->package_id);

        DB::beginTransaction();
        try {
            // Generate unique transaction code
            $kodeTransaksi = Transaction::generateKodeTransaksi();

            // Create transaction with pending status
            $transaction = new Transaction([
                'package_id' => $package->id,
                'payment_gateway_id' => $request->payment_gateway_id,
                'kode_transaksi' => $kodeTransaksi,
                'jumlah_bayar' => $package->harga,
                'status_pembayaran' => 'pending',
                'metode_pembayaran' => null, // Will be set by payment gateway
                'waktu_dibuat' => now(),
                'waktu_kadaluarsa' => now()->addHours(24),
            ]);

            // SECURITY: Set customer_id explicitly to prevent manipulation
            $transaction->setCustomer($customer->id);
            $transaction->save();

            // Prepare order details for Midtrans
            $orderDetails = [
                'order_id' => $kodeTransaksi,
                'gross_amount' => (int) $package->harga,
                'customer' => [
                    'first_name' => $customer->nama_lengkap,
                    'last_name' => '',
                    'email' => $user->email,
                    'phone' => $customer->nomor_telepon ?? '',
                ],
                'items' => [
                    [
                        'id' => $package->id,
                        'price' => (int) $package->harga,
                        'quantity' => 1,
                        'name' => $package->nama_paket,
                    ],
                ],
            ];

            // Create Midtrans Snap Token
            $snapResult = $this->midtransService->createSnapToken($orderDetails);

            // Update transaction with payment URL
            $transaction->update([
                'payment_url' => $snapResult['redirect_url'],
                'payment_metadata' => [
                    'snap_token' => $snapResult['snap_token'],
                ],
            ]);

            DB::commit();

            Log::info('Token purchase initiated', [
                'customer_id' => $customer->id,
                'transaction_id' => $transaction->id,
                'kode_transaksi' => $kodeTransaksi,
                'amount' => $package->harga,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat. Silakan lanjutkan pembayaran.',
                'transaction' => [
                    'id' => $transaction->id,
                    'kode' => $transaction->kode_transaksi,
                    'status' => $transaction->status_pembayaran,
                    'amount' => $transaction->jumlah_bayar,
                ],
                'payment' => [
                    'snap_token' => $snapResult['snap_token'],
                    'redirect_url' => $snapResult['redirect_url'],
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Token purchase failed', [
                'error' => $e->getMessage(),
                'customer_id' => $customer->id ?? null,
                'package_id' => $request->package_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pembelian: ' . $e->getMessage()
            ], 500);
        }
    }

    public function balance()
    {
        $customer = auth()->user()->customer;

        if (!$customer) {
            return response()->json(['balance' => 0]);
        }

        $tokenPurchases = TokenPurchase::where('customer_id', $customer->id)
            ->active()
            ->get();

        $totalTokens = $tokenPurchases->sum('jumlah_token');
        $usedTokens = $tokenPurchases->sum('jumlah_terpakai');
        $balance = $totalTokens - $usedTokens;

        return response()->json([
            'balance' => $balance,
            'total' => $totalTokens,
            'used' => $usedTokens,
        ]);
    }
}
