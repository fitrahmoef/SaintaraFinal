# Algoritma Numerologi Platform Saintara

## Overview

Platform Saintara menggunakan algoritma numerologi yang komprehensif untuk menganalisis karakter personal berdasarkan kombinasi dari data pribadi dan hasil tes. Algoritma ini menggabungkan prinsip-prinsip numerologi klasik dengan modifikasi modern untuk menghasilkan 9 tipe karakter yang unik.

## Komponen Algoritma

### 1. Life Path Number (Bobot: 35%)

**Sumber:** Tanggal lahir customer

**Metode Perhitungan:**
```
1. Pisahkan hari, bulan, dan tahun
2. Reduce masing-masing komponen menjadi single digit (1-9)
3. Jumlahkan ketiga komponen
4. Reduce hasil penjumlahan menjadi single digit (1-9)
```

**Contoh:**
```
Tanggal lahir: 1990-05-15

Day: 15 → 1+5 = 6
Month: 05 → 0+5 = 5
Year: 1990 → 1+9+9+0 = 19 → 1+9 = 10 → 1+0 = 1

Life Path Number = 6+5+1 = 12 → 1+2 = 3
```

**Makna:**
- Angka ini merepresentasikan jalur hidup dan tujuan dasar dari individu
- Mencerminkan karakteristik bawaan sejak lahir
- Pengaruh terbesar (35%) dalam perhitungan final

---

### 2. Expression Number (Bobot: 25%)

**Sumber:** Nama lengkap customer

**Metode Perhitungan:**
```
1. Konversi setiap huruf ke angka menggunakan mapping:
   A=1, B=2, C=3, D=4, E=5, F=6, G=7, H=8, I=9
   J=1, K=2, L=3, M=4, N=5, O=6, P=7, Q=8, R=9
   S=1, T=2, U=3, V=4, W=5, X=6, Y=7, Z=8

2. Jumlahkan semua nilai huruf
3. Reduce menjadi single digit (1-9)
```

**Contoh:**
```
Nama: JOHN DOE

J=1, O=6, H=8, N=5, D=4, O=6, E=5

Total = 1+6+8+5+4+6+5 = 35 → 3+5 = 8
Expression Number = 8
```

**Makna:**
- Merepresentasikan potensi dan bakat natural
- Mencerminkan bagaimana seseorang mengekspresikan diri
- Pengaruh kedua terbesar (25%)

---

### 3. Soul Urge Number (Bobot: 15%)

**Sumber:** Nama panggilan customer

**Metode Perhitungan:**
- Sama dengan Expression Number, tapi menggunakan nama panggilan

**Makna:**
- Merepresentasikan keinginan dan motivasi inner
- Mencerminkan apa yang benar-benar penting bagi individu
- Pengaruh moderate (15%)

---

### 4. Test Number (Bobot: 25%)

**Sumber:** Jawaban tes customer

**Metode Perhitungan:**
```
1. Hitung total score dari semua jawaban
2. Normalize score ke range 1-9
```

**Contoh:**
```
Jawaban: [8, 7, 9, 6, 8, 7, 10, 5, 9, 8]
Total Score = 77

Normalize:
77 → 7+7 = 14 → 1+4 = 5
Test Number = 5
```

**Makna:**
- Merepresentasikan kondisi dan preferensi saat ini
- Mencerminkan hasil self-assessment aktual
- Pengaruh significant (25%)

---

### 5. Modifiers (Fine-tuning)

#### A. Blood Type Modifier

**Berbasis penelitian karakteristik golongan darah:**

| Golongan Darah | Modifier | Karakteristik | Pengaruh |
|----------------|----------|---------------|----------|
| **A** | -1 | Perfeksionis, detail-oriented, introvert tendencies | Push ke karakter introvert (4, 7) |
| **B** | +1 | Kreatif, fleksibel, independent | Push ke karakter intuitive (3, 9) |
| **AB** | 0 | Rasional, unique, balanced | Netral, tidak ada push |
| **O** | +2 | Leadership, confident, extrovert | Push ke karakter action/leader (5, 8) |

