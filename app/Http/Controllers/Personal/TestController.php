<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\TokenPurchase;
use App\Models\TokenUsage;
use App\Models\Certificate;
use App\Models\Customer;
use App\Services\CharacterAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    protected CharacterAnalysisService $characterAnalysis;

    public function __construct(CharacterAnalysisService $characterAnalysis)
    {
        $this->characterAnalysis = $characterAnalysis;
    }

    public function index()
    {
        $tests = Test::active()
            ->get()
            ->map(function ($test) {
                return [
                    'id' => $test->id,
                    'nama' => $test->nama_tes,
                    'deskripsi' => $test->deskripsi_tes,
                    'jenis' => $test->jenis_tes,
                    'jumlah_soal' => $test->jumlah_soal,
                    'durasi' => $test->durasi_menit . ' menit',
                    'token_required' => $test->token_required,
                ];
            });

        return response()->json(['tests' => $tests]);
    }

    public function show($id)
    {
        $test = Test::with('questions')->findOrFail($id);

        return response()->json([
            'test' => [
                'id' => $test->id,
                'nama' => $test->nama_tes,
                'deskripsi' => $test->deskripsi_tes,
                'jenis' => $test->jenis_tes,
                'jumlah_soal' => $test->jumlah_soal,
                'durasi' => $test->durasi_menit,
                'token_required' => $test->token_required,
                'metadata' => $test->metadata,
                'questions' => $test->questions->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'nomor_soal' => $question->nomor_soal,
                        'pertanyaan' => $question->pertanyaan,
                        'tipe_soal' => $question->tipe_soal,
                        'pilihan_jawaban' => $question->pilihan_jawaban,
                        // Don't send bobot_karakter to frontend for security
                    ];
                }),
            ]
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'test_id' => 'required|exists:tests,id',
            'jawaban' => 'required|array',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date',
        ]);

        $user = auth()->user();
        $customer = $user->customer;

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found'
            ], 400);
        }

        $test = Test::findOrFail($request->test_id);

        // Check if user has enough tokens
        $availableToken = TokenPurchase::where('customer_id', $customer->id)
            ->active()
            ->where('jumlah_tersisa', '>=', $test->token_required)
            ->first();

        if (!$availableToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak mencukupi'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Validate customer data for character analysis
            if (!$customer->nama_lengkap || !$customer->tanggal_lahir || !$customer->golongan_darah) {
                throw new \Exception('Data profil tidak lengkap. Harap lengkapi nama, tanggal lahir, dan golongan darah.');
            }

            // Perform character analysis using real algorithm
            $characterAnalysis = $this->characterAnalysis->analyze(
                $customer->nama_lengkap,
                $customer->tanggal_lahir,
                $customer->golongan_darah
            );

            // Calculate score based on answers
            $skor = $this->calculateScore($request->jawaban, $characterAnalysis);

            // Create test result
            $testResult = TestResult::create([
                'test_id' => $test->id,
                'customer_id' => $customer->id,
                'token_purchase_id' => $availableToken->id,
                'hasil_karakter' => $characterAnalysis['character_name'],
                'deskripsi_hasil' => $characterAnalysis['description'],
                'skor' => $skor,
                'jawaban' => $request->jawaban,
                'analisis' => $characterAnalysis,
                'tanggal_tes' => now(),
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'ip_address' => $request->ip(),
            ]);

            // Record token usage
            TokenUsage::create([
                'token_purchase_id' => $availableToken->id,
                'test_result_id' => $testResult->id,
                'jumlah_digunakan' => $test->token_required,
                'keterangan' => 'Token digunakan untuk tes: ' . $test->nama_tes,
                'tanggal_penggunaan' => now(),
            ]);

            // Update token purchase
            $availableToken->increment('jumlah_terpakai', $test->token_required);

            // Generate certificate
            $certificate = Certificate::create([
                'test_result_id' => $testResult->id,
                'nomor_sertifikat' => Certificate::generateNomorSertifikat(),
                'diterbitkan_oleh' => 'Saintara',
                'format_file' => 'pdf',
                'is_active' => true,
                'tanggal_terbit' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Test berhasil diselesaikan',
                'result' => [
                    'id' => $testResult->id,
                    'karakter' => $testResult->hasil_karakter,
                    'deskripsi' => $testResult->deskripsi_hasil,
                    'skor' => $testResult->skor,
                    'certificate_number' => $certificate->nomor_sertifikat,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan hasil tes: ' . $e->getMessage()
            ], 500);
        }
    }

    public function results()
    {
        $customer = auth()->user()->customer;

        if (!$customer) {
            return response()->json(['results' => []]);
        }

        $results = TestResult::where('customer_id', $customer->id)
            ->with(['test', 'certificate'])
            ->latest('tanggal_tes')
            ->get()
            ->map(function ($result) {
                return [
                    'id' => $result->id,
                    'test_name' => $result->test->nama_tes ?? 'N/A',
                    'karakter' => $result->hasil_karakter,
                    'deskripsi' => $result->deskripsi_hasil,
                    'skor' => $result->skor,
                    'tanggal' => $result->tanggal_tes->format('d M Y'),
                    'durasi' => $result->getDurationInMinutes() . ' menit',
                    'certificate' => $result->certificate ? [
                        'nomor' => $result->certificate->nomor_sertifikat,
                        'url' => route('personal.certificates.download', $result->certificate->id),
                    ] : null,
                ];
            });

        return response()->json(['results' => $results]);
    }

    public function resultDetail($id)
    {
        $customer = auth()->user()->customer;

        if (!$customer) {
            return response()->json(['message' => 'Customer profile not found'], 404);
        }

        $result = TestResult::where('customer_id', $customer->id)
            ->with(['test', 'certificate'])
            ->findOrFail($id);

        return response()->json([
            'result' => [
                'id' => $result->id,
                'test' => [
                    'nama' => $result->test->nama_tes,
                    'jenis' => $result->test->jenis_tes,
                ],
                'karakter' => $result->hasil_karakter,
                'deskripsi' => $result->deskripsi_hasil,
                'skor' => $result->skor,
                'analisis' => $result->analisis,
                'tanggal' => $result->tanggal_tes->format('d M Y H:i'),
                'durasi' => $result->getDurationInMinutes() . ' menit',
                'certificate' => $result->certificate ? [
                    'nomor' => $result->certificate->nomor_sertifikat,
                    'tanggal_terbit' => $result->certificate->tanggal_terbit->format('d M Y'),
                    'url' => route('personal.certificates.download', $result->certificate->id),
                ] : null,
            ]
        ]);
    }

    /**
     * Calculate score based on answers and character analysis
     * This can be enhanced based on answer weights
     */
    private function calculateScore(array $jawaban, array $characterAnalysis): int
    {
        // Base score from number of answers
        $baseScore = count($jawaban) * 5;

        // Bonus from detailed scores
        $detailedScores = $characterAnalysis['detailed_scores'] ?? [];
        $averageDetailedScore = !empty($detailedScores)
            ? array_sum($detailedScores) / count($detailedScores)
            : 50;

        // Combine scores (max 100)
        $finalScore = min(100, ($baseScore + $averageDetailedScore) / 2);

        return (int) round($finalScore);
    }
}
