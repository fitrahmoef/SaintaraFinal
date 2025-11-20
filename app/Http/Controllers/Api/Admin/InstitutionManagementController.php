<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminInstansi;
use App\Models\User;
use App\Models\Customer;
use App\Models\TestResult;
use App\Models\TokenUsage;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class InstitutionManagementController extends Controller
{
    /**
     * Get all institutions with pagination and filters
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        $status = $request->get('status');

        $query = AdminInstansi::with(['user', 'employees'])
            ->when($search, function ($q) use ($search) {
                $q->search($search);
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status_akun', $status);
            });

        $institutions = $query->latest()->paginate($perPage);

        // Add statistics for each institution
        $institutions->getCollection()->transform(function ($institution) {
            $institution->stats = $institution->getStatistics();
            return $institution;
        });

        return response()->json($institutions);
    }

    /**
     * Get institution statistics
     */
    public function getStats()
    {
        $totalInstitutions = AdminInstansi::count();
        $activeInstitutions = AdminInstansi::active()->count();
        $pendingInstitutions = AdminInstansi::pending()->count();
        $inactiveInstitutions = AdminInstansi::inactive()->count();

        // Get total employees across all institutions
        $totalEmployees = User::whereNotNull('parent_instansi_id')->count();

        // Get expiring soon (within 30 days)
        $expiringSoon = AdminInstansi::active()
            ->whereNotNull('tanggal_berakhir')
            ->where('tanggal_berakhir', '<=', now()->addDays(30))
            ->where('tanggal_berakhir', '>=', now())
            ->count();

        return response()->json([
            'total_institutions' => $totalInstitutions,
            'active_institutions' => $activeInstitutions,
            'pending_institutions' => $pendingInstitutions,
            'inactive_institutions' => $inactiveInstitutions,
            'total_employees' => $totalEmployees,
            'expiring_soon' => $expiringSoon,
        ]);
    }

    /**
     * Get single institution detail with full statistics
     */
    public function show($id)
    {
        $institution = AdminInstansi::with([
            'user.customer',
            'employees' => function ($query) {
                $query->with('customer');
            },
            'tokenPurchases',
            'transactions'
        ])->findOrFail($id);

        // Get detailed statistics
        $stats = $institution->getStatistics();

        // Get recent activities
        $employeeIds = $institution->employees()->pluck('id')->toArray();
        $allUserIds = array_merge([$institution->user_id], $employeeIds);

        $recentTests = TestResult::whereIn('user_id', $allUserIds)
            ->with(['user', 'test'])
            ->latest()
            ->limit(10)
            ->get();

        $recentTransactions = Transaction::whereIn('user_id', $allUserIds)
            ->with('user')
            ->latest()
            ->limit(10)
            ->get();

        // Get monthly test completion trend (last 6 months)
        $monthlyTests = TestResult::whereIn('user_id', $allUserIds)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Get character type distribution
        $characterDistribution = TestResult::whereIn('user_id', $allUserIds)
            ->selectRaw('character_type_id, COUNT(*) as count')
            ->groupBy('character_type_id')
            ->with('characterType')
            ->get();

        return response()->json([
            'institution' => $institution,
            'statistics' => $stats,
            'recent_tests' => $recentTests,
            'recent_transactions' => $recentTransactions,
            'monthly_test_trend' => $monthlyTests,
            'character_distribution' => $characterDistribution,
        ]);
    }

    /**
     * Create new institution
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'nama_admin' => 'required|string|max:255',
            'nama_instansi' => 'required|string|max:255',
            'nomor_telepon' => 'nullable|string|max:20',
            'email_instansi' => 'nullable|email',
            'alamat_instansi' => 'nullable|string',
            'kota_instansi' => 'nullable|string|max:100',
            'provinsi_instansi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'status_akun' => 'nullable|in:aktif,tidak_aktif,pending',
            'tanggal_berakhir' => 'nullable|date|after:today',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create user account
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->setUserType('instansi');
            $user->save();

            // Create customer profile with initial free tokens
            $customer = new Customer();
            $customer->user_id = $user->id;
            $customer->nama = $request->nama_admin;
            $customer->email = $request->email;
            $customer->saldo_token = 5; // Initial free tokens
            $customer->save();

            // Create institution profile
            $institution = AdminInstansi::create([
                'user_id' => $user->id,
                'nama_admin' => $request->nama_admin,
                'nama_instansi' => $request->nama_instansi,
                'nomor_telepon' => $request->nomor_telepon,
                'email_instansi' => $request->email_instansi ?? $request->email,
                'alamat_instansi' => $request->alamat_instansi,
                'kota_instansi' => $request->kota_instansi,
                'provinsi_instansi' => $request->provinsi_instansi,
                'kode_pos' => $request->kode_pos,
                'status_akun' => $request->status_akun ?? 'pending',
                'tanggal_bergabung' => now(),
                'tanggal_berakhir' => $request->tanggal_berakhir,
                'catatan' => $request->catatan,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Institution created successfully',
                'institution' => $institution->load('user'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create institution',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update institution
     */
    public function update(Request $request, $id)
    {
        $institution = AdminInstansi::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama_admin' => 'sometimes|string|max:255',
            'nama_instansi' => 'sometimes|string|max:255',
            'nomor_telepon' => 'nullable|string|max:20',
            'email_instansi' => 'nullable|email',
            'alamat_instansi' => 'nullable|string',
            'kota_instansi' => 'nullable|string|max:100',
            'provinsi_instansi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'status_akun' => 'sometimes|in:aktif,tidak_aktif,pending',
            'tanggal_berakhir' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $institution->update($request->all());

            return response()->json([
                'message' => 'Institution updated successfully',
                'institution' => $institution->load('user'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update institution',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete institution
     */
    public function destroy($id)
    {
        $institution = AdminInstansi::findOrFail($id);

        try {
            DB::beginTransaction();

            // Get all employees
            $employeeIds = $institution->employees()->pluck('id');

            // Delete or unlink employees (you can choose to keep or delete)
            if ($employeeIds->isNotEmpty()) {
                // Option 1: Unlink employees (keep users but remove institution link)
                User::whereIn('id', $employeeIds)->update(['parent_instansi_id' => null]);

                // Option 2: Delete employees (uncomment if you want to delete)
                // User::whereIn('id', $employeeIds)->delete();
            }

            // Delete institution profile (soft delete)
            $institution->delete();

            // Optionally deactivate the user account
            $institution->user->update(['status' => 'inactive']);

            DB::commit();

            return response()->json([
                'message' => 'Institution deleted successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete institution',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update institution status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:aktif,tidak_aktif,pending',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $institution = AdminInstansi::findOrFail($id);
        $institution->update(['status_akun' => $request->status]);

        return response()->json([
            'message' => 'Institution status updated successfully',
            'institution' => $institution,
        ]);
    }

    /**
     * Extend institution expiry
     */
    public function extendExpiry(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'days' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $institution = AdminInstansi::findOrFail($id);
        $institution->extendExpiry($request->days);

        return response()->json([
            'message' => 'Institution expiry extended successfully',
            'institution' => $institution,
        ]);
    }

    /**
     * Get institution employees
     */
    public function getEmployees($id)
    {
        $institution = AdminInstansi::findOrFail($id);

        $employees = $institution->employees()
            ->with(['customer', 'testResults'])
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'notelp' => $employee->notelp,
                    'status' => $employee->status,
                    'token_balance' => $employee->customer->saldo_token ?? 0,
                    'tests_completed' => $employee->testResults()->count(),
                    'created_at' => $employee->created_at,
                ];
            });

        return response()->json([
            'employees' => $employees,
            'total' => $employees->count(),
        ]);
    }

    /**
     * Export institutions to CSV
     */
    public function export(Request $request)
    {
        $status = $request->get('status');

        $query = AdminInstansi::with('user')
            ->when($status, function ($q) use ($status) {
                $q->where('status_akun', $status);
            });

        $institutions = $query->get();

        $csvData = [];
        $csvData[] = [
            'ID',
            'Nama Instansi',
            'Nama Admin',
            'Email',
            'Nomor Telepon',
            'Kota',
            'Provinsi',
            'Status',
            'Tanggal Bergabung',
            'Tanggal Berakhir',
            'Jumlah Karyawan',
        ];

        foreach ($institutions as $institution) {
            $csvData[] = [
                $institution->id,
                $institution->nama_instansi,
                $institution->nama_admin,
                $institution->email_instansi,
                $institution->nomor_telepon,
                $institution->kota_instansi,
                $institution->provinsi_instansi,
                $institution->status_akun,
                $institution->tanggal_bergabung?->format('Y-m-d'),
                $institution->tanggal_berakhir?->format('Y-m-d'),
                $institution->employees()->count(),
            ];
        }

        $filename = 'institutions_' . date('Y-m-d_His') . '.csv';
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
