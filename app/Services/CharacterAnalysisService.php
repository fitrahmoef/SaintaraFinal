<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Service untuk menganalisis karakter berdasarkan nama, tanggal lahir, dan golongan darah
 * Menggunakan metode numerologi dan karakteristik psikologis
 */
class CharacterAnalysisService
{
    /**
     * 12 Tipe Karakter Saintara
     */
    private const CHARACTER_TYPES = [
        1 => [
            'nama' => 'Pemimpin Visioner',
            'nama_en' => 'Visionary Leader',
            'deskripsi' => 'Anda adalah seorang pemimpin alami yang memiliki visi jauh ke depan. Anda berani mengambil inisiatif dan mampu menginspirasi orang lain untuk mengikuti visi Anda.',
            'kekuatan' => ['Kepemimpinan kuat', 'Visi jelas', 'Keberanian tinggi', 'Inovatif'],
            'area_pengembangan' => ['Kesabaran', 'Mendengarkan orang lain', 'Delegasi tugas'],
            'karir_cocok' => ['CEO/Direktur', 'Entrepreneur', 'Manajer Proyek', 'Konsultan Strategis'],
        ],
        2 => [
            'nama' => 'Pelindung Harmoni',
            'nama_en' => 'Harmony Protector',
            'deskripsi' => 'Anda sangat peduli dengan keharmonisan dan kesejahteraan orang lain. Anda adalah mediator yang baik dan selalu berusaha menciptakan lingkungan yang damai.',
            'kekuatan' => ['Empati tinggi', 'Diplomasi', 'Kemampuan mendengar', 'Kerjasama tim'],
            'area_pengembangan' => ['Ketegasan', 'Mengutamakan diri sendiri', 'Mengambil keputusan tegas'],
            'karir_cocok' => ['HR Manager', 'Konselor', 'Mediator', 'Community Manager'],
        ],
        3 => [
            'nama' => 'Pemikir Analitis',
            'nama_en' => 'Analytical Thinker',
            'deskripsi' => 'Anda memiliki kemampuan analitis yang tajam dan selalu mencari kebenaran melalui logika dan data. Anda sangat teliti dan metodis dalam pendekatan Anda.',
            'kekuatan' => ['Analisis mendalam', 'Logika kuat', 'Ketelitian', 'Problem solving'],
            'area_pengembangan' => ['Fleksibilitas', 'Intuisi', 'Kecepatan keputusan'],
            'karir_cocok' => ['Data Scientist', 'Researcher', 'Financial Analyst', 'Engineer'],
        ],
        4 => [
            'nama' => 'Kreator Inovatif',
            'nama_en' => 'Innovative Creator',
            'deskripsi' => 'Anda adalah jiwa kreatif yang selalu mencari cara baru untuk mengekspresikan diri dan memecahkan masalah. Imajinasi Anda tidak terbatas.',
            'kekuatan' => ['Kreativitas tinggi', 'Inovasi', 'Berpikir out-of-the-box', 'Adaptabilitas'],
            'area_pengembangan' => ['Fokus', 'Konsistensi', 'Manajemen waktu'],
            'karir_cocok' => ['Designer', 'Artist', 'Product Developer', 'Content Creator'],
        ],
        5 => [
            'nama' => 'Komunikator Inspiratif',
            'nama_en' => 'Inspiring Communicator',
            'deskripsi' => 'Anda memiliki karunia dalam berkomunikasi dan menginspirasi orang lain melalui kata-kata. Anda adalah storyteller yang hebat dan mudah terhubung dengan orang.',
            'kekuatan' => ['Komunikasi efektif', 'Public speaking', 'Networking', 'Pengaruh sosial'],
            'area_pengembangan' => ['Kedalaman analisis', 'Follow-through', 'Dokumentasi'],
            'karir_cocok' => ['Public Relations', 'Marketing Manager', 'Teacher', 'Motivational Speaker'],
        ],
        6 => [
            'nama' => 'Pengabdi Setia',
            'nama_en' => 'Devoted Servant',
            'deskripsi' => 'Anda memiliki dedikasi yang tinggi dan selalu siap membantu orang lain. Kesetiaan dan tanggung jawab adalah nilai utama Anda.',
            'kekuatan' => ['Dedikasi tinggi', 'Tanggung jawab', 'Kesetiaan', 'Perhatian detail'],
            'area_pengembangan' => ['Batasan pribadi', 'Mengatakan tidak', 'Self-care'],
            'karir_cocok' => ['Healthcare Professional', 'Social Worker', 'Customer Service', 'Administrator'],
        ],
        7 => [
            'nama' => 'Petualang Berani',
            'nama_en' => 'Brave Adventurer',
            'deskripsi' => 'Anda adalah jiwa petualang yang tidak takut mengambil risiko dan mencoba hal-hal baru. Anda berkembang dalam ketidakpastian dan tantangan.',
            'kekuatan' => ['Keberanian', 'Adaptabilitas', 'Optimisme', 'Energi tinggi'],
            'area_pengembangan' => ['Perencanaan', 'Stabilitas', 'Pertimbangan risiko'],
            'karir_cocok' => ['Sales', 'Entrepreneur', 'Event Organizer', 'Travel Consultant'],
        ],
        8 => [
            'nama' => 'Diplomat Bijaksana',
            'nama_en' => 'Wise Diplomat',
            'deskripsi' => 'Anda memiliki kebijaksanaan yang mendalam dan kemampuan untuk melihat berbagai perspektif. Anda adalah penengah yang adil dan objektif.',
            'kekuatan' => ['Kebijaksanaan', 'Objektivitas', 'Keadilan', 'Perspektif luas'],
            'area_pengembangan' => ['Kecepatan keputusan', 'Ketegasan', 'Risiko kalkulasi'],
            'karir_cocok' => ['Judge/Lawyer', 'Diplomat', 'Consultant', 'Executive Coach'],
        ],
        9 => [
            'nama' => 'Perfeksionis Detil',
            'nama_en' => 'Detail Perfectionist',
            'deskripsi' => 'Anda memiliki standar yang sangat tinggi dan selalu berusaha mencapai kesempurnaan. Perhatian Anda terhadap detail sangat luar biasa.',
            'kekuatan' => ['Perhatian detail', 'Standar tinggi', 'Kualitas kerja', 'Sistematis'],
            'area_pengembangan' => ['Fleksibilitas', 'Menerima ketidaksempurnaan', 'Delegasi'],
            'karir_cocok' => ['Quality Assurance', 'Auditor', 'Editor', 'Architect'],
        ],
        10 => [
            'nama' => 'Motivator Energetik',
            'nama_en' => 'Energetic Motivator',
            'deskripsi' => 'Anda memiliki energi yang menular dan mampu memotivasi orang lain untuk mencapai potensi terbaik mereka. Antusiasme Anda adalah kekuatan Anda.',
            'kekuatan' => ['Energi tinggi', 'Motivasi', 'Antusiasme', 'Team building'],
            'area_pengembangan' => ['Ketenangan', 'Mendengarkan lebih banyak', 'Refleksi'],
            'karir_cocok' => ['Trainer', 'Coach', 'Team Leader', 'Fitness Instructor'],
        ],
        11 => [
            'nama' => 'Pemikir Filosofis',
            'nama_en' => 'Philosophical Thinker',
            'deskripsi' => 'Anda adalah pemikir yang dalam dan selalu mencari makna di balik segala sesuatu. Anda tertarik pada pertanyaan-pertanyaan besar kehidupan.',
            'kekuatan' => ['Pemikiran mendalam', 'Introspeksi', 'Spiritualitas', 'Kebijaksanaan'],
            'area_pengembangan' => ['Praktikalitas', 'Aksi nyata', 'Komunikasi sederhana'],
            'karir_cocok' => ['Philosopher', 'Writer', 'Spiritual Guide', 'Academic'],
        ],
        12 => [
            'nama' => 'Pembawa Perubahan',
            'nama_en' => 'Change Maker',
            'deskripsi' => 'Anda adalah agen perubahan yang passionate tentang membuat dunia menjadi tempat yang lebih baik. Anda tidak takut menantang status quo.',
            'kekuatan' => ['Visi perubahan', 'Passion', 'Keberanian', 'Pengaruh sosial'],
            'area_pengembangan' => ['Kesabaran', 'Diplomasi', 'Strategi bertahap'],
            'karir_cocok' => ['Activist', 'Change Manager', 'Social Entrepreneur', 'NGO Leader'],
        ],
    ];

