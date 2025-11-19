<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackageManagementController extends Controller
{
    /**
     * Get all packages
     */
    public function index(Request $request)
    {
        $query = Package::withCount(['tokenPurchases'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        // Filter by type
        if ($request->has('tipe_paket') && $request->tipe_paket !== 'all') {
            $query->where('tipe_paket', $request->tipe_paket);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('nama_paket', 'like', "%{$request->search}%");
        }

        if ($request->has('paginate') && $request->paginate === 'false') {
            $packages = $query->get();
        } else {
            $packages = $query->paginate($request->per_page ?? 15);
        }

        return response()->json($packages);
    }

    /**
     * Get single package
     */
    public function show($id)
    {
        $package = Package::withCount(['tokenPurchases'])
            ->with(['tokenPurchases' => function ($query) {
                $query->latest()->limit(10);
            }])
            ->findOrFail($id);

        return response()->json($package);
    }

    /**
     * Create new package
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_paket' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'tipe_paket' => 'required|in:personal,instansi',
            'jumlah_token' => 'required|integer|min:1',
            'masa_aktif_hari' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $package = Package::create($request->all());

        return response()->json([
            'message' => 'Paket berhasil dibuat',
            'package' => $package
        ], 201);
    }

    /**
     * Update package
     */
    public function update(Request $request, $id)
    {
        $package = Package::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama_paket' => 'sometimes|required|string|max:255',
            'harga' => 'sometimes|required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'tipe_paket' => 'sometimes|required|in:personal,instansi',
            'jumlah_token' => 'sometimes|required|integer|min:1',
            'masa_aktif_hari' => 'sometimes|required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $package->update($request->all());

        return response()->json([
            'message' => 'Paket berhasil diperbarui',
            'package' => $package->fresh()
        ]);
    }

    /**
     * Delete package
     */
    public function destroy($id)
    {
        $package = Package::findOrFail($id);

        // Check if package has been purchased
        if ($package->tokenPurchases()->count() > 0) {
            return response()->json([
                'message' => 'Tidak dapat menghapus paket yang sudah pernah dibeli'
            ], 400);
        }

        $package->delete();

        return response()->json([
            'message' => 'Paket berhasil dihapus'
        ]);
    }

    /**
     * Toggle package active status
     */
    public function toggleStatus($id)
    {
        $package = Package::findOrFail($id);
        $package->update(['is_active' => !$package->is_active]);

        return response()->json([
            'message' => 'Status paket berhasil diubah',
            'package' => $package
        ]);
    }
}
