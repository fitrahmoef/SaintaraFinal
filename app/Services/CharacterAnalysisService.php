<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CharacterType;
use Carbon\Carbon;

/**
 * Character Analysis Service - Algoritma Numerologi Saintara
 *
 * Menggunakan kombinasi dari:
 * 1. Life Path Number (dari tanggal lahir)
 * 2. Expression Number (dari nama)
 * 3. Blood Type Modifier (golongan darah)
 * 4. Gender Modifier (jenis kelamin)
 * 5. Test Answers Analysis (jawaban tes)
 */
class CharacterAnalysisService
{
    // Mapping angka ke karakter
    private const CHARACTER_MAPPING = [
        1 => 'THINKING_EXTROVERT',    // Pemikir Extrovert (Leader)
        2 => 'FEELING_INTROVERT',     // Perasa Introvert (Diplomat)
        3 => 'INTUITING_EXTROVERT',   // Pemimpi Extrovert (Enthusiast)
        4 => 'SENSING_INTROVERT',     // Pengamat Introvert (Practical)
        5 => 'MOBILIZER',             // Penggerak (Dynamic)
        6 => 'FEELING_EXTROVERT',     // Perasa Extrovert (Nurturer)
        7 => 'THINKING_INTROVERT',    // Pemikir Introvert (Analyst)
        8 => 'SENSING_EXTROVERT',     // Pengamat Extrovert (Action)
        9 => 'INTUITING_INTROVERT',   // Pemimpi Introvert (Visionary)
    ];

    // Letter to number mapping untuk Expression Number
    private const LETTER_VALUES = [
        'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5,
        'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 1,
        'K' => 2, 'L' => 3, 'M' => 4, 'N' => 5, 'O' => 6,
        'P' => 7, 'Q' => 8, 'R' => 9, 'S' => 1, 'T' => 2,
        'U' => 3, 'V' => 4, 'W' => 5, 'X' => 6, 'Y' => 7, 'Z' => 8,
    ];

    /**
     * Calculate character type based on customer data and test answers
     */
    public function calculateCharacterType(Customer $customer, array $jawaban, int $testId): array
    {
        // 1. Life Path Number (35% weight) - dari tanggal lahir
        $lifePathNumber = $this->calculateLifePathNumber($customer->tanggal_lahir);

        // 2. Expression Number (25% weight) - dari nama lengkap
        $expressionNumber = $this->calculateExpressionNumber($customer->nama_lengkap);

        // 3. Soul Urge Number (15% weight) - dari nama panggilan
        $soulUrgeNumber = $this->calculateExpressionNumber($customer->nama_panggilan);

        // 4. Test Score (25% weight) - dari jawaban tes
        $testScore = $this->calculateTestScore($jawaban);
        $testNumber = $this->normalizeToNumerology($testScore);

        // Weighted calculation
        $baseNumber = round(
            ($lifePathNumber * 0.35) +
            ($expressionNumber * 0.25) +
            ($soulUrgeNumber * 0.15) +
            ($testNumber * 0.25)
        );

        // 5. Apply modifiers
        $modifiedNumber = $this->applyModifiers(
            $baseNumber,
            $customer->golongan_darah,
            $customer->jenis_kelamin,
            $jawaban
        );

        // Normalize to 1-9
        $finalNumber = $this->normalizeToNumerology($modifiedNumber);

        // Get character type
        $characterCode = self::CHARACTER_MAPPING[$finalNumber];
        $characterType = CharacterType::where('code', $characterCode)->first();

        // Generate detailed analysis
        $analysis = $this->generateDetailedAnalysis([
            'life_path' => $lifePathNumber,
            'expression' => $expressionNumber,
            'soul_urge' => $soulUrgeNumber,
            'test_number' => $testNumber,
            'base_number' => $baseNumber,
            'modified_number' => $modifiedNumber,
            'final_number' => $finalNumber,
            'blood_type' => $customer->golongan_darah,
            'gender' => $customer->jenis_kelamin,
            'answers' => $jawaban,
            'test_id' => $testId,
        ]);

        return [
            'character_type' => $characterType,
            'numerology_number' => $finalNumber,
            'score' => $testScore,
            'analysis' => $analysis,
        ];
    }