    /**
     * Karakteristik Golongan Darah
     */
    private const BLOOD_TYPE_TRAITS = [
        'A' => ['perfeksionis' => 3, 'analitis' => 3, 'harmoni' => 2, 'detail' => 3],
        'A+' => ['perfeksionis' => 3, 'analitis' => 3, 'harmoni' => 2, 'detail' => 3],
        'A-' => ['perfeksionis' => 4, 'analitis' => 4, 'harmoni' => 2, 'detail' => 4],
        'B' => ['kreativ' => 3, 'petualang' => 3, 'energetik' => 3, 'inovatif' => 3],
        'B+' => ['kreativ' => 3, 'petualang' => 3, 'energetik' => 3, 'inovatif' => 3],
        'B-' => ['kreativ' => 4, 'petualang' => 4, 'energetik' => 3, 'inovatif' => 4],
        'AB' => ['diplomat' => 3, 'filosofis' => 3, 'bijaksana' => 3, 'adaptif' => 3],
        'AB+' => ['diplomat' => 3, 'filosofis' => 3, 'bijaksana' => 3, 'adaptif' => 3],
        'AB-' => ['diplomat' => 4, 'filosofis' => 4, 'bijaksana' => 4, 'adaptif' => 3],
        'O' => ['pemimpin' => 3, 'berani' => 3, 'motivator' => 3, 'komunikator' => 2],
        'O+' => ['pemimpin' => 3, 'berani' => 3, 'motivator' => 3, 'komunikator' => 2],
        'O-' => ['pemimpin' => 4, 'berani' => 4, 'motivator' => 3, 'komunikator' => 3],
    ];

