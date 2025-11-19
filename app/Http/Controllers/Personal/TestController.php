<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\TestSession;
use App\Models\TokenPurchase;
use App\Models\TokenUsage;
use App\Models\Certificate;
use App\Models\Customer;
use App\Services\CharacterAnalysisService;
use App\Services\FreeTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    protected CharacterAnalysisService $characterAnalysis;
    protected FreeTokenService $freeTokenService;

    public function __construct(CharacterAnalysisService $characterAnalysis, FreeTokenService $freeTokenService)
    {
        $this->characterAnalysis = $characterAnalysis;
        $this->freeTokenService = $freeTokenService;
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

        DB::beginTransaction();
        try {
            // SECURITY FIX: Check token availability with row lock to prevent race conditions
            // This ensures only one test can claim tokens at a time
            $availableToken = TokenPurchase::where('customer_id', $customer->id)
                ->active()
                ->where('jumlah_tersisa', '>=', $test->token_required)
                ->lockForUpdate()
                ->first();

            if (!$availableToken) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak mencukupi'
                ], 400);
            }
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

    /**
     * ============================================
     * TEST SESSION MANAGEMENT METHODS
     * ============================================
     */

    /**
     * Start a new test session
     * This locks the token and creates a session to prevent token loss
     */
    public function startSession(Request $request)
    {
        $request->validate([
            'test_id' => 'required|exists:tests,id',
        ]);

        $user = auth()->user();
        $customer = $user->customer;

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found'
            ], 400);
        }

        $test = Test::with('questions')->findOrFail($request->test_id);

        DB::beginTransaction();
        try {
            // Check if user has an active session for this test
            $existingSession = TestSession::where('customer_id', $customer->id)
                ->where('test_id', $test->id)
                ->active()
                ->first();

            if ($existingSession) {
                // Resume existing session
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Resuming existing session',
                    'session' => $this->formatSessionResponse($existingSession, $test),
                    'is_resume' => true,
                ]);
            }

            // PRIORITY 1: Check if user has free tokens
            $usingFreeToken = false;
            $availableToken = null;

            if ($this->freeTokenService->hasFreeTokens($customer) &&
                $this->freeTokenService->getFreeTokenCount($customer) >= $test->token_required) {
                $usingFreeToken = true;
            } else {
                // PRIORITY 2: Check purchased tokens WITH LOCK to prevent race condition
                $availableToken = TokenPurchase::where('customer_id', $customer->id)
                    ->active()
                    ->where('jumlah_tersisa', '>=', $test->token_required)
                    ->lockForUpdate() // CRITICAL: Prevent race condition
                    ->first();

                if (!$availableToken) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Token tidak mencukupi'
                    ], 400);
                }
            }

            // Create new test session
            $sessionToken = TestSession::generateSessionToken();
            $waktuMulai = now();
            $waktuExpired = $waktuMulai->copy()->addMinutes($test->durasi_menit);

            $session = TestSession::create([
                'customer_id' => $customer->id,
                'test_id' => $test->id,
                'token_purchase_id' => $availableToken?->id, // Null if using free token
                'session_token' => $sessionToken,
                'status' => 'in_progress',
                'jawaban' => [],
                'current_question' => 0,
                'waktu_mulai' => $waktuMulai,
                'waktu_expired' => $waktuExpired,
                'token_locked' => true,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => ['using_free_token' => $usingFreeToken], // Track if using free token
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Test session started successfully',
                'session' => $this->formatSessionResponse($session, $test),
                'is_resume' => false,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to start session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save progress during test (auto-save)
     */
    public function saveProgress(Request $request)
    {
        $request->validate([
            'session_token' => 'required|exists:test_sessions,session_token',
            'current_question' => 'required|integer|min:0',
            'jawaban' => 'required|array',
        ]);

        $customer = auth()->user()->customer;
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found'
            ], 400);
        }

        $session = TestSession::where('session_token', $request->session_token)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found'
            ], 404);
        }

        if (!$session->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Session is no longer active',
                'is_expired' => true,
            ], 400);
        }

        $session->updateProgress($request->current_question, $request->jawaban);

        return response()->json([
            'success' => true,
            'message' => 'Progress saved',
            'remaining_time' => $session->remaining_time,
        ]);
    }

    /**
     * Submit test and finalize result
     */
    public function submitSession(Request $request)
    {
        $request->validate([
            'session_token' => 'required|exists:test_sessions,session_token',
            'jawaban' => 'required|array',
        ]);

        $customer = auth()->user()->customer;
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found'
            ], 400);
        }

        $session = TestSession::where('session_token', $request->session_token)
            ->where('customer_id', $customer->id)
            ->with(['test', 'tokenPurchase'])
            ->lockForUpdate() // CRITICAL: Prevent duplicate submission
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found'
            ], 404);
        }

        if ($session->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Test already submitted'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Validate customer data for character analysis
            if (!$customer->nama_lengkap || !$customer->tanggal_lahir || !$customer->golongan_darah) {
                throw new \Exception('Data profil tidak lengkap. Harap lengkapi nama, tanggal lahir, dan golongan darah.');
            }

            // Perform character analysis
            $characterAnalysis = $this->characterAnalysis->analyze(
                $customer->nama_lengkap,
                $customer->tanggal_lahir,
                $customer->golongan_darah
            );

            // Calculate score
            $skor = $this->calculateScore($request->jawaban, $characterAnalysis);

            // Create test result
            $testResult = TestResult::create([
                'test_id' => $session->test_id,
                'customer_id' => $customer->id,
                'token_purchase_id' => $session->token_purchase_id,
                'hasil_karakter' => $characterAnalysis['character_name'],
                'deskripsi_hasil' => $characterAnalysis['description'],
                'skor' => $skor,
                'jawaban' => $request->jawaban,
                'analisis' => $characterAnalysis,
                'tanggal_tes' => now(),
                'waktu_mulai' => $session->waktu_mulai,
                'waktu_selesai' => now(),
                'ip_address' => $request->ip(),
            ]);

            // Deduct tokens based on session type
            $sessionMetadata = $session->metadata ?? [];
            $usingFreeToken = $sessionMetadata['using_free_token'] ?? false;

            if ($usingFreeToken) {
                // Use free tokens
                $this->freeTokenService->useFreeTokens($customer, $session->test->token_required);
            } else if ($session->token_purchase_id) {
                // SECURITY FIX: Lock token purchase to prevent race condition
                // Reload with lockForUpdate to ensure atomic increment
                $tokenPurchase = TokenPurchase::where('id', $session->token_purchase_id)
                    ->lockForUpdate()
                    ->first();

                if ($tokenPurchase) {
                    TokenUsage::create([
                        'token_purchase_id' => $tokenPurchase->id,
                        'test_result_id' => $testResult->id,
                        'jumlah_digunakan' => $session->test->token_required,
                        'keterangan' => 'Token digunakan untuk tes: ' . $session->test->nama_tes,
                        'tanggal_penggunaan' => now(),
                    ]);

                    // Update token purchase with atomic increment
                    $tokenPurchase->increment('jumlah_terpakai', $session->test->token_required);
                }
            }

            // Generate certificate
            $certificate = Certificate::create([
                'test_result_id' => $testResult->id,
                'nomor_sertifikat' => Certificate::generateNomorSertifikat(),
                'diterbitkan_oleh' => 'Saintara',
                'format_file' => 'pdf',
                'is_active' => true,
                'tanggal_terbit' => now(),
            ]);

            // Mark session as completed and unlock token
            $session->markAsCompleted();

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

    /**
     * Get current session status
     */
    public function getSession(Request $request)
    {
        $request->validate([
            'session_token' => 'required|exists:test_sessions,session_token',
        ]);

        $customer = auth()->user()->customer;
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found'
            ], 400);
        }

        $session = TestSession::where('session_token', $request->session_token)
            ->where('customer_id', $customer->id)
            ->with('test.questions')
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'session' => $this->formatSessionResponse($session, $session->test),
        ]);
    }

    /**
     * Abandon session (user leaves without completing)
     */
    public function abandonSession(Request $request)
    {
        $request->validate([
            'session_token' => 'required|exists:test_sessions,session_token',
        ]);

        $customer = auth()->user()->customer;
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found'
            ], 400);
        }

        $session = TestSession::where('session_token', $request->session_token)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found'
            ], 404);
        }

        $session->markAsAbandoned();

        return response()->json([
            'success' => true,
            'message' => 'Session abandoned',
        ]);
    }

    /**
     * Format session response for frontend
     */
    private function formatSessionResponse(TestSession $session, Test $test): array
    {
        return [
            'session_token' => $session->session_token,
            'status' => $session->status,
            'current_question' => $session->current_question,
            'jawaban' => $session->jawaban,
            'waktu_mulai' => $session->waktu_mulai->toIso8601String(),
            'waktu_expired' => $session->waktu_expired?->toIso8601String(),
            'remaining_time' => $session->remaining_time,
            'progress_percentage' => $session->progress_percentage,
            'test' => [
                'id' => $test->id,
                'nama' => $test->nama_tes,
                'deskripsi' => $test->deskripsi_tes,
                'jenis' => $test->jenis_tes,
                'jumlah_soal' => $test->jumlah_soal,
                'durasi' => $test->durasi_menit,
                'token_required' => $test->token_required,
                'questions' => $test->questions->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'nomor_soal' => $question->nomor_soal,
                        'pertanyaan' => $question->pertanyaan,
                        'tipe_soal' => $question->tipe_soal,
                        'pilihan_jawaban' => $question->pilihan_jawaban,
                    ];
                })->toArray(),
            ],
        ];
    }
}