    /**
     * Calculate Life Path Number from birth date
     * Metode: Reduce tanggal, bulan, tahun secara terpisah lalu jumlahkan
     */
    private function calculateLifePathNumber(string $birthDate): int
    {
        $date = Carbon::parse($birthDate);

        $day = $this->reduceToSingleDigit($date->day);
        $month = $this->reduceToSingleDigit($date->month);
        $year = $this->reduceToSingleDigit($date->year);

        // Jumlahkan dan reduce lagi
        $total = $day + $month + $year;

        return $this->reduceToSingleDigit($total);
    }

    /**
     * Calculate Expression Number from name
     * Setiap huruf memiliki nilai numerologi
     */
    private function calculateExpressionNumber(string $name): int
    {
        $name = strtoupper(preg_replace('/[^A-Z]/i', '', $name));
        $sum = 0;

        for ($i = 0; $i < strlen($name); $i++) {
            $letter = $name[$i];
            $sum += self::LETTER_VALUES[$letter] ?? 0;
        }

        return $this->reduceToSingleDigit($sum);
    }

    /**
     * Reduce number to single digit (1-9)
     * Master numbers (11, 22, 33) dapat dipertahankan jika perlu
     */
    private function reduceToSingleDigit(int $number): int
    {
        while ($number > 9) {
            $digits = str_split((string)$number);
            $number = array_sum($digits);
        }

        return $number;
    }

    /**
     * Calculate test score from answers
     */
    private function calculateTestScore(array $jawaban): int
    {
        $totalScore = 0;

        foreach ($jawaban as $answer) {
            if (is_array($answer) && isset($answer['score'])) {
                $totalScore += (int)$answer['score'];
            } elseif (is_numeric($answer)) {
                $totalScore += (int)$answer;
            }
        }

        return $totalScore;
    }

    /**
     * Normalize any number to 1-9 range
     */
    private function normalizeToNumerology(int $number): int
    {
        if ($number <= 0) return 1;
        if ($number <= 9) return $number;

        return $this->reduceToSingleDigit($number);
    }

    /**
     * Apply blood type and gender modifiers
     */
    private function applyModifiers(
        int $baseNumber,
        ?string $bloodType,
        ?string $gender,
        array $answers
    ): int {
        $modifiedNumber = $baseNumber;

        // Blood Type Modifier
        $bloodTypeModifier = $this->getBloodTypeModifier($bloodType);
        $modifiedNumber += $bloodTypeModifier;

        // Gender Modifier
        $genderModifier = $this->getGenderModifier($gender, $baseNumber);
        $modifiedNumber += $genderModifier;

        // Answer Pattern Modifier (untuk fine-tuning)
        $answerModifier = $this->getAnswerPatternModifier($answers);
        $modifiedNumber += $answerModifier;

        return $modifiedNumber;
    }

    /**
     * Get blood type modifier
     * Based on research tentang karakteristik golongan darah
     */
    private function getBloodTypeModifier(?string $bloodType): int
    {
        if (!$bloodType) return 0;

        $type = strtoupper(substr($bloodType, 0, strpos($bloodType, '+') ?: strpos($bloodType, '-') ?: strlen($bloodType)));

        switch ($type) {
            case 'A':
                // A: Perfeksionis, detail-oriented, introvert tendencies
                // Push towards: THINKING_INTROVERT (7), SENSING_INTROVERT (4)
                return -1; // Slight push to lower (introvert) numbers

            case 'B':
                // B: Kreatif, fleksibel, independent
                // Push towards: INTUITING types (3, 9)
                return 1; // Slight push to creative numbers

            case 'AB':
                // AB: Rasional, unique, balanced
                // Push towards: THINKING types (1, 7)
                return 0; // Balanced, no strong push

            case 'O':
                // O: Leadership, confident, extrovert tendencies
                // Push towards: MOBILIZER (5), SENSING_EXTROVERT (8)
                return 2; // Push to action/leader numbers

            default:
                return 0;
        }
    }