#### B. Gender Modifier

**Subtle adjustment berdasar psychological tendencies:**

- **Pria:**
  - Jika hasil awal di karakter extreme feeling (2, 6): +1
  - Slight tendency ke thinking/action

- **Wanita:**
  - Jika hasil awal di karakter extreme sensing (4, 8): +1
  - Slight tendency ke feeling/intuiting

**Catatan:** Modifier sangat subtle untuk menghormati kesetaraan gender

#### C. Answer Pattern Modifier

**Analisis variance dari jawaban:**

```
Variance > 10 = High variance
  → Kepribadian dinamis/fleksibel
  → Modifier: +1 (push ke tipe dynamic: 5, 8)

Variance < 2 = Low variance
  → Kepribadian konsisten/stable
  → Modifier: -1 (push ke tipe stable: 4, 7)

Otherwise = Balanced
  → Modifier: 0
```

---

## Formula Perhitungan Final

### Step 1: Base Calculation (Weighted Average)

```
Base Number = (Life Path × 0.35) + (Expression × 0.25) + (Soul Urge × 0.15) + (Test Number × 0.25)
```

### Step 2: Apply Modifiers

```
Modified Number = Base Number + Blood Type Modifier + Gender Modifier + Answer Pattern Modifier
```

### Step 3: Normalize to 1-9

```
Final Number = Reduce(Modified Number) to single digit (1-9)
```

### Step 4: Map to Character Type

```
Character Mapping:
1 → THINKING_EXTROVERT (Pemikir Extrovert)
2 → FEELING_INTROVERT (Perasa Introvert)
3 → INTUITING_EXTROVERT (Pemimpi Extrovert)
4 → SENSING_INTROVERT (Pengamat Introvert)
5 → MOBILIZER (Penggerak)
6 → FEELING_EXTROVERT (Perasa Extrovert)
7 → THINKING_INTROVERT (Pemikir Introvert)
8 → SENSING_EXTROVERT (Pengamat Extrovert)
9 → INTUITING_INTROVERT (Pemimpi Introvert)
```

---

## Contoh Perhitungan Lengkap

### Input Data:
```
Nama Lengkap: BUDI SANTOSO
Nama Panggilan: BUDI
Tanggal Lahir: 1990-08-17
Golongan Darah: O+
Jenis Kelamin: Pria
Jawaban Tes: [8, 7, 9, 6, 8, 7, 10, 5, 9, 8] (Total: 77)
```

### Perhitungan:

**1. Life Path Number:**
```
Day: 17 → 1+7 = 8
Month: 08 → 0+8 = 8
Year: 1990 → 1+9+9+0 = 19 → 1+9 = 10 → 1+0 = 1

Life Path = 8+8+1 = 17 → 1+7 = 8
```

**2. Expression Number (BUDI SANTOSO):**
```
B=2, U=3, D=4, I=9, S=1, A=1, N=5, T=2, O=6, S=1, O=6

Total = 2+3+4+9+1+1+5+2+6+1+6 = 40 → 4+0 = 4
Expression = 4
```

**3. Soul Urge Number (BUDI):**
```
B=2, U=3, D=4, I=9

Total = 2+3+4+9 = 18 → 1+8 = 9
Soul Urge = 9
```

**4. Test Number:**
```
Total Score = 77 → 7+7 = 14 → 1+4 = 5
Test Number = 5
```

**5. Base Calculation:**
```
Base = (8 × 0.35) + (4 × 0.25) + (9 × 0.15) + (5 × 0.25)
Base = 2.8 + 1.0 + 1.35 + 1.25
Base = 6.4 ≈ 6 (rounded)
```

**6. Apply Modifiers:**
```
Blood Type (O): +2
Gender (Pria, base=6): 0 (tidak ada push karena base bukan 2 atau 6)
Answer Pattern (variance moderate): 0

Modified = 6 + 2 + 0 + 0 = 8
```

