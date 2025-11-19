<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'personal'); // personal, instansi, gift

        $query = User::with('customer');

        // Filter by user type
        if ($type === 'personal') {
            $query->where('user_type', 'personal');
        } elseif ($type === 'instansi') {
            $query->where('user_type', 'instansi');
        } elseif ($type === 'gift') {
            $query->where('user_type', 'gift');
        }

        // Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()
            ->paginate(20)
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'notelp' => $user->notelp,
                    'negara' => $user->negara,
                    'kota' => $user->kota,
                    'created_at' => $user->created_at->format('d M Y'),
                    'customer' => $user->customer ? [
                        'nama_lengkap' => $user->customer->nama_lengkap,
                        'tanggal_lahir' => $user->customer->tanggal_lahir?->format('d M Y'),
                        'jenis_kelamin' => $user->customer->jenis_kelamin,
                    ] : null,
                ];
            });

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'user_type' => 'required|in:personal,admin,instansi,gift',
            'notelp' => 'nullable|string|max:20',
            'negara' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
            // Customer fields
            'nama_lengkap' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:pria,wanita',
            'golongan_darah' => 'nullable|string|max:3',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'notelp' => $request->notelp,
                'negara' => $request->negara,
                'kota' => $request->kota,
            ]);

            // SECURITY: Set user_type via protected method to prevent mass assignment exploit
            $user->setUserType($request->user_type);
            $user->save();

            // Create customer profile if applicable
            if (in_array($request->user_type, ['personal', 'gift'])) {
                Customer::create([
                    'user_id' => $user->id,
                    'nama_lengkap' => $request->nama_lengkap ?? $request->name,
                    'nama_panggilan' => $request->namapanggilan,
                    'nomor_telepon' => $request->notelp,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'golongan_darah' => $request->golongan_darah,
                    'negara' => $request->negara,
                    'kota' => $request->kota,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dibuat',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $user = User::with('customer')->findOrFail($id);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'notelp' => $user->notelp,
                'negara' => $user->negara,
                'kota' => $user->kota,
                'created_at' => $user->created_at->format('d M Y H:i'),
                'customer' => $user->customer ? [
                    'nama_lengkap' => $user->customer->nama_lengkap,
                    'nama_panggilan' => $user->customer->nama_panggilan,
                    'nomor_telepon' => $user->customer->nomor_telepon,
                    'tanggal_lahir' => $user->customer->tanggal_lahir?->format('d M Y'),
                    'jenis_kelamin' => $user->customer->jenis_kelamin,
                    'golongan_darah' => $user->customer->golongan_darah,
                ] : null,
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'user_type' => 'sometimes|in:personal,admin,instansi,gift',
            'notelp' => 'nullable|string|max:20',
            'negara' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $userData = $request->only(['name', 'email', 'notelp', 'negara', 'kota']);

            if ($request->has('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // SECURITY: Set user_type separately via protected method
            if ($request->has('user_type')) {
                $user->setUserType($request->user_type);
                $user->save();
            }

            // Update customer profile if exists
            if ($user->customer && $request->has('customer')) {
                $user->customer->update($request->customer);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diupdate',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function stats()
    {
        $totalUsers = User::count();
        $personalUsers = User::where('user_type', 'personal')->count();
        $instansiUsers = User::where('user_type', 'instansi')->count();
        $giftUsers = User::where('user_type', 'gift')->count();
        $adminUsers = User::where('user_type', 'admin')->count();

        return response()->json([
            'total' => $totalUsers,
            'personal' => $personalUsers,
            'instansi' => $instansiUsers,
            'gift' => $giftUsers,
            'admin' => $adminUsers,
        ]);
    }
}
