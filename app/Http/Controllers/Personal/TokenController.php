<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\TokenPurchase;
use App\Models\Transaction;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TokenController extends Controller
{
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
            // Create transaction
            $transaction = Transaction::create([
                'customer_id' => $customer->id,
                'package_id' => $package->id,
                'payment_gateway_id' => $request->payment_gateway_id,
                'kode_transaksi' => Transaction::generateKodeTransaksi(),
                'jumlah_bayar' => $package->harga,
                'status_pembayaran' => 'pending',
                'metode_pembayaran' => $request->metode_pembayaran ?? 'Transfer Bank',
                'waktu_dibuat' => now(),
                'waktu_kadaluarsa' => now()->addHours(24),
            ]);

            // Create token purchase
            $tokenPurchase = TokenPurchase::create([
                'customer_id' => $customer->id,
                'transaction_id' => $transaction->id,
                'package_id' => $package->id,
                'kode_token' => TokenPurchase::generateKodeToken(),
                'jumlah_token' => $package->jumlah_token,
                'jumlah_terpakai' => 0,
                'status' => 'aktif',
                'tanggal_pembelian' => now(),
                'tanggal_kadaluarsa' => now()->addDays($package->masa_aktif_hari),
            ]);

            // Update transaction status to paid (for demo - in real app this would be done by payment gateway callback)
            $transaction->update([
                'status_pembayaran' => 'dibayar',
                'waktu_dibayar' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Token berhasil dibeli',
                'transaction' => [
                    'kode' => $transaction->kode_transaksi,
                    'status' => $transaction->status_pembayaran,
                ],
                'token' => [
                    'kode' => $tokenPurchase->kode_token,
                    'jumlah' => $tokenPurchase->jumlah_token,
                    'kadaluarsa' => $tokenPurchase->tanggal_kadaluarsa->format('d M Y'),
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
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