**7. Final Number:**
```
Final = 8 (already in 1-9 range)
```

**8. Character Mapping:**
```
8 → SENSING_EXTROVERT (Pengamat Extrovert)
```

### Hasil:
```json
{
  "character_type": {
    "name": "Pengamat Extrovert",
    "code": "SENSING_EXTROVERT",
    "description": "Energetik dan berorientasi pada aksi..."
  },
  "numerology_number": 8,
  "score": 77,
  "analysis": {
    "numerologi": {
      "life_path_number": 8,
      "expression_number": 4,
      "soul_urge_number": 9,
      "final_number": 8
    },
    "perhitungan": {
      "base_calculation": 6,
      "with_modifiers": 8,
      "normalized": 8
    },
    "modifiers": {
      "blood_type": "O+",
      "blood_type_influence": "Pragmatis dan natural leader...",
      "gender": "pria",
      "answer_pattern": "Pola jawaban seimbang..."
    },
    "interpretasi": {
      "essence": "Aksi dan Energi",
      "traits": "Aktif, energetik, praktis, hasil-oriented",
      "strength": "Kemampuan mengeksekusi rencana dengan cepat dan efektif",
      "challenge": "Kadang terlalu fokus pada hasil dan kurang mempertimbangkan proses"
    }
  }
}
```

---

## 9 Tipe Karakter Saintara

### 1. THINKING_EXTROVERT (Pemikir Extrovert)
- **Essence:** Kepemimpinan dan Pemikiran Strategis
- **Traits:** Independent, pemikir strategis, natural leader, inovatif
- **Strength:** Kemampuan menganalisis dan membuat keputusan strategis
- **Challenge:** Terlalu fokus pada logika, kurang aspek emosional

### 2. FEELING_INTROVERT (Perasa Introvert)
- **Essence:** Empati dan Diplomasi
- **Traits:** Empatik, sensitif, mediator, idealis
- **Strength:** Memahami perasaan orang lain dan menciptakan harmoni
- **Challenge:** Terlalu sensitif terhadap kritik

### 3. INTUITING_EXTROVERT (Pemimpi Extrovert)
- **Essence:** Kreativitas dan Antusiasme
- **Traits:** Kreatif, antusias, inspiratif, ekspresif
- **Strength:** Berpikir out-of-the-box dan menginspirasi
- **Challenge:** Terlalu idealis, kurang fokus pada detail praktis

### 4. SENSING_INTROVERT (Pengamat Introvert)
- **Essence:** Kepraktisan dan Konsistensi
- **Traits:** Praktis, detail-oriented, konsisten, dapat diandalkan
- **Strength:** Memperhatikan detail dan bekerja sistematis
- **Challenge:** Terlalu kaku dan resisten terhadap perubahan

### 5. MOBILIZER (Penggerak)
- **Essence:** Dinamisme dan Kepemimpinan Aksi
- **Traits:** Dinamis, action-oriented, adaptif, energetik
- **Strength:** Menggerakkan dan memotivasi untuk bertindak
- **Challenge:** Terlalu impulsif, kurang perencanaan jangka panjang

### 6. FEELING_EXTROVERT (Perasa Extrovert)
- **Essence:** Kepedulian dan Harmoni Sosial
- **Traits:** Peduli, harmonis, people-oriented, supportif
- **Strength:** Menciptakan lingkungan hangat dan mendukung
- **Challenge:** Terlalu mengutamakan orang lain, mengabaikan diri sendiri

### 7. THINKING_INTROVERT (Pemikir Introvert)
- **Essence:** Analisis dan Introspeksi
- **Traits:** Analitis, introspektif, mendalam, pencari kebenaran
- **Strength:** Berpikir mendalam dan menemukan solusi kompleks
- **Challenge:** Terlalu perfeksionis, sulit berkompromi

### 8. SENSING_EXTROVERT (Pengamat Extrovert)
- **Essence:** Aksi dan Energi
- **Traits:** Aktif, energetik, praktis, hasil-oriented
- **Strength:** Mengeksekusi rencana dengan cepat dan efektif
- **Challenge:** Terlalu fokus pada hasil, kurang mempertimbangkan proses