    /**
     * Analisis karakter berdasarkan nama, tanggal lahir, dan golongan darah
     *
     * @param string $nama
     * @param string $tanggalLahir (format: Y-m-d)
     * @param string $golonganDarah
     * @return array
     */
    public function analyze(string $nama, string $tanggalLahir, string $golonganDarah): array
    {
        // Hitung numerologi dari nama
        $namaNumber = $this->calculateNameNumerology($nama);

        // Hitung life path number dari tanggal lahir
        $lifePathNumber = $this->calculateLifePathNumber($tanggalLahir);

        // Get blood type traits
        $bloodTypeTraits = $this->getBloodTypeTraits($golonganDarah);

        // Kombinasikan untuk mendapatkan character type
        $characterTypeId = $this->determineCharacterType($namaNumber, $lifePathNumber, $bloodTypeTraits);

        // Get character details
        $characterType = self::CHARACTER_TYPES[$characterTypeId];

        // Calculate detailed scores
        $scores = $this->calculateDetailedScores($namaNumber, $lifePathNumber, $bloodTypeTraits);

        return [
            'character_type_id' => $characterTypeId,
            'character_name' => $characterType['nama'],
            'character_name_en' => $characterType['nama_en'],
            'description' => $characterType['deskripsi'],
            'strengths' => $characterType['kekuatan'],
            'development_areas' => $characterType['area_pengembangan'],
            'career_matches' => $characterType['karir_cocok'],
            'numerology' => [
                'name_number' => $namaNumber,
                'life_path_number' => $lifePathNumber,
            ],
            'blood_type_traits' => $bloodTypeTraits,
            'detailed_scores' => $scores,
            'analysis_date' => Carbon::now()->toDateString(),
        ];
    }

    /**
     * Hitung numerologi dari nama
     * A=1, B=2, C=3, ..., Z=26, kemudian reduce sampai 1-9
     */
    private function calculateNameNumerology(string $nama): int
    {
        $nama = strtoupper(preg_replace('/[^A-Za-z]/', '', $nama));
        $sum = 0;

        for ($i = 0; $i < strlen($nama); $i++) {
            $char = $nama[$i];
            $value = ord($char) - ord('A') + 1;
            $sum += $value;
        }

        // Reduce to single digit (1-9)
        return $this->reduceToSingleDigit($sum);
    }