    /**
     * Get gender modifier
     * Subtle adjustment based on gender and current number
     */
    private function getGenderModifier(?string $gender, int $currentNumber): int
    {
        if (!$gender) return 0;

        // Modifier sangat subtle untuk menghormati kesetaraan gender
        // Hanya slight tendency based on psychological research

        if (strtolower($gender) === 'pria') {
            // Slight tendency towards thinking/action: 1, 5, 7, 8
            if (in_array($currentNumber, [2, 6])) {
                return 1; // Slight push away from extreme feeling
            }
        } elseif (strtolower($gender) === 'wanita') {
            // Slight tendency towards feeling/intuiting: 2, 3, 6, 9
            if (in_array($currentNumber, [4, 8])) {
                return 1; // Slight push away from extreme sensing
            }
        }

        return 0;
    }

    /**
     * Analyze answer patterns for additional modifier
     */
    private function getAnswerPatternModifier(array $answers): int
    {
        if (empty($answers)) return 0;

        $scores = [];
        foreach ($answers as $answer) {
            if (is_array($answer) && isset($answer['score'])) {
                $scores[] = (int)$answer['score'];
            } elseif (is_numeric($answer)) {
                $scores[] = (int)$answer;
            }
        }

        if (empty($scores)) return 0;

        // Analyze variance in answers
        $mean = array_sum($scores) / count($scores);
        $variance = 0;
        foreach ($scores as $score) {
            $variance += pow($score - $mean, 2);
        }
        $variance /= count($scores);

        // High variance = more dynamic/flexible personality
        // Low variance = more consistent/stable personality

        if ($variance > 10) {
            return 1; // Push towards dynamic types (5, 8)
        } elseif ($variance < 2) {
            return -1; // Push towards stable types (4, 7)
        }

        return 0;
    }

    /**
     * Generate detailed analysis based on numerology calculations
     */
    private function generateDetailedAnalysis(array $data): array
    {
        $analysis = [
            'numerologi' => [
                'life_path_number' => $data['life_path'],
                'expression_number' => $data['expression'],
                'soul_urge_number' => $data['soul_urge'],
                'final_number' => $data['final_number'],
            ],
            'perhitungan' => [
                'base_calculation' => $data['base_number'],
                'with_modifiers' => $data['modified_number'],
                'normalized' => $data['final_number'],
            ],
            'modifiers' => [
                'blood_type' => $data['blood_type'],
                'blood_type_influence' => $this->getBloodTypeInfluence($data['blood_type']),
                'gender' => $data['gender'],
                'answer_pattern' => $this->getAnswerPatternDescription($data['answers']),
            ],
            'interpretasi' => $this->getNumberInterpretation($data['final_number']),
            'skor_tes' => $this->calculateTestScore($data['answers']),
            'total_jawaban' => count($data['answers']),
        ];

        // Add detailed breakdown based on test type
        if ($data['test_id'] == 2 || $data['test_id'] == 3) { // Standar or Premium
            $analysis['breakdown'] = $this->getDetailedBreakdown($data['answers'], $data['test_id']);
        }

        return $analysis;
    }

    /**
     * Get blood type influence description
     */
    private function getBloodTypeInfluence(?string $bloodType): string
    {
        if (!$bloodType) return 'Tidak ada pengaruh golongan darah';

        $type = strtoupper(substr($bloodType, 0, strpos($bloodType, '+') ?: strpos($bloodType, '-') ?: strlen($bloodType)));

        $influences = [
            'A' => 'Perfeksionis dan detail-oriented. Cenderung analitis dan terorganisir.',
            'B' => 'Kreatif dan fleksibel. Memiliki pemikiran out-of-the-box dan adaptif.',
            'AB' => 'Rasional dan unik. Kombinasi pemikiran logis dengan intuisi kreatif.',
            'O' => 'Pragmatis dan natural leader. Berorientasi pada hasil dan tindakan.',
        ];

        return $influences[$type] ?? 'Pengaruh golongan darah tidak teridentifikasi';
    }

