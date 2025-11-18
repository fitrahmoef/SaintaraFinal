<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\TokenPurchase;
use App\Models\TokenUsage;
use App\Models\Certificate;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
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
        $test = Test::findOrFail($id);

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
            // Calculate result (simplified version)
            $skor = $this->calculateScore($request->jawaban);
            $hasil_karakter = $this->determineCharacter($skor);

            // Create test result
            $testResult = TestResult::create([
                'test_id' => $test->id,
                'customer_id' => $customer->id,
                'token_purchase_id' => $availableToken->id,
                'hasil_karakter' => $hasil_karakter,
                'deskripsi_hasil' => $this->getCharacterDescription($hasil_karakter),
                'skor' => $skor,
                'jawaban' => $request->jawaban,
                'analisis' => $this->generateAnalysis($request->jawaban, $skor),
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

    private function calculateScore(array $jawaban): int
    {
        // Simplified scoring logic
        return count($jawaban) * 10;
    }

    private function determineCharacter(int $skor): string
    {
        // Simplified character determination
        if ($skor >= 80) {
            return 'INTJ - The Architect';
        } elseif ($skor >= 60) {
            return 'ENFP - The Campaigner';
        } elseif ($skor >= 40) {
            return 'ISTJ - The Logistician';
        } else {
            return 'ESFP - The Entertainer';
        }
    }

    private function getCharacterDescription(string $karakter): string
    {
        $descriptions = [
            'INTJ - The Architect' => 'Strategic thinkers who plan and organize everything meticulously.',
            'ENFP - The Campaigner' => 'Enthusiastic, creative, and sociable free spirits.',
            'ISTJ - The Logistician' => 'Practical and fact-minded individuals, whose reliability cannot be doubted.',
            'ESFP - The Entertainer' => 'Spontaneous, energetic, and enthusiastic people.',
        ];

        return $descriptions[$karakter] ?? 'Karakter unik dengan potensi luar biasa.';
    }

    private function generateAnalysis(array $jawaban, int $skor): array
    {
        return [
            'total_jawaban' => count($jawaban),
            'skor_total' => $skor,
            'kategori' => $skor >= 60 ? 'Tinggi' : ($skor >= 40 ? 'Sedang' : 'Rendah'),
            'rekomendasi' => 'Terus kembangkan potensi diri Anda melalui pembelajaran dan pengalaman.',
        ];
    }
}
