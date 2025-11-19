<?php

namespace App\Http\Controllers\Instansi;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use App\Models\TestResult;
// use App\Models\Token; // DEPRECATED: Use TokenPurchase instead
use App\Models\TokenPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use League\Csv\Reader;
use League\Csv\Writer;

class InstansiDashboardController extends Controller
{
    /**
     * Get dashboard statistics for institution
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get institution's customer record
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return response()->json([
                'message' => 'Customer record not found'
            ], 404);
        }

        $stats = [
            // Total employees (sub-accounts under this institution)
            'total_employees' => User::where('parent_instansi_id', $user->id)->count(),

            // Active tokens (FIXED: Use TokenPurchase with correct fields)
            'active_tokens' => TokenPurchase::where('customer_id', $customer->id)
                ->where('status', 'aktif')
                ->where(function ($query) {
                    $query->whereNull('tanggal_kadaluarsa')
                        ->orWhere('tanggal_kadaluarsa', '>', now());
                })
                ->sum(DB::raw('jumlah_token - jumlah_terpakai')),

            // Used tokens (FIXED: Calculate from TokenPurchase)
            'used_tokens' => TokenPurchase::where('customer_id', $customer->id)
                ->sum('jumlah_terpakai'),

            // Completed tests
            'completed_tests' => TestResult::whereHas('customer.user', function ($query) use ($user) {
                $query->where('parent_instansi_id', $user->id)
                    ->orWhere('id', $user->id);
            })->count(),

            // Tests this month
            'tests_this_month' => TestResult::whereHas('customer.user', function ($query) use ($user) {
                $query->where('parent_instansi_id', $user->id)
                    ->orWhere('id', $user->id);
            })
            ->whereMonth('tanggal_tes', now()->month)
            ->whereYear('tanggal_tes', now()->year)
            ->count(),

            // Recent test results
            'recent_results' => TestResult::with(['test', 'customer.user'])
                ->whereHas('customer.user', function ($query) use ($user) {
                    $query->where('parent_instansi_id', $user->id)
                        ->orWhere('id', $user->id);
                })
                ->orderBy('tanggal_tes', 'desc')
                ->limit(5)
                ->get(),

            // Character type distribution (FIXED: Use hasil_karakter instead of non-existent tipe_karakter_dominan)
            'character_distribution' => TestResult::select('hasil_karakter', DB::raw('count(*) as total'))
                ->whereHas('customer.user', function ($query) use ($user) {
                    $query->where('parent_instansi_id', $user->id)
                        ->orWhere('id', $user->id);
                })
                ->whereNotNull('hasil_karakter')
                ->groupBy('hasil_karakter')
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Get all employees under this institution
     */
    public function employees(Request $request)
    {
        $user = Auth::user();

        $query = User::with(['customer.tokens', 'customer.testResults'])
            ->where('parent_instansi_id', $user->id)
            ->orderBy('created_at', 'desc');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nomor_telepon', 'like', "%{$search}%");
            });
        }

        $employees = $query->paginate($request->per_page ?? 15);

        return response()->json($employees);
    }

    /**
     * Get test results for institution
     */
    public function testResults(Request $request)
    {
        $user = Auth::user();

        $query = TestResult::with(['test', 'customer.user', 'certificate'])
            ->whereHas('customer.user', function ($q) use ($user) {
                $q->where('parent_instansi_id', $user->id)
                    ->orWhere('id', $user->id);
            })
            ->orderBy('tanggal_tes', 'desc');

        // Filter by test
        if ($request->has('test_id') && $request->test_id) {
            $query->where('test_id', $request->test_id);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('tanggal_tes', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('tanggal_tes', '<=', $request->end_date);
        }

        // Search by employee name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('customer.user', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%");
            });
        }

        $results = $query->paginate($request->per_page ?? 15);

        return response()->json($results);
    }

    /**
     * Bulk upload employees from CSV
     */
    public function bulkUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return response()->json([
                'message' => 'Customer record not found'
            ], 404);
        }

        try {
            $file = $request->file('file');
            $csv = Reader::createFromPath($file->getRealPath(), 'r');
            $csv->setHeaderOffset(0); // First row is header

            $records = $csv->getRecords();
            $createdEmployees = [];
            $errors = [];
            $rowNumber = 1; // Start from 1 (after header)

            DB::beginTransaction();

            foreach ($records as $record) {
                $rowNumber++;

                try {
                    // Validate required fields
                    if (empty($record['nama_lengkap']) || empty($record['email'])) {
                        $errors[] = "Baris {$rowNumber}: Nama lengkap dan email wajib diisi";
                        continue;
                    }

                    // Check if email already exists
                    if (User::where('email', $record['email'])->exists()) {
                        $errors[] = "Baris {$rowNumber}: Email {$record['email']} sudah terdaftar";
                        continue;
                    }

                    // Create user account for employee
                    // SECURITY: Use Str::random() for cryptographically secure password generation
                    $password = $record['password'] ?? Str::random(16);

                    $employee = User::create([
                        'name' => $record['nama_lengkap'],
                        'email' => $record['email'],
                        'password' => Hash::make($password),
                        'notelp' => $record['nomor_telepon'] ?? null,
                        // Note: tanggal_lahir, jenis_kelamin, alamat not in User model
                    ]);

                    // SECURITY: Set user_type via protected method
                    $employee->setUserType('personal');
                    $employee->save();

                    // Create customer record for employee
                    $employeeCustomer = Customer::create([
                        'user_id' => $employee->id,
                        'nama_lengkap' => $record['nama_lengkap'],
                    ]);

                    $createdEmployees[] = [
                        'user' => $employee,
                        'generated_password' => $password,
                    ];
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'message' => count($createdEmployees) . ' karyawan berhasil ditambahkan',
                'created' => count($createdEmployees),
                'errors' => $errors,
                'employees' => $createdEmployees,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal mengupload file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download CSV template for bulk upload
     */
    public function downloadTemplate()
    {
        $csv = Writer::createFromString('');

        // Add header
        $csv->insertOne([
            'nama_lengkap',
            'email',
            'nomor_telepon',
            'tanggal_lahir',
            'jenis_kelamin',
            'alamat',
            'password'
        ]);

        // Add example row
        $csv->insertOne([
            'John Doe',
            'john.doe@example.com',
            '081234567890',
            '1990-01-01',
            'L',
            'Jl. Contoh No. 123',
            'password123'
        ]);

        $filename = 'template_upload_karyawan.csv';

        return response($csv->toString(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