    /**
     * Get answer pattern description
     */
    private function getAnswerPatternDescription(array $answers): string
    {
        $scores = [];
        foreach ($answers as $answer) {
            if (is_array($answer) && isset($answer['score'])) {
                $scores[] = (int)$answer['score'];
            } elseif (is_numeric($answer)) {
                $scores[] = (int)$answer;
            }
        }

        if (empty($scores)) return 'Tidak ada pola jawaban';

        $mean = array_sum($scores) / count($scores);
        $variance = 0;
        foreach ($scores as $score) {
            $variance += pow($score - $mean, 2);
        }
        $variance /= count($scores);

        if ($variance > 10) {
            return 'Pola jawaban dinamis dan bervariasi, menunjukkan kepribadian yang fleksibel dan adaptif.';
        } elseif ($variance < 2) {
            return 'Pola jawaban konsisten dan stabil, menunjukkan kepribadian yang teratur dan dapat diprediksi.';
        } else {
            return 'Pola jawaban seimbang antara konsistensi dan fleksibilitas.';
        }
    }

    /**
     * Get interpretation of final numerology number
     */
    private function getNumberInterpretation(int $number): array
    {
        $interpretations = [
            1 => [
                'essence' => 'Kepemimpinan dan Pemikiran Strategis',
                'traits' => 'Independent, pemikir strategis, natural leader, inovatif',
                'strength' => 'Kemampuan menganalisis situasi kompleks dan membuat keputusan strategis',
                'challenge' => 'Kadang terlalu fokus pada logika dan kurang mempertimbangkan aspek emosional',
            ],
            2 => [
                'essence' => 'Empati dan Diplomasi',
                'traits' => 'Empatik, sensitif, mediator, idealis',
                'strength' => 'Kemampuan memahami perasaan orang lain dan menciptakan harmoni',
                'challenge' => 'Kadang terlalu sensitif terhadap kritik dan konflik',
            ],
            3 => [
                'essence' => 'Kreativitas dan Antusiasme',
                'traits' => 'Kreatif, antusias, inspiratif, ekspresif',
                'strength' => 'Kemampuan berpikir out-of-the-box dan menginspirasi orang lain',
                'challenge' => 'Kadang terlalu idealis dan kurang fokus pada detail praktis',
            ],
            4 => [
                'essence' => 'Kepraktisan dan Konsistensi',
                'traits' => 'Praktis, detail-oriented, konsisten, dapat diandalkan',
                'strength' => 'Kemampuan memperhatikan detail dan bekerja dengan sistematis',
                'challenge' => 'Kadang terlalu kaku dan resisten terhadap perubahan',
            ],
            5 => [
                'essence' => 'Dinamisme dan Kepemimpinan Aksi',
                'traits' => 'Dinamis, action-oriented, adaptif, energetik',
                'strength' => 'Kemampuan menggerakkan dan memotivasi orang untuk bertindak',
                'challenge' => 'Kadang terlalu impulsif dan kurang perencanaan jangka panjang',
            ],
            6 => [
                'essence' => 'Kepedulian dan Harmoni Sosial',
                'traits' => 'Peduli, harmonis, people-oriented, supportif',
                'strength' => 'Kemampuan menciptakan lingkungan yang hangat dan mendukung',
                'challenge' => 'Kadang terlalu mengutamakan kebutuhan orang lain dan mengabaikan diri sendiri',
            ],
            7 => [
                'essence' => 'Analisis dan Introspeksi',
                'traits' => 'Analitis, introspektif, mendalam, pencari kebenaran',
                'strength' => 'Kemampuan berpikir mendalam dan menemukan solusi kompleks',
                'challenge' => 'Kadang terlalu perfeksionis dan sulit untuk berkompromi',
            ],
            8 => [
                'essence' => 'Aksi dan Energi',
                'traits' => 'Aktif, energetik, praktis, hasil-oriented',
                'strength' => 'Kemampuan mengeksekusi rencana dengan cepat dan efektif',
                'challenge' => 'Kadang terlalu fokus pada hasil dan kurang mempertimbangkan proses',
            ],
            9 => [
                'essence' => 'Visi dan Imajinasi',
                'traits' => 'Visioner, imajinatif, konseptual, futuristik',
                'strength' => 'Kemampuan melihat gambaran besar dan menciptakan visi jangka panjang',
                'challenge' => 'Kadang terlalu abstrak dan sulit mengimplementasikan ide',
            ],
        ];

        return $interpretations[$number] ?? $interpretations[5];
    }

