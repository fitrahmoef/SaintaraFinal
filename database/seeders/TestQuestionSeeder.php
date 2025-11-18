<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Test;
use App\Models\TestQuestion;

class TestQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get test IDs
        $testDasar = Test::where('nama_tes', 'LIKE', '%Dasar%')->first();
        $testStandar = Test::where('nama_tes', 'LIKE', '%Standar%')->first();
        $testPremium = Test::where('nama_tes', 'LIKE', '%Premium%')->first();

        // Soal-soal untuk Tes Dasar (10 soal)
        if ($testDasar) {
            $questions = [
                [
                    'test_id' => $testDasar->id,
                    'nomor_soal' => 1,
                    'pertanyaan' => 'Bagaimana Anda biasanya mengambil keputusan penting?',
                    'tipe_soal' => 'multiple_choice',
                    'pilihan_jawaban' => [
                        'A' => 'Saya menganalisis data dan fakta dengan cermat',
                        'B' => 'Saya mengikuti intuisi dan perasaan saya',
                        'C' => 'Saya meminta pendapat orang lain terlebih dahulu',
                        'D' => 'Saya membuat daftar pro dan kontra'
                    ],
                    'bobot_karakter' => [
                        'A' => ['analytical' => 3, 'perfectionism' => 2],
                        'B' => ['creativity' => 3, 'adaptability' => 2],
                        'C' => ['empathy' => 3, 'communication' => 2],
                        'D' => ['analytical' => 2, 'perfectionism' => 3]
                    ],
                    'is_active' => true,
                ],
                [
                    'test_id' => $testDasar->id,
                    'nomor_soal' => 2,
                    'pertanyaan' => 'Dalam situasi sosial, Anda lebih suka:',
                    'tipe_soal' => 'multiple_choice',
                    'pilihan_jawaban' => [
                        'A' => 'Berada di pusat perhatian dan memimpin percakapan',
                        'B' => 'Mengobrol dengan beberapa orang yang sudah dikenal',
                        'C' => 'Mendengarkan dan mengamati dari samping',
                        'D' => 'Networking dan mengenal banyak orang baru'
                    ],
                    'bobot_karakter' => [
                        'A' => ['leadership' => 3, 'communication' => 3, 'courage' => 2],
                        'B' => ['empathy' => 2, 'communication' => 2],
                        'C' => ['wisdom' => 3, 'analytical' => 2],
                        'D' => ['communication' => 3, 'adaptability' => 3]
                    ],
                    'is_active' => true,
                ],
                [
                    'test_id' => $testDasar->id,
                    'nomor_soal' => 3,
                    'pertanyaan' => 'Ketika menghadapi masalah baru, pendekatan Anda adalah:',
                    'tipe_soal' => 'multiple_choice',
                    'pilihan_jawaban' => [
                        'A' => 'Mencoba solusi kreatif yang belum pernah dilakukan',
                        'B' => 'Mencari referensi atau panduan yang sudah terbukti',
                        'C' => 'Mendiskusikannya dengan orang yang berpengalaman',
                        'D' => 'Langsung action dan belajar sambil jalan'
                    ],
                    'bobot_karakter' => [
                        'A' => ['creativity' => 3, 'courage' => 2, 'adaptability' => 2],
                        'B' => ['analytical' => 3, 'perfectionism' => 2],
                        'C' => ['empathy' => 2, 'wisdom' => 3, 'communication' => 2],
                        'D' => ['courage' => 3, 'adaptability' => 3, 'motivation' => 2]
                    ],
                    'is_active' => true,
                ],
                [
                    'test_id' => $testDasar->id,
                    'nomor_soal' => 4,
                    'pertanyaan' => 'Apa yang paling memotivasi Anda dalam bekerja?',
                    'tipe_soal' => 'multiple_choice',
                    'pilihan_jawaban' => [
                        'A' => 'Kesempatan untuk berinovasi dan menciptakan hal baru',
                        'B' => 'Pengakuan dan apresiasi dari orang lain',
                        'C' => 'Kepuasan melihat hasil yang sempurna',
                        'D' => 'Kontribusi untuk membantu orang lain'
                    ],
                    'bobot_karakter' => [
                        'A' => ['creativity' => 3, 'courage' => 2, 'motivation' => 2],
                        'B' => ['communication' => 2, 'leadership' => 2, 'motivation' => 3],
                        'C' => ['perfectionism' => 3, 'analytical' => 2],
                        'D' => ['empathy' => 3, 'wisdom' => 2]
                    ],
                    'is_active' => true,
                ],
                [
                    'test_id' => $testDasar->id,
                    'nomor_soal' => 5,
                    'pertanyaan' => 'Bagaimana Anda mengelola waktu dan tugas?',
                    'tipe_soal' => 'multiple_choice',
                    'pilihan_jawaban' => [
                        'A' => 'Membuat rencana detail dan mengikutinya dengan ketat',
                        'B' => 'Fleksibel dan menyesuaikan dengan situasi',
                        'C' => 'Fokus pada prioritas utama saja',
                        'D' => 'Multi-tasking dan menangani banyak hal sekaligus'
                    ],
                    'bobot_karakter' => [
                        'A' => ['perfectionism' => 3, 'analytical' => 2],
                        'B' => ['adaptability' => 3, 'creativity' => 2],
                        'C' => ['leadership' => 2, 'wisdom' => 3, 'analytical' => 2],
                        'D' => ['motivation' => 3, 'adaptability' => 2, 'courage' => 2]
                    ],
                    'is_active' => true,
                ],
                [
                    'test_id' => $testDasar->id,
                    'nomor_soal' => 6,
                    'pertanyaan' => 'Dalam tim, peran yang paling nyaman untuk Anda adalah:',
                    'tipe_soal' => 'multiple_choice',
                    'pilihan_jawaban' => [
                        'A' => 'Pemimpin yang mengarahkan dan membuat keputusan',
                        'B' => 'Kreator yang menghasilkan ide-ide baru',
                        'C' => 'Mediator yang menjaga harmoni tim',
                        'D' => 'Pelaksana yang menyelesaikan tugas dengan baik'
                    ],
                    'bobot_karakter' => [
                        'A' => ['leadership' => 3, 'courage' => 2, 'communication' => 2],
                        'B' => ['creativity' => 3, 'adaptability' => 2],
                        'C' => ['empathy' => 3, 'communication' => 2, 'wisdom' => 2],
                        'D' => ['perfectionism' => 3, 'analytical' => 2]
                    ],
                    'is_active' => true,
                ],
                [
                    'test_id' => $testDasar->id,
                    'nomor_soal' => 7,
                    'pertanyaan' => 'Ketika mendapat kritik, reaksi Anda:',
                    'tipe_soal' => 'multiple_choice',
                    'pilihan_jawaban' => [
                        'A' => 'Menganalisis dan mencari cara untuk memperbaiki',
                        'B' => 'Merasa tertantang untuk membuktikan kemampuan',
                        'C' => 'Mempertimbangkan dengan hati-hati sebelum bereaksi',
                        'D' => 'Terbuka menerima dan berterima kasih atas masukan'
                    ],
                    'bobot_karakter' => [
                        'A' => ['analytical' => 3, 'perfectionism' => 2, 'wisdom' => 2],
                        'B' => ['courage' => 3, 'motivation' => 3],
                        'C' => ['wisdom' => 3, 'empathy' => 2],
                        'D' => ['empathy' => 3, 'adaptability' => 2, 'wisdom' => 2]
                    ],
                    'is_active' => true,
                ],
                [
                    'test_id' => $testDasar->id,
                    'nomor_soal' => 8,
                    'pertanyaan' => 'Cara Anda mengekspresikan ide atau pendapat:',
                    'tipe_soal' => 'multiple_choice',
                    'pilihan_jawaban' => [
                        'A' => 'Langsung dan to the point',
                        'B' => 'Dengan data dan fakta yang mendukung',
                        'C' => 'Menyesuaikan dengan siapa yang diajak bicara',
                        'D' => 'Dengan cerita dan contoh yang relatable'
                    ],
                    'bobot_karakter' => [
                        'A' => ['courage' => 3, 'leadership' => 2, 'communication' => 2],
                        'B' => ['analytical' => 3, 'perfectionism' => 2],
                        'C' => ['empathy' => 3, 'adaptability' => 3, 'wisdom' => 2],
                        'D' => ['communication' => 3, 'creativity' => 2]
                    ],
                    'is_active' => true,
                ],
                [
                    'test_id' => $testDasar->id,
                    'nomor_soal' => 9,
                    'pertanyaan' => 'Lingkungan kerja yang ideal bagi Anda:',
                    'tipe_soal' => 'multiple_choice',
                    'pilihan_jawaban' => [
                        'A' => 'Dinamis dengan tantangan baru setiap hari',
                        'B' => 'Terstruktur dengan prosedur yang jelas',
                        'C' => 'Kolaboratif dengan interaksi tim yang baik',
                        'D' => 'Mandiri dengan kebebasan berkreasi'
                    ],
                    'bobot_karakter' => [
                        'A' => ['adaptability' => 3, 'courage' => 2, 'motivation' => 2],
                        'B' => ['perfectionism' => 3, 'analytical' => 2],
                        'C' => ['empathy' => 3, 'communication' => 3],
                        'D' => ['creativity' => 3, 'wisdom' => 2]
                    ],
                    'is_active' => true,
                ],
                [
                    'test_id' => $testDasar->id,
                    'nomor_soal' => 10,
                    'pertanyaan' => 'Apa yang Anda lakukan untuk mengembangkan diri?',
                    'tipe_soal' => 'multiple_choice',
                    'pilihan_jawaban' => [
                        'A' => 'Mengikuti kursus atau pelatihan formal',
                        'B' => 'Belajar dari pengalaman dan trial-error',
                        'C' => 'Membaca buku dan artikel',
                        'D' => 'Berdiskusi dengan mentor atau ahli'
                    ],
                    'bobot_karakter' => [
                        'A' => ['perfectionism' => 2, 'analytical' => 2, 'motivation' => 2],
                        'B' => ['courage' => 3, 'adaptability' => 3, 'creativity' => 2],
                        'C' => ['wisdom' => 3, 'analytical' => 2],
                        'D' => ['empathy' => 2, 'communication' => 2, 'wisdom' => 3]
                    ],
                    'is_active' => true,
                ],
            ];

            foreach ($questions as $question) {
                TestQuestion::create($question);
            }
        }

        // Untuk test standar dan premium, bisa ditambahkan lebih banyak soal
        // atau menggunakan soal yang sama dengan variasi
        if ($testStandar) {
            // Copy soal dari test dasar dan tambahkan lebih banyak
            $basarQuestions = TestQuestion::where('test_id', $testDasar->id)->get();
            foreach ($basarQuestions as $index => $question) {
                TestQuestion::create([
                    'test_id' => $testStandar->id,
                    'nomor_soal' => $index + 1,
                    'pertanyaan' => $question->pertanyaan,
                    'tipe_soal' => $question->tipe_soal,
                    'pilihan_jawaban' => $question->pilihan_jawaban,
                    'bobot_karakter' => $question->bobot_karakter,
                    'is_active' => true,
                ]);
            }

            // Tambahkan soal ekstra untuk test standar
            $extraQuestions = [
                [
                    'test_id' => $testStandar->id,
                    'nomor_soal' => 11,
                    'pertanyaan' => 'Bagaimana Anda menangani konflik interpersonal?',
                    'tipe_soal' => 'multiple_choice',
                    'pilihan_jawaban' => [
                        'A' => 'Menghadapinya langsung dan mencari solusi',
                        'B' => 'Mendengarkan semua pihak terlebih dahulu',
                        'C' => 'Menghindari konfrontasi dan memberi waktu',
                        'D' => 'Mencari mediator atau pihak ketiga'
                    ],
                    'bobot_karakter' => [
                        'A' => ['courage' => 3, 'leadership' => 2, 'communication' => 2],
                        'B' => ['empathy' => 3, 'wisdom' => 3, 'communication' => 2],
                        'C' => ['adaptability' => 2, 'wisdom' => 2],
                        'D' => ['empathy' => 2, 'wisdom' => 3]
                    ],
                    'is_active' => true,
                ],
                // Add more questions...
            ];

            foreach ($extraQuestions as $question) {
                TestQuestion::create($question);
            }
        }

        if ($testPremium) {
            // Copy dari test standar
            $standarQuestions = TestQuestion::where('test_id', $testStandar->id)->get();
            foreach ($standarQuestions as $index => $question) {
                TestQuestion::create([
                    'test_id' => $testPremium->id,
                    'nomor_soal' => $index + 1,
                    'pertanyaan' => $question->pertanyaan,
                    'tipe_soal' => $question->tipe_soal,
                    'pilihan_jawaban' => $question->pilihan_jawaban,
                    'bobot_karakter' => $question->bobot_karakter,
                    'is_active' => true,
                ]);
            }
        }
    }
}
