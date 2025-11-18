<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tests = [
            [
                'nama_tes' => 'Tes Karakter Alami Saintara - Dasar',
                'deskripsi_tes' => 'Tes untuk mengetahui karakter alami Anda berdasarkan data pribadi dan preferensi dasar. Mendapatkan hasil analisis 10 karakter utama yang mencakup sistem operasi otak, arah ekspresi, dan kecenderungan alami Anda.',
                'jenis_tes' => 'kepribadian',
                'jumlah_soal' => 10,
                'durasi_menit' => 5,
                'token_required' => 1,
                'is_active' => true,
                'metadata' => json_encode([
                    'category' => 'basic',
                    'difficulty' => 'easy',
                    'character_analysis' => [
                        'Sistem Operasi Otak',
                        'Arah Ekspresi (Introvert/Extrovert)',
                        'Unsur Karakter Alami',
                        'Warna Karakter & Maknanya',
                        'Kata Kunci Kepribadian',
                        'Kekuatan Utama',
                        'Tantangan Utama',
                        'Cara Belajar',
                        'Kecenderungan Karir',
                        'Komunikasi Dasar'
                    ],
                    'result_format' => 'pdf',
                    'certificate' => true,
                    'package_type' => ['personal_dasar', 'gift_dasar']
                ])
            ],
            [
                'nama_tes' => 'Tes Karakter Alami Saintara - Standar',
                'deskripsi_tes' => 'Tes lengkap untuk mengetahui karakter alami Anda dengan 25 analisis mendalam. Mencakup aspek kepribadian, relasi, potensi diri, dan kehidupan praktis.',
                'jenis_tes' => 'kepribadian',
                'jumlah_soal' => 25,
                'durasi_menit' => 10,
                'token_required' => 1,
                'is_active' => true,
                'metadata' => json_encode([
                    'category' => 'standard',
                    'difficulty' => 'medium',
                    'character_analysis' => [
                        // Identitas Dasar (10)
                        'Sistem Operasi Otak',
                        'Arah Ekspresi',
                        'Unsur Karakter Alami',
                        'Warna Karakter & Maknanya',
                        'Kata Kunci',
                        // Fondasi Kehidupan (7)
                        'Kemistri Alami',
                        'Peranan Ideal',
                        'Target & Kunci Sukses',
                        'Harapan Alami',
                        'Mental Alami',
                        'Kecenderungan Alami',
                        'Ketakutan Diri',
                        // Relasi Sosial (5)
                        'Kecocokan Pasangan',
                        'Kecocokan Pertemanan',
                        'Coach & Mentor Ideal',
                        'Bahasa Cinta',
                        // Potensi Diri (3)
                        'Kekuatan/Kelebihan',
                        'Tantangan/Kelemahan',
                        'Sumber Kebahagiaan',
                        // Belajar & Berkembang (5)
                        'Cara Belajar',
                        'Naikkan Minat Belajar',
                        'Sekolah Menuju Karir',
                        'Rekomendasi Karir',
                        'Tips Berbisnis',
                        // Kehidupan Praktis (3)
                        'Tabiat Uang',
                        'Cara Bekerja',
                        'Pengendalian Mood'
                    ],
                    'result_format' => 'pdf',
                    'certificate' => true,
                    'consultation' => true,
                    'package_type' => ['personal_standar']
                ])
            ],
            [
                'nama_tes' => 'Tes Karakter Alami Saintara - Premium',
                'deskripsi_tes' => 'Tes ultimate dengan 35+ analisis karakter super lengkap. Mencakup semua aspek kehidupan dengan detail dan rekomendasi personal untuk pengembangan diri optimal.',
                'jenis_tes' => 'kepribadian',
                'jumlah_soal' => 35,
                'durasi_menit' => 15,
                'token_required' => 1,
                'is_active' => true,
                'metadata' => json_encode([
                    'category' => 'premium',
                    'difficulty' => 'advanced',
                    'character_analysis' => [
                        // Identitas Dasar (10)
                        'Sistem Operasi Otak Detail',
                        'Arah Ekspresi & Pola Energi',
                        'Unsur Karakter Alami',
                        'Warna Karakter & Maknanya',
                        'Kata Kunci Kepribadian',
                        // Fondasi Kehidupan (7)
                        'Kemistri Alami dengan Semua Tipe',
                        'Peranan Ideal di Berbagai Konteks',
                        'Target & Kunci Sukses Detail',
                        'Harapan & Motivasi Alami',
                        'Mental & Mindset Alami',
                        'Kecenderungan Alami dalam Berbagai Situasi',
                        'Ketakutan Diri & Cara Mengatasinya',
                        // Relasi Sosial (8)
                        'Kecocokan Pasangan (Ranking Lengkap)',
                        'Saran Ideal Rumah Tangga',
                        'Kecocokan Pertemanan Detail',
                        'Coach & Mentor Ideal',
                        'Bahasa Cinta & Cara Mengekspresikan',
                        'Pola Komunikasi dengan Tipe Lain',
                        'Conflict Resolution Style',
                        'Networking Strategy',
                        // Potensi Diri (6)
                        'Kekuatan/Kelebihan Detail',
                        'Tantangan/Kelemahan & Solusi',
                        'Catatan Potensi & Ancaman',
                        'Apresiasi untuk Diri',
                        'Sumber Kebahagiaan',
                        'Self-Development Roadmap',
                        // Belajar & Berkembang (7)
                        'Cara Belajar Optimal',
                        'Naikkan Minat Belajar',
                        'Sekolah Menuju Karir',
                        'Rekomendasi Karir Detail',
                        'Tips Berbisnis & Entrepreneurship',
                        'Nasehat Penting',
                        'Skill Development Priority',
                        // Kehidupan Praktis (7)
                        'Tabiat Uang & Financial Planning',
                        'Cara Bekerja & Produktivitas',
                        'Pengendalian Mood',
                        'Pemicu Emosi & Stres',
                        'Saran Olahraga & Wellness',
                        'Work-Life Balance Strategy',
                        'Personal Branding'
                    ],
                    'result_format' => 'pdf_premium',
                    'certificate' => true,
                    'consultation' => true,
                    'consultation_sessions' => 3,
                    'ai_chat_access' => true,
                    'lifetime_updates' => true,
                    'package_type' => ['personal_premium']
                ])
            ],
            [
                'nama_tes' => 'Tes Karakter Tim - Instansi',
                'deskripsi_tes' => 'Tes karakter untuk anggota tim organisasi/instansi. Fokus pada analisis chemistry tim, penempatan optimal, dan pengembangan organisasi.',
                'jenis_tes' => 'kepribadian',
                'jumlah_soal' => 20,
                'durasi_menit' => 10,
                'token_required' => 1,
                'is_active' => true,
                'metadata' => json_encode([
                    'category' => 'organization',
                    'difficulty' => 'medium',
                    'target_audience' => 'instansi',
                    'character_analysis' => [
                        'Karakter Alami Individual',
                        'Gaya Komunikasi dalam Tim',
                        'Peran Ideal dalam Organisasi',
                        'Kekuatan yang Dapat Dikontribusikan',
                        'Area Pengembangan',
                        'Chemistry dengan Rekan Tim',
                        'Gaya Kepemimpinan',
                        'Cara Kerja Optimal',
                        'Motivasi Kerja',
                        'Conflict Management Style'
                    ],
                    'team_features' => [
                        'Team Chemistry Analysis',
                        'Optimal Role Placement',
                        'Team Composition Report',
                        'Leadership Recommendation',
                        'Team Development Strategy'
                    ],
                    'result_format' => 'pdf',
                    'certificate' => true,
                    'bulk_submission' => true,
                    'package_type' => ['instansi_umum', 'instansi_lengkap', 'instansi_pelatihan_online', 'instansi_pelatihan_offline']
                ])
            ],
            [
                'nama_tes' => 'Tes Minat & Bakat - Sekolah',
                'deskripsi_tes' => 'Tes untuk siswa sekolah yang fokus pada identifikasi minat, bakat, dan kecenderungan karir. Membantu siswa menentukan jurusan dan arah pendidikan.',
                'jenis_tes' => 'minat_bakat',
                'jumlah_soal' => 30,
                'durasi_menit' => 15,
                'token_required' => 1,
                'is_active' => true,
                'metadata' => json_encode([
                    'category' => 'education',
                    'difficulty' => 'easy',
                    'target_audience' => 'students',
                    'age_range' => '12-18',
                    'character_analysis' => [
                        'Karakter Alami Siswa',
                        'Gaya Belajar Optimal',
                        'Cara Meningkatkan Motivasi Belajar',
                        'Kecerdasan Dominan',
                        'Minat Utama',
                        'Bakat yang Menonjol',
                        'Rekomendasi Jurusan SMA/SMK',
                        'Rekomendasi Jurusan Kuliah',
                        'Rekomendasi Karir',
                        'Ekstrakurikuler yang Cocok'
                    ],
                    'school_features' => [
                        'Student Profile Report',
                        'Parent Guidance',
                        'Teacher Recommendation',
                        'Career Pathway',
                        'Study Plan'
                    ],
                    'result_format' => 'pdf',
                    'certificate' => true,
                    'bulk_submission' => true,
                    'parent_access' => true,
                    'teacher_dashboard' => true,
                    'package_type' => ['sekolah_umum', 'sekolah_lengkap']
                ])
            ]
        ];

        foreach ($tests as $test) {
            \App\Models\Test::create($test);
        }
    }
}