    /**
     * Get detailed breakdown for Standar/Premium tests
     */
    private function getDetailedBreakdown(array $answers, int $testId): array
    {
        $breakdown = [];

        if ($testId == 2) { // Standar - 25 items
            $breakdown['life_foundation'] = $this->analyzeQuestionGroup($answers, 0, 5);
            $breakdown['social_relations'] = $this->analyzeQuestionGroup($answers, 5, 5);
            $breakdown['career_potential'] = $this->analyzeQuestionGroup($answers, 10, 5);
            $breakdown['learning_style'] = $this->analyzeQuestionGroup($answers, 15, 5);
            $breakdown['practical_life'] = $this->analyzeQuestionGroup($answers, 20, 5);
        } elseif ($testId == 3) { // Premium - 35 items
            $breakdown['life_foundation'] = $this->analyzeQuestionGroup($answers, 0, 5);
            $breakdown['social_relations'] = $this->analyzeQuestionGroup($answers, 5, 5);
            $breakdown['career_potential'] = $this->analyzeQuestionGroup($answers, 10, 5);
            $breakdown['learning_style'] = $this->analyzeQuestionGroup($answers, 15, 5);
            $breakdown['practical_life'] = $this->analyzeQuestionGroup($answers, 20, 5);
            $breakdown['emotional_intelligence'] = $this->analyzeQuestionGroup($answers, 25, 5);
            $breakdown['spiritual_growth'] = $this->analyzeQuestionGroup($answers, 30, 5);
        }

        return $breakdown;
    }

    /**
     * Analyze a specific group of questions
     */
    private function analyzeQuestionGroup(array $answers, int $start, int $count): array
    {
        $groupAnswers = array_slice($answers, $start, $count);
        $scores = [];

        foreach ($groupAnswers as $answer) {
            if (is_array($answer) && isset($answer['score'])) {
                $scores[] = (int)$answer['score'];
            } elseif (is_numeric($answer)) {
                $scores[] = (int)$answer;
            }
        }

        if (empty($scores)) {
            return [
                'average_score' => 0,
                'total_score' => 0,
                'interpretation' => 'Tidak ada data'
            ];
        }

        $total = array_sum($scores);
        $average = $total / count($scores);

        return [
            'average_score' => round($average, 2),
            'total_score' => $total,
            'interpretation' => $this->getScoreInterpretation($average),
        ];
    }

    /**
     * Get score interpretation
     */
    private function getScoreInterpretation(float $average): string
    {
        if ($average >= 8) {
            return 'Sangat Tinggi - Area kekuatan utama Anda';
        } elseif ($average >= 6) {
            return 'Tinggi - Potensi yang sudah berkembang baik';
        } elseif ($average >= 4) {
            return 'Sedang - Area yang perlu pengembangan lebih lanjut';
        } else {
            return 'Rendah - Area yang memerlukan perhatian dan pembelajaran';
        }
    }
}
