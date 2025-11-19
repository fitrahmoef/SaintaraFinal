<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionManagementController extends Controller
{
    /**
     * Get all transactions with pagination and filters
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['customer.user', 'package', 'paymentGateway', 'tokenPurchase'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status_pembayaran', $request->status);
        }

        // Filter by payment method
        if ($request->has('metode_pembayaran') && $request->metode_pembayaran !== 'all') {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('waktu_dibuat', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('waktu_dibuat', '<=', $request->end_date);
        }

        // Search by transaction code or customer name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                    ->orWhereHas('customer.user', function ($q) use ($search) {
                        $q->where('nama_lengkap', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $transactions = $query->paginate($request->per_page ?? 15);

        return response()->json($transactions);
    }

    /**
     * Get transaction statistics
     */
    public function stats(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth();
        $endDate = $request->end_date ?? now()->endOfMonth();

        $stats = [
            'total_transactions' => Transaction::whereBetween('waktu_dibuat', [$startDate, $endDate])->count(),
            'total_revenue' => Transaction::where('status_pembayaran', 'dibayar')
                ->whereBetween('waktu_dibuat', [$startDate, $endDate])
                ->sum('jumlah_bayar'),
            'pending_transactions' => Transaction::where('status_pembayaran', 'pending')
                ->whereBetween('waktu_dibuat', [$startDate, $endDate])
                ->count(),
            'paid_transactions' => Transaction::where('status_pembayaran', 'dibayar')
                ->whereBetween('waktu_dibuat', [$startDate, $endDate])
                ->count(),
            'failed_transactions' => Transaction::whereIn('status_pembayaran', ['gagal', 'kadaluarsa'])
                ->whereBetween('waktu_dibuat', [$startDate, $endDate])
                ->count(),
            'payment_methods' => Transaction::select('metode_pembayaran', DB::raw('count(*) as total'))
                ->whereBetween('waktu_dibuat', [$startDate, $endDate])
                ->groupBy('metode_pembayaran')
                ->get(),
            'daily_revenue' => Transaction::select(
                DB::raw('DATE(waktu_dibuat) as date'),
                DB::raw('SUM(jumlah_bayar) as revenue'),
                DB::raw('COUNT(*) as count')
            )
                ->where('status_pembayaran', 'dibayar')
                ->whereBetween('waktu_dibuat', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Get single transaction detail
     */
    public function show($id)
    {
        $transaction = Transaction::with([
            'customer.user',
            'package',
            'paymentGateway',
            'tokenPurchase.tokens'
        ])->findOrFail($id);

        return response()->json($transaction);
    }

    /**
     * Update transaction status (for manual verification)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_pembayaran' => 'required|in:pending,dibayar,gagal,kadaluarsa',
            'notes' => 'nullable|string',
        ]);

        $transaction = Transaction::findOrFail($id);

        // Only allow manual update for pending transactions
        if ($transaction->status_pembayaran !== 'pending' && $request->status_pembayaran === 'dibayar') {
            return response()->json([
                'message' => 'Hanya transaksi pending yang dapat diverifikasi manual'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $transaction->update([
                'status_pembayaran' => $request->status_pembayaran,
                'waktu_dibayar' => $request->status_pembayaran === 'dibayar' ? now() : null,
            ]);

            // If marked as paid, create token purchase
            if ($request->status_pembayaran === 'dibayar' && !$transaction->tokenPurchase) {
                $package = $transaction->package;

                $tokenPurchase = $transaction->customer->tokenPurchases()->create([
                    'package_id' => $package->id,
                    'transaction_id' => $transaction->id,
                    'jumlah_token' => $package->jumlah_token,
                    'tanggal_pembelian' => now(),
                    'tanggal_kadaluarsa' => now()->addDays($package->masa_aktif_hari),
                    'status_token' => 'aktif',
                ]);

                // Create individual tokens
                for ($i = 0; $i < $package->jumlah_token; $i++) {
                    $tokenPurchase->tokens()->create([
                        'customer_id' => $transaction->customer_id,
                        'tanggal_kadaluarsa' => now()->addDays($package->masa_aktif_hari),
                        'status_token' => 'tersedia',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Status transaksi berhasil diperbarui',
                'transaction' => $transaction->fresh(['customer.user', 'package', 'tokenPurchase.tokens'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal memperbarui status transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export transactions to CSV
     */
    public function export(Request $request)
    {
        $query = Transaction::with(['customer.user', 'package', 'paymentGateway'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status_pembayaran', $request->status);
        }
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('waktu_dibuat', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('waktu_dibuat', '<=', $request->end_date);
        }

        $transactions = $query->get();

        $csvData = [];
        $csvData[] = [
            'Kode Transaksi',
            'Nama Customer',
            'Email',
            'Paket',
            'Jumlah Bayar',
            'Metode Pembayaran',
            'Status',
            'Waktu Dibuat',
            'Waktu Dibayar'
        ];

        foreach ($transactions as $transaction) {
            $csvData[] = [
                $transaction->kode_transaksi,
                $transaction->customer->user->nama_lengkap ?? '-',
                $transaction->customer->user->email ?? '-',
                $transaction->package->nama_paket ?? '-',
                $transaction->jumlah_bayar,
                $transaction->metode_pembayaran ?? '-',
                $transaction->status_pembayaran,
                $transaction->waktu_dibuat?->format('Y-m-d H:i:s'),
                $transaction->waktu_dibayar?->format('Y-m-d H:i:s') ?? '-',
            ];
        }

        $filename = 'transactions_' . now()->format('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