### 9. INTUITING_INTROVERT (Pemimpi Introvert)
- **Essence:** Visi dan Imajinasi
- **Traits:** Visioner, imajinatif, konseptual, futuristik
- **Strength:** Melihat gambaran besar dan menciptakan visi jangka panjang
- **Challenge:** Terlalu abstrak, sulit mengimplementasikan ide

---

## Detail Analisis Berdasarkan Tipe Tes

### Tes Dasar (10 soal)
- Analisis umum karakter
- Numerologi dasar
- Skor dan kategori

### Tes Standar (25 soal)
Breakdown analisis:
1. **Life Foundation** (soal 1-5) - Dasar kepribadian
2. **Social Relations** (soal 6-10) - Hubungan sosial
3. **Career Potential** (soal 11-15) - Potensi karir
4. **Learning Style** (soal 16-20) - Gaya belajar
5. **Practical Life** (soal 21-25) - Kehidupan praktis

### Tes Premium (35 soal)
Semua breakdown Standar, plus:
6. **Emotional Intelligence** (soal 26-30) - Kecerdasan emosional
7. **Spiritual Growth** (soal 31-35) - Pertumbuhan spiritual

---

## Validasi dan Quality Assurance

### Test Cases:
1. ✅ Life Path Number calculation accuracy
2. ✅ Expression Number from various names
3. ✅ Blood type modifier effects (A, B, AB, O)
4. ✅ Gender modifier effects
5. ✅ Answer pattern analysis (high/low variance)
6. ✅ Character mapping to 9 types
7. ✅ Numerology number range (always 1-9)
8. ✅ Detailed breakdown for Standar/Premium tests
9. ✅ Handles missing optional fields gracefully
10. ✅ Interpretation provided for all numbers

### Unit Tests:
- Location: `/tests/Unit/CharacterAnalysisServiceTest.php`
- Coverage: 12 comprehensive test methods
- Run: `php artisan test --filter=CharacterAnalysisServiceTest`

---

## Implementasi

### Service Class:
**File:** `/app/Services/CharacterAnalysisService.php`

**Main Method:**
```php
public function calculateCharacterType(
    Customer $customer,
    array $jawaban,
    int $testId
): array
```

**Returns:**
```php
[
    'character_type' => CharacterType,  // Model instance
    'numerology_number' => int,         // 1-9
    'score' => int,                     // Total test score
    'analysis' => array,                // Detailed analysis
]
```

### Controller Integration:
**File:** `/app/Http/Controllers/Personal/TestController.php`

**Usage:**
```php
$analysisService = new CharacterAnalysisService();
$result = $analysisService->calculateCharacterType($customer, $jawaban, $testId);
```

---

## Referensi

### Numerologi Klasik:
- Pythagorean Numerology System
- Life Path Number calculation
- Expression Number (Destiny Number)
- Soul Urge Number (Heart's Desire)

### Modern Adaptations:
- Blood type personality research (popularized in Japan & Korea)
- Gender psychology tendencies
- Behavioral pattern analysis

### Saintara Character System:
- 9 unique character types
- Balanced between Eastern and Western psychology
- Practical application for personal development

---

## Version History

- **v1.0.0** (2025-11-18): Initial implementation
  - Complete numerology algorithm
  - 9 character type mapping
  - Blood type & gender modifiers
  - Comprehensive test coverage
  - Detailed analysis generation

---

## Maintainers

- Development Branch: `claude/numerology-placeholder-algorithm-01TimFgYZ3ebMqnNvKiKzn3Q`
- Service: `CharacterAnalysisService`
- Tests: `CharacterAnalysisServiceTest`

---

**Catatan:** Algoritma ini adalah sistem proprietary platform Saintara dan dirancang khusus untuk memberikan insight karakter personal yang akurat dan actionable berdasarkan prinsip numerologi yang telah disesuaikan dengan konteks modern.
