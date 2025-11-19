<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\CharacterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuestionManagementController extends Controller
{
    /**
     * Get all questions for a test
     */
    public function index($testId)
    {
        $test = Test::findOrFail($testId);
        $questions = TestQuestion::where('test_id', $testId)
            ->orderBy('nomor_soal')
            ->get();

        return response()->json([
            'test' => $test,
            'questions' => $questions,
            'character_types' => CharacterType::all(),
        ]);
    }

    /**
     * Get single question
     */
    public function show($id)
    {
        $question = TestQuestion::with('test')->findOrFail($id);

        return response()->json($question);
    }

    /**
     * Create new question
     */
    public function store(Request $request, $testId)
    {
        $test = Test::findOrFail($testId);

        $validator = Validator::make($request->all(), [
            'nomor_soal' => 'required|integer|min:1',
            'pertanyaan' => 'required|string',
            'tipe_soal' => 'required|in:pilihan_ganda,skala_likert,essay',
            'pilihan_jawaban' => 'required|array',
            'pilihan_jawaban.*.text' => 'required|string',
            'pilihan_jawaban.*.value' => 'nullable',
            'bobot_karakter' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if question number already exists
        $exists = TestQuestion::where('test_id', $testId)
            ->where('nomor_soal', $request->nomor_soal)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Nomor soal sudah digunakan'
            ], 422);
        }

        $question = TestQuestion::create([
            'test_id' => $testId,
            'nomor_soal' => $request->nomor_soal,
            'pertanyaan' => $request->pertanyaan,
            'tipe_soal' => $request->tipe_soal,
            'pilihan_jawaban' => $request->pilihan_jawaban,
            'bobot_karakter' => $request->bobot_karakter,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'message' => 'Soal berhasil dibuat',
            'question' => $question
        ], 201);
    }

    /**
     * Bulk create questions
     */
    public function bulkStore(Request $request, $testId)
    {
        $test = Test::findOrFail($testId);

        $validator = Validator::make($request->all(), [
            'questions' => 'required|array|min:1',
            'questions.*.nomor_soal' => 'required|integer|min:1',
            'questions.*.pertanyaan' => 'required|string',
            'questions.*.tipe_soal' => 'required|in:pilihan_ganda,skala_likert,essay',
            'questions.*.pilihan_jawaban' => 'required|array',
            'questions.*.bobot_karakter' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $createdQuestions = [];

            foreach ($request->questions as $questionData) {
                $question = TestQuestion::create([
                    'test_id' => $testId,
                    'nomor_soal' => $questionData['nomor_soal'],
                    'pertanyaan' => $questionData['pertanyaan'],
                    'tipe_soal' => $questionData['tipe_soal'],
                    'pilihan_jawaban' => $questionData['pilihan_jawaban'],
                    'bobot_karakter' => $questionData['bobot_karakter'] ?? null,
                    'is_active' => $questionData['is_active'] ?? true,
                ]);

                $createdQuestions[] = $question;
            }

            // Update test's jumlah_soal
            $test->update(['jumlah_soal' => TestQuestion::where('test_id', $testId)->count()]);

            DB::commit();

            return response()->json([
                'message' => count($createdQuestions) . ' soal berhasil dibuat',
                'questions' => $createdQuestions
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal membuat soal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update question
     */
    public function update(Request $request, $id)
    {
        $question = TestQuestion::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nomor_soal' => 'sometimes|required|integer|min:1',
            'pertanyaan' => 'sometimes|required|string',
            'tipe_soal' => 'sometimes|required|in:pilihan_ganda,skala_likert,essay',
            'pilihan_jawaban' => 'sometimes|required|array',
            'bobot_karakter' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if new question number conflicts
        if ($request->has('nomor_soal') && $request->nomor_soal != $question->nomor_soal) {
            $exists = TestQuestion::where('test_id', $question->test_id)
                ->where('nomor_soal', $request->nomor_soal)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'Nomor soal sudah digunakan'
                ], 422);
            }
        }

        $question->update($request->all());

        return response()->json([
            'message' => 'Soal berhasil diperbarui',
            'question' => $question->fresh()
        ]);
    }

    /**
     * Delete question
     */
    public function destroy($id)
    {
        $question = TestQuestion::findOrFail($id);
        $testId = $question->test_id;

        $question->delete();

        // Update test's jumlah_soal
        $test = Test::find($testId);
        if ($test) {
            $test->update(['jumlah_soal' => TestQuestion::where('test_id', $testId)->count()]);
        }

        return response()->json([
            'message' => 'Soal berhasil dihapus'
        ]);
    }

    /**
     * Reorder questions
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'questions' => 'required|array',
            'questions.*.id' => 'required|exists:test_questions,id',
            'questions.*.nomor_soal' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($request->questions as $questionData) {
                TestQuestion::where('id', $questionData['id'])
                    ->update(['nomor_soal' => $questionData['nomor_soal']]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Urutan soal berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal memperbarui urutan soal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
