<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'nama_paket' => 'Paket Dasar',
                'harga' => 99000,
                'deskripsi' => 'Paket personal untuk mengenal karakter dasar Anda. Mendapatkan hasil tes dengan 10 analisis karakter utama.',
                'tipe_paket' => 'personal',
                'jumlah_token' => 1,
                'masa_aktif_hari' => 30,
                'is_active' => true,
                'metadata' => json_encode([
                    'features' => [
                        'Analisis 10 Karakter Dasar',
                        'Sistem Operasi Otak',
                        'Arah Ekspresi',
                        'Unsur Karakter Alami',
                        'Warna Karakter',
                        'Kata Kunci Kepribadian',
                        'Kekuatan & Tantangan',
                        'Cara Belajar',
                        'Rekomendasi Karir',
                        'Sertifikat Digital'
                    ],
                    'validity_days' => 30,
                    'max_attempts' => 1
                ])
            ],
            [
                'nama_paket' => 'Paket Standar',
                'harga' => 199000,
                'deskripsi' => 'Paket lengkap dengan 25 analisis karakter mendalam. Cocok untuk pengembangan diri dan perencanaan karir.',
                'tipe_paket' => 'personal',
                'jumlah_token' => 1,
                'masa_aktif_hari' => 90,
                'is_active' => true,
                'metadata' => json_encode([
                    'features' => [
                        'Analisis 25 Karakter Lengkap',
                        'Semua fitur Paket Dasar',
                        'Kecocokan Pasangan (Ranking)',
                        'Kecocokan Pertemanan',
                        'Bahasa Cinta',
                        'Coach & Mentor Ideal',
                        'Target & Kunci Sukses',
                        'Harapan & Mental Alami',
                        'Ketakutan Diri',
                        'Tabiat Uang',
                        'Cara Bekerja',
                        'Pengendalian Mood',
                        'Saran Olahraga',
                        'Tips Berbisnis',
                        'Konsultasi Email 1x',
                        'Sertifikat Premium'
                    ],
                    'validity_days' => 90,
                    'max_attempts' => 2,
                    'consultation' => true
                ])
            ],
            [
                'nama_paket' => 'Paket Premium',
                'harga' => 349000,
                'deskripsi' => 'Paket ultimate dengan 35+ analisis karakter super lengkap, konsultasi personal, dan akses seumur hidup.',
                'tipe_paket' => 'personal',
                'jumlah_token' => 1,
                'masa_aktif_hari' => 365,
                'is_active' => true,
                'metadata' => json_encode([
                    'features' => [
                        'Analisis 35+ Karakter Ultimate',
                        'Semua fitur Paket Standar',
                        'Analisis Mendalam Semua Aspek Kehidupan',
                        'Konsultasi Personal 3x via Video Call',
                        'Saran Pengembangan Karir Personal',
                        'Analisis Kecocokan Tim Kerja',
                        'Rekomendasi Pendidikan Lanjutan',
                        'Update Hasil Tes Lifetime',
                        'Akses AI Chat Assistant',
                        'Laporan PDF Premium',
                        'Sertifikat Premium + Tanda Tangan Digital',
                        'Priority Support 24/7'
                    ],
                    'validity_days' => 365,
                    'max_attempts' => 'unlimited',
                    'consultation' => true,
                    'consultation_sessions' => 3,
                    'ai_access' => true,
                    'priority_support' => true
                ])
            ],
            // Paket Instansi/Organisasi
            [
                'nama_paket' => 'Paket Instansi - Laporan Umum',
                'harga' => 75000, // per orang
                'deskripsi' => 'Paket untuk organisasi/instansi dengan laporan umum karakter tim.',
                'tipe_paket' => 'instansi',
                'jumlah_token' => 1,
                'masa_aktif_hari' => 30,
                'is_active' => true,
                'metadata' => json_encode([
                    'features' => [
                        'Analisis Karakter Individual',
                        'Laporan Umum Tim',
                        'Rekomendasi Penempatan',
                        'Sertifikat Digital'
                    ],
                    'min_participants' => 10,
                    'price_per_person' => 75000
                ])
            ],
            [
                'nama_paket' => 'Paket Instansi - Lengkap & Penempatan',
                'harga' => 120000, // per orang
                'deskripsi' => 'Paket lengkap untuk organisasi dengan analisis mendalam dan strategi penempatan tim.',
                'tipe_paket' => 'instansi',
                'jumlah_token' => 1,
                'masa_aktif_hari' => 60,
                'is_active' => true,
                'metadata' => json_encode([
                    'features' => [
                        'Analisis Karakter Lengkap',
                        'Laporan Detail Individual & Tim',
                        'Strategi Penempatan Optimal',
                        'Analisis Chemistry Tim',
                        'Rekomendasi Leadership',
                        'Dashboard Admin',
                        'Sertifikat Premium'
                    ],
                    'min_participants' => 10,
                    'price_per_person' => 120000,
                    'team_analysis' => true
                ])
            ],
            [
                'nama_paket' => 'Paket Instansi + Pelatihan Online',
                'harga' => 180000, // per orang
                'deskripsi' => 'Paket instansi dengan pelatihan online untuk pengembangan tim.',
                'tipe_paket' => 'instansi',
                'jumlah_token' => 1,
                'masa_aktif_hari' => 90,
                'is_active' => true,
                'metadata' => json_encode([
                    'features' => [
                        'Semua fitur Paket Lengkap',
                        'Pelatihan Online 2 Sesi @ 2 Jam',
                        'Materi Pelatihan Digital',
                        'Sesi Q&A dengan Trainer',
                        'Recording Video Pelatihan',
                        'Follow-up Consultation'
                    ],
                    'min_participants' => 10,
                    'price_per_person' => 180000,
                    'training_sessions' => 2,
                    'training_duration' => '2 jam per sesi'
                ])
            ],
            [
                'nama_paket' => 'Paket Instansi + Pelatihan Tatap Muka',
                'harga' => 250000, // per orang
                'deskripsi' => 'Paket premium instansi dengan pelatihan tatap muka langsung oleh certified trainer.',
                'tipe_paket' => 'instansi',
                'jumlah_token' => 1,
                'masa_aktif_hari' => 90,
                'is_active' => true,
                'metadata' => json_encode([
                    'features' => [
                        'Semua fitur Paket Lengkap',
                        'Pelatihan Tatap Muka Full Day',
                        'Certified Trainer',
                        'Materi Pelatihan Printed',
                        'Team Building Activities',
                        'Lunch & Coffee Break',
                        'Certificate of Participation',
                        'Follow-up Support 3 Bulan'
                    ],
                    'min_participants' => 20,
                    'price_per_person' => 250000,
                    'training_type' => 'offline',
                    'training_duration' => '1 hari (8 jam)'
                ])
            ],
            // Paket Sekolah
            [
                'nama_paket' => 'Paket Sekolah - Laporan Umum',
                'harga' => 50000, // per siswa
                'deskripsi' => 'Paket untuk sekolah dengan laporan umum karakter siswa.',
                'tipe_paket' => 'sekolah',
                'jumlah_token' => 1,
                'masa_aktif_hari' => 30,
                'is_active' => true,
                'metadata' => json_encode([
                    'features' => [
                        'Analisis Karakter Siswa',
                        'Laporan untuk Guru & Orang Tua',
                        'Gaya Belajar',
                        'Sertifikat Digital'
                    ],
                    'min_participants' => 30,
                    'price_per_student' => 50000
                ])
            ],
            [
                'nama_paket' => 'Paket Sekolah - Lengkap & Kecenderungan Karir',
                'harga' => 85000, // per siswa
                'deskripsi' => 'Paket lengkap untuk sekolah dengan analisis kecenderungan karir siswa.',
                'tipe_paket' => 'sekolah',
                'jumlah_token' => 1,
                'masa_aktif_hari' => 60,
                'is_active' => true,
                'metadata' => json_encode([
                    'features' => [
                        'Analisis Karakter Lengkap',
                        'Kecenderungan Minat & Bakat',
                        'Rekomendasi Jurusan',
                        'Rekomendasi Karir',
                        'Gaya Belajar Optimal',
                        'Tips Motivasi Belajar',
                        'Konseling Guidance',
                        'Dashboard Guru BK',
                        'Sertifikat Premium'
                    ],
                    'min_participants' => 30,
                    'price_per_student' => 85000,
                    'career_guidance' => true
                ])
            ],
            // Paket Hadiah/Sosial Gift
            [
                'nama_paket' => 'Gift - Paket Dasar',
                'harga' => 99000,
                'deskripsi' => 'Berikan hadiah berharga berupa tes karakter Saintara untuk orang tersayang.',
                'tipe_paket' => 'gift',
                'jumlah_token' => 1,
                'masa_aktif_hari' => 90,
                'is_active' => true,
                'metadata' => json_encode([
                    'features' => [
                        'Analisis 10 Karakter Dasar',
                        'Sertifikat Digital',
                        'Gift Card Digital',
                        'Pesan Personal dari Pemberi'
                    ],
                    'validity_days' => 90,
                    'gift_card' => true
                ])
            ],
            [
                'nama_paket' => 'Social Gift - Donasi 1 Tes',
                'harga' => 50000,
                'deskripsi' => 'Donasi 1 tes untuk anak/remaja yang membutuhkan.',
                'tipe_paket' => 'social_gift',
                'jumlah_token' => 1,
                'masa_aktif_hari' => 365,
                'is_active' => true,
                'metadata' => json_encode([
                    'features' => [
                        'Donasi untuk yang membutuhkan',
                        'Laporan dampak sosial',
                        'Sertifikat Apresiasi Donatur'
                    ],
                    'donation_type' => 'individual',
                    'tax_deductible' => false
                ])
            ],
            [
                'nama_paket' => 'Social Gift - Donasi 10 Tes',
                'harga' => 450000,
                'deskripsi' => 'Donasi paket 10 tes untuk sekolah/komunitas.',
                'tipe_paket' => 'social_gift',
                'jumlah_token' => 10,
                'masa_aktif_hari' => 365,
                'is_active' => true,
                'metadata' => json_encode([
                    'features' => [
                        'Donasi 10 tes',
                        'Pemilihan target penerima',
                        'Laporan dampak sosial detail',
                        'Plakat Apresiasi',
                        'Publikasi di Media Sosial'
                    ],
                    'donation_type' => 'bulk',
                    'tax_deductible' => true
                ])
            ]
        ];

        foreach ($packages as $package) {
            \App\Models\Package::create($package);
        }
    }
}