    /**
     * Hitung life path number dari tanggal lahir
     */
    private function calculateLifePathNumber(string $tanggalLahir): int
    {
        $date = Carbon::parse($tanggalLahir);

        $day = $date->day;
        $month = $date->month;
        $year = $date->year;

        // Sum all digits
        $sum = $this->reduceToSingleDigit($day) +
               $this->reduceToSingleDigit($month) +
               $this->reduceToSingleDigit($year);

        return $this->reduceToSingleDigit($sum);
    }

    /**
     * Reduce number to single digit (1-9)
     */
    private function reduceToSingleDigit(int $number): int
    {
        while ($number > 9) {
            $sum = 0;
            while ($number > 0) {
                $sum += $number % 10;
                $number = intdiv($number, 10);
            }
            $number = $sum;
        }

        return max(1, $number); // Ensure minimum 1
    }

    /**
     * Get blood type traits
     */
    private function getBloodTypeTraits(string $golonganDarah): array
    {
        return self::BLOOD_TYPE_TRAITS[$golonganDarah] ?? self::BLOOD_TYPE_TRAITS['O'];
    }

    /**
     * Determine character type based on all inputs
     */
    private function determineCharacterType(int $namaNumber, int $lifePathNumber, array $bloodTypeTraits): int
    {
        // Kombinasi dari ketiga faktor
        $baseScore = ($namaNumber + $lifePathNumber) / 2;

        // Adjust based on blood type traits
        $traitSum = array_sum($bloodTypeTraits);
        $adjustment = ($traitSum % 12);

        // Calculate final character type (1-12)
        $characterType = (int) (($baseScore + $adjustment) % 12);

        // Ensure result is between 1-12
        return $characterType === 0 ? 12 : $characterType;
    }

    /**
     * Calculate detailed personality scores
     */
    private function calculateDetailedScores(int $namaNumber, int $lifePathNumber, array $bloodTypeTraits): array
    {
        return [
            'leadership' => $this->calculateScore($namaNumber, 1, $bloodTypeTraits, 'pemimpin'),
            'creativity' => $this->calculateScore($namaNumber, 3, $bloodTypeTraits, 'kreativ'),
            'analytical' => $this->calculateScore($lifePathNumber, 3, $bloodTypeTraits, 'analitis'),
            'communication' => $this->calculateScore($namaNumber, 5, $bloodTypeTraits, 'komunikator'),
            'empathy' => $this->calculateScore($lifePathNumber, 2, $bloodTypeTraits, 'harmoni'),
            'adaptability' => $this->calculateScore($lifePathNumber, 5, $bloodTypeTraits, 'adaptif'),
            'perfectionism' => $this->calculateScore($lifePathNumber, 1, $bloodTypeTraits, 'perfeksionis'),
            'courage' => $this->calculateScore($namaNumber, 7, $bloodTypeTraits, 'berani'),
            'wisdom' => $this->calculateScore($lifePathNumber, 7, $bloodTypeTraits, 'bijaksana'),
            'motivation' => $this->calculateScore($namaNumber, 1, $bloodTypeTraits, 'motivator'),
        ];
    }

    /**
     * Calculate individual score (0-100)
     */
    private function calculateScore(int $baseNumber, int $targetNumber, array $traits, string $traitKey): int
    {
        // Base score from numerology (0-100)
        $baseScore = 100 - (abs($baseNumber - $targetNumber) * 10);
        $baseScore = max(0, min(100, $baseScore));

        // Bonus from blood type traits
        $traitBonus = ($traits[$traitKey] ?? 0) * 5;

        // Final score
        $finalScore = min(100, $baseScore + $traitBonus);

        return (int) $finalScore;
    }

    /**
     * Get character type by ID
     */
    public function getCharacterType(int $id): ?array
    {
        return self::CHARACTER_TYPES[$id] ?? null;
    }

    /**
     * Get all character types
     */
    public function getAllCharacterTypes(): array
    {
        return self::CHARACTER_TYPES;
    }
}
