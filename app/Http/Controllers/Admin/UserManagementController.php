<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Permission;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        // Check permission
        Gate::authorize(Permission::VIEW_USERS->value);

        $type = $request->get('type', 'personal'); // personal, instansi, gift, admin, superadmin

        $query = User::with('customer');

        // Filter by user type
        if (in_array($type, ['personal', 'instansi', 'gift', 'admin', 'superadmin'])) {
            $query->where('user_type', $type);
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
                    'role_label' => $user->getRole()?->label(),
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
        // Check permission
        Gate::authorize(Permission::CREATE_USERS->value);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'user_type' => 'required|in:personal,admin,instansi,gift,superadmin',
            'notelp' => 'nullable|string|max:20',
            'negara' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
            // Customer fields
            'nama_lengkap' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:pria,wanita',
            'golongan_darah' => 'nullable|string|max:3',
        ]);

        // SECURITY: Only superadmin can create other superadmins
        if ($request->user_type === 'superadmin' && !auth()->user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only superadmin can create superadmin users',
            ], 403);
        }

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
        // Check permission
        Gate::authorize(Permission::VIEW_USERS->value);

        $user = User::with('customer')->findOrFail($id);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'role_label' => $user->getRole()?->label(),
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
        // Check permission
        Gate::authorize(Permission::UPDATE_USERS->value);

        $user = User::findOrFail($id);

        // SECURITY: Prevent modifying superadmin unless you are superadmin
        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only superadmin can modify superadmin users',
            ], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'user_type' => 'sometimes|in:personal,admin,instansi,gift,superadmin',
            'notelp' => 'nullable|string|max:20',
            'negara' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
        ]);

        // SECURITY: Only superadmin can promote to superadmin
        if ($request->has('user_type') && $request->user_type === 'superadmin' && !auth()->user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only superadmin can promote users to superadmin',
            ], 403);
        }

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
        // Check permission
        Gate::authorize(Permission::DELETE_USERS->value);

        try {
            $user = User::findOrFail($id);

            // SECURITY: Prevent deleting superadmin unless you are superadmin
            if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only superadmin can delete superadmin users',
                ], 403);
            }

            // SECURITY: Prevent deleting yourself
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account',
                ], 403);
            }

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
        // Check permission
        Gate::authorize(Permission::VIEW_USERS->value);

        $totalUsers = User::count();
        $personalUsers = User::where('user_type', 'personal')->count();
        $instansiUsers = User::where('user_type', 'instansi')->count();
        $giftUsers = User::where('user_type', 'gift')->count();
        $adminUsers = User::where('user_type', 'admin')->count();
        $superadminUsers = User::where('user_type', 'superadmin')->count();

        return response()->json([
            'total' => $totalUsers,
            'personal' => $personalUsers,
            'instansi' => $instansiUsers,
            'gift' => $giftUsers,
            'admin' => $adminUsers,
            'superadmin' => $superadminUsers,
        ]);
    }

    /**
     * Get detailed user information with all related data.
     */
    public function details($id)
    {
        Gate::authorize(Permission::VIEW_USERS->value);

        $user = User::with([
            'customer',
            'transactions' => function ($query) {
                $query->latest()->limit(10);
            },
            'testResults' => function ($query) {
                $query->latest()->limit(10);
            },
        ])->findOrFail($id);

        // Get token statistics
        $tokenStats = DB::table('token_purchases')
            ->where('customer_id', $user->customer?->id)
            ->select(
                DB::raw('SUM(token_amount) as total_purchased'),
                DB::raw('COUNT(*) as purchase_count')
            )
            ->first();

        $tokenUsage = DB::table('token_usage')
            ->join('tokens', 'token_usage.token_id', '=', 'tokens.id')
            ->where('tokens.customer_id', $user->customer?->id)
            ->count();

        // Get activity summary
        $activityStats = DB::table('activity_logs')
            ->where('user_id', $id)
            ->select(
                DB::raw('COUNT(*) as total_activities'),
                DB::raw('COUNT(DISTINCT DATE(created_at)) as active_days'),
                DB::raw('MAX(created_at) as last_activity')
            )
            ->first();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'role_label' => $user->getRole()?->label(),
                'notelp' => $user->notelp,
                'negara' => $user->negara,
                'kota' => $user->kota,
                'created_at' => $user->created_at,
                'email_verified_at' => $user->email_verified_at,
            ],
            'customer' => $user->customer ? [
                'id' => $user->customer->id,
                'nama_lengkap' => $user->customer->nama_lengkap,
                'nama_panggilan' => $user->customer->nama_panggilan,
                'nomor_telepon' => $user->customer->nomor_telepon,
                'tanggal_lahir' => $user->customer->tanggal_lahir,
                'jenis_kelamin' => $user->customer->jenis_kelamin,
                'golongan_darah' => $user->customer->golongan_darah,
                'free_tokens_granted' => $user->customer->free_tokens_granted,
                'free_token_count' => $user->customer->free_token_count,
            ] : null,
            'tokens' => [
                'total_purchased' => $tokenStats->total_purchased ?? 0,
                'total_used' => $tokenUsage,
                'purchase_count' => $tokenStats->purchase_count ?? 0,
                'remaining' => ($tokenStats->total_purchased ?? 0) - $tokenUsage,
            ],
            'transactions' => $user->transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'transaction_code' => $transaction->transaction_code,
                    'amount' => $transaction->amount,
                    'status' => $transaction->status,
                    'payment_method' => $transaction->payment_method,
                    'created_at' => $transaction->created_at->format('d M Y H:i'),
                ];
            }),
            'test_results' => $user->testResults->map(function ($result) {
                return [
                    'id' => $result->id,
                    'test_name' => $result->test?->name,
                    'score' => $result->score,
                    'result_text' => $result->result_text,
                    'completed_at' => $result->completed_at?->format('d M Y H:i'),
                ];
            }),
            'activity' => [
                'total_activities' => $activityStats->total_activities ?? 0,
                'active_days' => $activityStats->active_days ?? 0,
                'last_activity' => $activityStats->last_activity,
            ],
        ]);
    }

    /**
     * Get user activity summary.
     */
    public function activitySummary($id)
    {
        Gate::authorize(Permission::VIEW_USERS->value);

        $user = User::findOrFail($id);

        // Recent activities
        $recentActivities = DB::table('activity_logs')
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Activity by module
        $activityByModule = DB::table('activity_logs')
            ->where('user_id', $id)
            ->select('module', DB::raw('COUNT(*) as count'))
            ->groupBy('module')
            ->orderBy('count', 'desc')
            ->get();

        // Activity by day (last 30 days)
        $activityByDay = DB::table('activity_logs')
            ->where('user_id', $id)
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'recent_activities' => $recentActivities,
            'by_module' => $activityByModule,
            'by_day' => $activityByDay,
        ]);
    }

    /**
     * Bulk delete users.
     */
    public function bulkDelete(Request $request)
    {
        Gate::authorize(Permission::DELETE_USERS->value);

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $userIds = $request->user_ids;
        $currentUserId = auth()->id();
        $isSuperAdmin = auth()->user()->isSuperAdmin();

        // Filter out users that cannot be deleted
        $usersToDelete = User::whereIn('id', $userIds)
            ->where('id', '!=', $currentUserId) // Cannot delete self
            ->when(!$isSuperAdmin, function ($query) {
                // Non-superadmins cannot delete superadmins
                $query->where('user_type', '!=', 'superadmin');
            })
            ->get();

        $deletedCount = 0;
        $errors = [];

        foreach ($usersToDelete as $user) {
            try {
                $user->delete();
                $deletedCount++;
            } catch (\Exception $e) {
                $errors[] = "Failed to delete user {$user->email}: {$e->getMessage()}";
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} user(s) berhasil dihapus",
            'deleted_count' => $deletedCount,
            'errors' => $errors,
        ]);
    }

    /**
     * Bulk update user types.
     */
    public function bulkUpdateType(Request $request)
    {
        Gate::authorize(Permission::UPDATE_USERS->value);

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'user_type' => 'required|in:personal,admin,instansi,gift,superadmin',
        ]);

        $isSuperAdmin = auth()->user()->isSuperAdmin();

        // Only superadmin can promote to superadmin
        if ($request->user_type === 'superadmin' && !$isSuperAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Only superadmin can promote users to superadmin',
            ], 403);
        }

        $users = User::whereIn('id', $request->user_ids)
            ->when(!$isSuperAdmin, function ($query) {
                // Non-superadmins cannot modify superadmins
                $query->where('user_type', '!=', 'superadmin');
            })
            ->get();

        $updatedCount = 0;
        $errors = [];

        foreach ($users as $user) {
            try {
                $user->setUserType($request->user_type);
                $user->save();
                $updatedCount++;
            } catch (\Exception $e) {
                $errors[] = "Failed to update user {$user->email}: {$e->getMessage()}";
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$updatedCount} user(s) berhasil diupdate",
            'updated_count' => $updatedCount,
            'errors' => $errors,
        ]);
    }

    /**
     * Export users to CSV.
     */
    public function export(Request $request)
    {
        Gate::authorize(Permission::VIEW_USERS->value);

        $query = User::with('customer');

        // Apply filters
        if ($request->filled('type')) {
            $query->where('user_type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get();

        $filename = 'users_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
                'ID',
                'Name',
                'Email',
                'User Type',
                'Phone',
                'Country',
                'City',
                'Full Name',
                'Birth Date',
                'Gender',
                'Blood Type',
                'Created At',
            ]);

            // Data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->user_type,
                    $user->notelp,
                    $user->negara,
                    $user->kota,
                    $user->customer?->nama_lengkap ?? '',
                    $user->customer?->tanggal_lahir?->format('Y-m-d') ?? '',
                    $user->customer?->jenis_kelamin ?? '',
                    $user->customer?->golongan_darah ?? '',
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
