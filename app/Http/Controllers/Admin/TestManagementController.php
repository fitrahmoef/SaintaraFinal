<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TestManagementController extends Controller
{
    /**
     * Get all tests
     */
    public function index(Request $request)
    {
        $query = Test::withCount(['testResults', 'questions'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        // Filter by type
        if ($request->has('jenis_tes') && $request->jenis_tes !== 'all') {
            $query->where('jenis_tes', $request->jenis_tes);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('nama_tes', 'like', "%{$request->search}%");
        }

        if ($request->has('paginate') && $request->paginate === 'false') {
            $tests = $query->get();
        } else {
            $tests = $query->paginate($request->per_page ?? 15);
        }

        return response()->json($tests);
    }

    /**
     * Get single test with questions
     */
    public function show($id)
    {
        $test = Test::withCount(['testResults', 'questions'])
            ->with(['questions' => function ($query) {
                $query->orderBy('nomor_soal');
            }])
            ->findOrFail($id);

        return response()->json($test);
    }

    /**
     * Create new test
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_tes' => 'required|string|max:255',
            'deskripsi_tes' => 'nullable|string',
            'jenis_tes' => 'required|in:karakter,kompetensi,kepribadian',
            'jumlah_soal' => 'required|integer|min:1',
            'durasi_menit' => 'required|integer|min:1',
            'token_required' => 'required|integer|min:0',
            'metadata' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $test = Test::create($request->all());

        return response()->json([
            'message' => 'Tes berhasil dibuat',
            'test' => $test
        ], 201);
    }

    /**
     * Update test
     */
    public function update(Request $request, $id)
    {
        $test = Test::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama_tes' => 'sometimes|required|string|max:255',
            'deskripsi_tes' => 'nullable|string',
            'jenis_tes' => 'sometimes|required|in:karakter,kompetensi,kepribadian',
            'jumlah_soal' => 'sometimes|required|integer|min:1',
            'durasi_menit' => 'sometimes|required|integer|min:1',
            'token_required' => 'sometimes|required|integer|min:0',
            'metadata' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $test->update($request->all());

        return response()->json([
            'message' => 'Tes berhasil diperbarui',
            'test' => $test->fresh()
        ]);
    }

    /**
     * Delete test
     */
    public function destroy($id)
    {
        $test = Test::findOrFail($id);

        // Check if test has been taken
        if ($test->testResults()->count() > 0) {
            return response()->json([
                'message' => 'Tidak dapat menghapus tes yang sudah pernah diambil'
            ], 400);
        }

        $test->delete();

        return response()->json([
            'message' => 'Tes berhasil dihapus'
        ]);
    }

    /**
     * Toggle test active status
     */
    public function toggleStatus($id)
    {
        $test = Test::findOrFail($id);
        $test->update(['is_active' => !$test->is_active]);

        return response()->json([
            'message' => 'Status tes berhasil diubah',
            'test' => $test
        ]);
    }

    /**
     * Duplicate test with all questions
     */
    public function duplicate($id)
    {
        $originalTest = Test::with('questions')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Create new test
            $newTest = Test::create([
                'nama_tes' => $originalTest->nama_tes . ' (Copy)',
                'deskripsi_tes' => $originalTest->deskripsi_tes,
                'jenis_tes' => $originalTest->jenis_tes,
                'jumlah_soal' => $originalTest->jumlah_soal,
                'durasi_menit' => $originalTest->durasi_menit,
                'token_required' => $originalTest->token_required,
                'metadata' => $originalTest->metadata,
                'is_active' => false, // Set inactive by default
            ]);

            // Duplicate all questions
            foreach ($originalTest->questions as $question) {
                TestQuestion::create([
                    'test_id' => $newTest->id,
                    'nomor_soal' => $question->nomor_soal,
                    'pertanyaan' => $question->pertanyaan,
                    'tipe_soal' => $question->tipe_soal,
                    'pilihan_jawaban' => $question->pilihan_jawaban,
                    'bobot_karakter' => $question->bobot_karakter,
                    'is_active' => $question->is_active,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Tes berhasil diduplikasi',
                'test' => $newTest->fresh(['questions'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menduplikasi tes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
