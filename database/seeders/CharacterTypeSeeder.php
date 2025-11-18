<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CharacterTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $characterTypes = [
            [
                'name' => 'Pemikir Introvert',
                'code' => 'THINKING_INTROVERT',
                'description' => 'Karakter yang cenderung berpikir mendalam, analitis, dan introspektif. Memiliki kemampuan luar biasa dalam menganalisis masalah secara logis dan sistematis. Lebih nyaman bekerja sendiri dan membutuhkan waktu untuk merenung.',
                'strengths' => [
                    'Kemampuan analisis yang mendalam',
                    'Berpikir logis dan sistematis',
                    'Mandiri dan fokus tinggi',
                    'Teliti dalam menyelesaikan masalah',
                    'Kemampuan riset yang kuat',
                    'Objektif dalam pengambilan keputusan'
                ],
                'challenges' => [
                    'Cenderung overthinking',
                    'Sulit mengekspresikan emosi',
                    'Kadang terlalu perfeksionis',
                    'Kurang fleksibel dengan perubahan mendadak',
                    'Butuh waktu lama untuk mengambil keputusan'
                ],
                'communication_style' => 'Lebih suka komunikasi tertulis yang terstruktur. Berbicara seperlunya dengan data dan fakta. Menghindari small talk.',
                'image_path' => '/images/characters/pemikir-introvert.png'
            ],
            [
                'name' => 'Pemikir Extrovert',
                'code' => 'THINKING_EXTROVERT',
                'description' => 'Karakter yang menggabungkan kemampuan berpikir analitis dengan energy ekstrovert. Senang berdiskusi, mendebat ide, dan memimpin dengan logika yang kuat.',
                'strengths' => [
                    'Pemikiran strategis yang kuat',
                    'Kemampuan leadership alami',
                    'Percaya diri dalam menyampaikan pendapat',
                    'Cepat dalam pengambilan keputusan',
                    'Mampu memotivasi tim dengan logika',
                    'Berani mengambil risiko terkalkulasi'
                ],
                'challenges' => [
                    'Kadang terlalu dominan dalam diskusi',
                    'Kurang peka terhadap perasaan orang lain',
                    'Terlalu fokus pada hasil',
                    'Sulit menerima kritik',
                    'Cenderung impatient'
                ],
                'communication_style' => 'Komunikatif, asertif, langsung to the point. Senang berdebat dan berdiskusi ide-ide besar.',
                'image_path' => '/images/characters/pemikir-extrovert.png'
            ],
            [
                'name' => 'Pengamat Introvert',
                'code' => 'SENSING_INTROVERT',
                'description' => 'Karakter yang detail-oriented, praktis, dan realistis. Sangat memperhatikan fakta dan data konkret. Bekerja dengan teliti dan konsisten.',
                'strengths' => [
                    'Sangat teliti dan detail',
                    'Praktis dan realistis',
                    'Konsisten dalam bekerja',
                    'Bertanggung jawab tinggi',
                    'Terorganisir dengan baik',
                    'Mengingat detail dengan akurat'
                ],
                'challenges' => [
                    'Terlalu fokus pada detail kecil',
                    'Kurang terbuka dengan ide baru',
                    'Sulit beradaptasi dengan perubahan',
                    'Cenderung rigid',
                    'Terlalu perfeksionis pada hal teknis'
                ],
                'communication_style' => 'Komunikasi yang spesifik dan faktual. Lebih suka instruksi yang jelas dan tertulis.',
                'image_path' => '/images/characters/pengamat-introvert.png'
            ],
            [
                'name' => 'Pengamat Extrovert',
                'code' => 'SENSING_EXTROVERT',
                'description' => 'Karakter yang energik, praktis, dan action-oriented. Senang berinteraksi dengan orang banyak sambil tetap fokus pada realitas dan hasil nyata.',
                'strengths' => [
                    'Energik dan aktif',
                    'Pragmatis dan action-oriented',
                    'Mudah bergaul',
                    'Adaptif dengan lingkungan',
                    'Senang tantangan baru',
                    'Problem solver yang praktis'
                ],
                'challenges' => [
                    'Kurang sabar dengan teori',
                    'Terlalu impulsif',
                    'Kurang perencanaan jangka panjang',
                    'Mudah bosan dengan rutinitas',
                    'Fokus pada sensasi saat ini'
                ],
                'communication_style' => 'Komunikatif, spontan, dan ekspresif. Senang bercerita dengan detail pengalaman konkret.',
                'image_path' => '/images/characters/pengamat-extrovert.png'
            ],
            [
                'name' => 'Perasa Introvert',
                'code' => 'FEELING_INTROVERT',
                'description' => 'Karakter yang penuh empati, sensitif, dan idealis. Sangat memperhatikan nilai dan perasaan orang lain. Bekerja dengan hati dan dedikasi.',
                'strengths' => [
                    'Empati yang tinggi',
                    'Pendengar yang baik',
                    'Idealis dan autentik',
                    'Kreatif dalam ekspresi diri',
                    'Memiliki nilai dan prinsip yang kuat',
                    'Loyal dan mendukung'
                ],
                'challenges' => [
                    'Terlalu sensitif',
                    'Sulit mengatakan tidak',
                    'Mudah terluka',
                    'Cenderung menghindari konflik',
                    'Terlalu idealis'
                ],
                'communication_style' => 'Lembut, penuh empati, dan mendalam. Komunikasi one-on-one lebih nyaman.',
                'image_path' => '/images/characters/perasa-introvert.png'
            ],
            [
                'name' => 'Perasa Extrovert',
                'code' => 'FEELING_EXTROVERT',
                'description' => 'Karakter yang hangat, ekspresif, dan people-oriented. Senang membantu orang lain dan menciptakan harmoni dalam kelompok.',
                'strengths' => [
                    'Sangat sosial dan ramah',
                    'Membangun hubungan dengan mudah',
                    'Mendukung dan memotivasi orang lain',
                    'Ekspresif secara emosional',
                    'Team player yang baik',
                    'Komunikator ulung'
                ],
                'challenges' => [
                    'Terlalu bergantung pada validasi orang lain',
                    'Sulit membuat keputusan objektif',
                    'Mudah terpengaruh emosi orang lain',
                    'Terlalu menghindari konflik',
                    'Kurang assertive'
                ],
                'communication_style' => 'Hangat, ekspresif, dan penuh antusiasme. Senang berbagi cerita personal.',
                'image_path' => '/images/characters/perasa-extrovert.png'
            ],
            [
                'name' => 'Pemimpi Introvert',
                'code' => 'INTUITING_INTROVERT',
                'description' => 'Karakter yang visioner, imajinatif, dan konseptual. Senang memikirkan kemungkinan-kemungkinan masa depan dan ide-ide abstrak.',
                'strengths' => [
                    'Visioner dan inovatif',
                    'Pemikiran konseptual yang kuat',
                    'Kreatif dalam memecahkan masalah',
                    'Mampu melihat pola dan koneksi',
                    'Independen dalam berpikir',
                    'Wawasan mendalam'
                ],
                'challenges' => [
                    'Terlalu fokus pada teori',
                    'Kurang praktis',
                    'Sulit mengimplementasikan ide',
                    'Mudah teralihkan dengan ide baru',
                    'Kurang perhatian pada detail'
                ],
                'communication_style' => 'Abstrak dan konseptual. Senang diskusi filosofis dan ide-ide besar.',
                'image_path' => '/images/characters/pemimpi-introvert.png'
            ],
            [
                'name' => 'Pemimpi Extrovert',
                'code' => 'INTUITING_EXTROVERT',
                'description' => 'Karakter yang penuh antusiasme, inovatif, dan inspiratif. Senang mengeksplorasi kemungkinan baru dan menginspirasi orang lain dengan visi mereka.',
                'strengths' => [
                    'Penuh energi dan antusiasme',
                    'Inovatif dan kreatif',
                    'Inspiratif bagi orang lain',
                    'Mudah beradaptasi',
                    'Melihat peluang di mana-mana',
                    'Networking yang kuat'
                ],
                'challenges' => [
                    'Terlalu banyak ide, kurang eksekusi',
                    'Mudah bosan dengan rutinitas',
                    'Kurang follow-through',
                    'Terlalu optimis',
                    'Sulit fokus pada satu hal'
                ],
                'communication_style' => 'Antusias, inspiratif, dan penuh ide. Senang brainstorming dan diskusi kreatif.',
                'image_path' => '/images/characters/pemimpi-extrovert.png'
            ],
            [
                'name' => 'Penggerak',
                'code' => 'MOBILIZER',
                'description' => 'Karakter yang dinamis, action-oriented, dan pragmatis. Kombinasi sempurna antara thinking dan doing. Mampu menggerakkan diri dan orang lain untuk mencapai tujuan.',
                'strengths' => [
                    'Action-oriented yang kuat',
                    'Pragmatis dan efektif',
                    'Natural leader',
                    'Cepat mengambil keputusan',
                    'Berani mengambil risiko',
                    'Energi tinggi dan produktif',
                    'Mampu mempengaruhi orang lain'
                ],
                'challenges' => [
                    'Terlalu impulsif',
                    'Kurang sabar',
                    'Bisa terlalu agresif',
                    'Kurang mempertimbangkan perasaan',
                    'Workaholic'
                ],
                'communication_style' => 'Langsung, tegas, dan persuasif. Fokus pada hasil dan action.',
                'image_path' => '/images/characters/penggerak.png'
            ]
        ];

        foreach ($characterTypes as $type) {
            \App\Models\CharacterType::create($type);
        }
    }
}
