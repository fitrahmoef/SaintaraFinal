<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CharacterAnalysisService;
use App\Models\Customer;
use App\Models\CharacterType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CharacterAnalysisServiceTest extends TestCase
{
    use RefreshDatabase;

    private CharacterAnalysisService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CharacterAnalysisService();
        $this->seedCharacterTypes();
    }

    /**
     * Seed character types for testing
     */
    private function seedCharacterTypes(): void
    {
        $characterTypes = [
            ['name' => 'Pemikir Extrovert', 'code' => 'THINKING_EXTROVERT'],
            ['name' => 'Perasa Introvert', 'code' => 'FEELING_INTROVERT'],
            ['name' => 'Pemimpi Extrovert', 'code' => 'INTUITING_EXTROVERT'],
            ['name' => 'Pengamat Introvert', 'code' => 'SENSING_INTROVERT'],
            ['name' => 'Penggerak', 'code' => 'MOBILIZER'],
            ['name' => 'Perasa Extrovert', 'code' => 'FEELING_EXTROVERT'],
            ['name' => 'Pemikir Introvert', 'code' => 'THINKING_INTROVERT'],
            ['name' => 'Pengamat Extrovert', 'code' => 'SENSING_EXTROVERT'],
            ['name' => 'Pemimpi Introvert', 'code' => 'INTUITING_INTROVERT'],
        ];

        foreach ($characterTypes as $type) {
            CharacterType::create([
                'name' => $type['name'],
                'code' => $type['code'],
                'description' => 'Test description for ' . $type['name'],
                'strengths' => json_encode(['strength1', 'strength2']),
                'challenges' => json_encode(['challenge1', 'challenge2']),
                'communication_style' => 'Test communication style',
            ]);
        }
    }

    /**
     * Test Life Path Number calculation
     */
    public function test_life_path_number_calculation(): void
    {
        // Test Case 1: 1990-05-15
        // Day: 15 -> 1+5 = 6
        // Month: 5 -> 5
        // Year: 1990 -> 1+9+9+0 = 19 -> 1+9 = 10 -> 1+0 = 1
        // Total: 6+5+1 = 12 -> 1+2 = 3
        $customer1 = Customer::factory()->create([
            'tanggal_lahir' => '1990-05-15',
            'nama_lengkap' => 'John Doe',
            'nama_panggilan' => 'John',
            'golongan_darah' => 'O+',
            'jenis_kelamin' => 'pria',
        ]);

        $result1 = $this->service->calculateCharacterType($customer1, [], 1);

        $this->assertNotNull($result1['character_type']);
        $this->assertInstanceOf(CharacterType::class, $result1['character_type']);
        $this->assertArrayHasKey('numerology_number', $result1);
        $this->assertArrayHasKey('analysis', $result1);
        $this->assertArrayHasKey('numerologi', $result1['analysis']);
    }

    /**
     * Test Expression Number calculation from name
     */
    public function test_expression_number_from_name(): void
    {
        $customer = Customer::factory()->create([
            'tanggal_lahir' => '1985-08-20',
            'nama_lengkap' => 'AAAAA', // A=1, total=5, reduce to 5
            'nama_panggilan' => 'AAA',
            'golongan_darah' => 'A+',
            'jenis_kelamin' => 'wanita',
        ]);

        $result = $this->service->calculateCharacterType($customer, [], 1);

        // Should calculate successfully
        $this->assertNotNull($result['character_type']);
        $this->assertArrayHasKey('numerologi', $result['analysis']);
        $this->assertArrayHasKey('expression_number', $result['analysis']['numerologi']);
    }

    /**
     * Test blood type modifier effects
     */
    public function test_blood_type_modifiers(): void
    {
        $bloodTypes = ['A+', 'B+', 'AB+', 'O+'];

        foreach ($bloodTypes as $bloodType) {
            $customer = Customer::factory()->create([
                'tanggal_lahir' => '1990-01-01',
                'nama_lengkap' => 'Test User',
                'nama_panggilan' => 'Test',
                'golongan_darah' => $bloodType,
                'jenis_kelamin' => 'pria',
            ]);

            $result = $this->service->calculateCharacterType($customer, [], 1);

            // Each blood type should produce a valid result
            $this->assertNotNull($result['character_type']);
            $this->assertEquals($bloodType, $result['analysis']['modifiers']['blood_type']);
            $this->assertNotEmpty($result['analysis']['modifiers']['blood_type_influence']);
        }
    }

    /**
     * Test gender modifier effects
     */
    public function test_gender_modifiers(): void
    {
        $genders = ['pria', 'wanita'];

        foreach ($genders as $gender) {
            $customer = Customer::factory()->create([
                'tanggal_lahir' => '1990-01-01',
                'nama_lengkap' => 'Test User',
                'nama_panggilan' => 'Test',
                'golongan_darah' => 'O+',
                'jenis_kelamin' => $gender,
            ]);

            $result = $this->service->calculateCharacterType($customer, [], 1);

            // Each gender should produce a valid result
            $this->assertNotNull($result['character_type']);
            $this->assertEquals($gender, $result['analysis']['modifiers']['gender']);
        }
    }

    /**
     * Test character mapping to 9 types
     */
    public function test_character_mapping_to_nine_types(): void
    {
        $expectedCodes = [
            'THINKING_EXTROVERT',
            'FEELING_INTROVERT',
            'INTUITING_EXTROVERT',
            'SENSING_INTROVERT',
            'MOBILIZER',
            'FEELING_EXTROVERT',
            'THINKING_INTROVERT',
            'SENSING_EXTROVERT',
            'INTUITING_INTROVERT',
        ];

        $customer = Customer::factory()->create([
            'tanggal_lahir' => '1990-01-01',
            'nama_lengkap' => 'Test User',
            'nama_panggilan' => 'Test',
            'golongan_darah' => 'O+',
            'jenis_kelamin' => 'pria',
        ]);

        $result = $this->service->calculateCharacterType($customer, [], 1);

        // Result should be one of the 9 character types
        $this->assertContains($result['character_type']->code, $expectedCodes);
    }

    /**
     * Test with test answers - should produce analysis
     */
    public function test_with_test_answers(): void
    {
        $customer = Customer::factory()->create([
            'tanggal_lahir' => '1990-01-01',
            'nama_lengkap' => 'Test User',
            'nama_panggilan' => 'Test',
            'golongan_darah' => 'O+',
            'jenis_kelamin' => 'pria',
        ]);

        $answers = [
            ['question_id' => 1, 'answer' => 'A', 'score' => 8],
            ['question_id' => 2, 'answer' => 'B', 'score' => 7],
            ['question_id' => 3, 'answer' => 'C', 'score' => 9],
            ['question_id' => 4, 'answer' => 'A', 'score' => 6],
            ['question_id' => 5, 'answer' => 'D', 'score' => 8],
        ];

        $result = $this->service->calculateCharacterType($customer, $answers, 1);

        // Should calculate test score
        $this->assertGreaterThan(0, $result['score']);
        $this->assertEquals(5, $result['analysis']['total_jawaban']);
        $this->assertArrayHasKey('answer_pattern', $result['analysis']['modifiers']);
    }

    /**
     * Test detailed breakdown for Standar test (test_id = 2)
     */
    public function test_detailed_breakdown_standar_test(): void
    {
        $customer = Customer::factory()->create([
            'tanggal_lahir' => '1990-01-01',
            'nama_lengkap' => 'Test User',
            'nama_panggilan' => 'Test',
            'golongan_darah' => 'O+',
            'jenis_kelamin' => 'pria',
        ]);

        // Generate 25 answers for Standar test
        $answers = [];
        for ($i = 1; $i <= 25; $i++) {
            $answers[] = ['question_id' => $i, 'answer' => 'A', 'score' => rand(5, 10)];
        }

        $result = $this->service->calculateCharacterType($customer, $answers, 2);

        // Should have detailed breakdown
        $this->assertArrayHasKey('breakdown', $result['analysis']);
        $this->assertArrayHasKey('life_foundation', $result['analysis']['breakdown']);
        $this->assertArrayHasKey('social_relations', $result['analysis']['breakdown']);
        $this->assertArrayHasKey('career_potential', $result['analysis']['breakdown']);
        $this->assertArrayHasKey('learning_style', $result['analysis']['breakdown']);
        $this->assertArrayHasKey('practical_life', $result['analysis']['breakdown']);
    }

    /**
     * Test detailed breakdown for Premium test (test_id = 3)
     */
    public function test_detailed_breakdown_premium_test(): void
    {
        $customer = Customer::factory()->create([
            'tanggal_lahir' => '1990-01-01',
            'nama_lengkap' => 'Test User',
            'nama_panggilan' => 'Test',
            'golongan_darah' => 'O+',
            'jenis_kelamin' => 'pria',
        ]);

        // Generate 35 answers for Premium test
        $answers = [];
        for ($i = 1; $i <= 35; $i++) {
            $answers[] = ['question_id' => $i, 'answer' => 'A', 'score' => rand(5, 10)];
        }

        $result = $this->service->calculateCharacterType($customer, $answers, 3);

        // Should have extended breakdown (includes emotional_intelligence and spiritual_growth)
        $this->assertArrayHasKey('breakdown', $result['analysis']);
        $this->assertArrayHasKey('emotional_intelligence', $result['analysis']['breakdown']);
        $this->assertArrayHasKey('spiritual_growth', $result['analysis']['breakdown']);
    }

    /**
     * Test numerology number is always between 1-9
     */
    public function test_numerology_number_range(): void
    {
        // Test with multiple customers
        for ($i = 0; $i < 20; $i++) {
            $customer = Customer::factory()->create([
                'tanggal_lahir' => '1990-' . rand(1, 12) . '-' . rand(1, 28),
                'nama_lengkap' => 'Test User ' . $i,
                'nama_panggilan' => 'Test',
                'golongan_darah' => ['A+', 'B+', 'AB+', 'O+'][rand(0, 3)],
                'jenis_kelamin' => ['pria', 'wanita'][rand(0, 1)],
            ]);

            $result = $this->service->calculateCharacterType($customer, [], 1);

            // Numerology number must be between 1-9
            $this->assertGreaterThanOrEqual(1, $result['numerology_number']);
            $this->assertLessThanOrEqual(9, $result['numerology_number']);
        }
    }

    /**
     * Test interpretation is provided for all numbers
     */
    public function test_interpretation_always_provided(): void
    {
        $customer = Customer::factory()->create([
            'tanggal_lahir' => '1990-01-01',
            'nama_lengkap' => 'Test User',
            'nama_panggilan' => 'Test',
            'golongan_darah' => 'O+',
            'jenis_kelamin' => 'pria',
        ]);

        $result = $this->service->calculateCharacterType($customer, [], 1);

        // Should have interpretation
        $this->assertArrayHasKey('interpretasi', $result['analysis']);
        $this->assertArrayHasKey('essence', $result['analysis']['interpretasi']);
        $this->assertArrayHasKey('traits', $result['analysis']['interpretasi']);
        $this->assertArrayHasKey('strength', $result['analysis']['interpretasi']);
        $this->assertArrayHasKey('challenge', $result['analysis']['interpretasi']);
    }

    /**
     * Test handles missing optional fields gracefully
     */
    public function test_handles_missing_optional_fields(): void
    {
        $customer = Customer::factory()->create([
            'tanggal_lahir' => '1990-01-01',
            'nama_lengkap' => 'Test User',
            'nama_panggilan' => 'Test',
            'golongan_darah' => null, // Missing blood type
            'jenis_kelamin' => null, // Missing gender
        ]);

        $result = $this->service->calculateCharacterType($customer, [], 1);

        // Should still produce valid result
        $this->assertNotNull($result['character_type']);
        $this->assertArrayHasKey('numerology_number', $result);
    }
}
