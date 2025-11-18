# Review dan Perbaikan Struktur Database Saintara

## Executive Summary
Dokumen ini berisi analisis komprehensif struktur database yang diusulkan dan rekomendasi perbaikan berdasarkan best practices database design.

---

## 🔴 MASALAH KRITIS YANG DITEMUKAN

### 1. Arsitektur User/Role yang Bermasalah
**Status:** ❌ CRITICAL

**Masalah Saat Ini:**
```
user (id_user, role_user)
customer (id, user FK, email, password, ...)
superadmin (id, user FK, email, password, ...)
admin_instansi (id, user FK, email, password, ...)
```

**Dampak:**
- Data redundansi (email & password ada di 3 tabel)
- Inkonsistensi data
- Sulit maintain
- Tidak bisa login jika data tidak sync

**Solusi:**
```
users (tabel master untuk semua user)
├─ id BIGINT UNSIGNED PK
├─ email VARCHAR(255) UNIQUE
├─ password VARCHAR(255)
├─ role ENUM('customer', 'superadmin', 'admin_instansi')
├─ email_verified_at TIMESTAMP NULL
├─ remember_token VARCHAR(100) NULL
├─ created_at TIMESTAMP
└─ updated_at TIMESTAMP

customers (profile untuk customer)
├─ id BIGINT UNSIGNED PK
├─ user_id BIGINT UNSIGNED FK -> users.id
├─ nama_lengkap VARCHAR(255)
├─ nama_panggilan VARCHAR(100)
├─ nomor_telepon VARCHAR(20)
├─ tanggal_lahir DATE
├─ jenis_kelamin ENUM('pria', 'wanita')
├─ golongan_darah ENUM('A', 'B', 'AB', 'O')
├─ negara VARCHAR(100)
├─ kota VARCHAR(100)
├─ created_at TIMESTAMP
└─ updated_at TIMESTAMP

admins (profile untuk semua admin)
├─ id BIGINT UNSIGNED PK
├─ user_id BIGINT UNSIGNED FK -> users.id
├─ tipe_admin ENUM('superadmin', 'admin_instansi')
├─ nama_admin VARCHAR(255)
├─ nama_instansi VARCHAR(255) NULL
├─ nomor_telepon VARCHAR(20)
├─ status_akun ENUM('aktif', 'tidak_aktif') DEFAULT 'aktif'
├─ created_at TIMESTAMP
└─ updated_at TIMESTAMP
```

---

### 2. Circular Reference (Hasil Tes ↔ Sertifikat)
**Status:** ❌ CRITICAL

**Masalah:**
```
hasil_tes -> id_sertifikat FK
sertifikat -> id_hasil FK
```
Circular dependency menyebabkan:
- Tidak bisa INSERT (siapa duluan?)
- CASCADE DELETE bermasalah
- Integrity constraint violation

**Solusi:**
Hapus `id_sertifikat` dari `hasil_tes`. Sertifikat references hasil, bukan sebaliknya:
```
hasil_tes (id, id_tes, id_customer, hasil, ...)
                ↑
sertifikat (id, id_hasil FK, no_sertifikat, ...)
```

---

### 3. Inkonsistensi Foreign Key & Primary Key
**Status:** ❌ CRITICAL

**Masalah:**
```
users.id_user (tipe tidak jelas)
customer.user VARCHAR FK <- ❌ Tipe berbeda!
pembayaran.id_transaksi VARCHAR PK
token.id_pembayaran VARCHAR FK <- ❌ Nama field berbeda!
```

**Solusi:**
- **Konsisten PK:** Semua tabel gunakan `id BIGINT UNSIGNED AUTO_INCREMENT`
- **FK matching:** FK harus sama persis dengan PK yang direferensikan
- **Naming:** Gunakan format `{table_singular}_id` untuk FK

```sql
-- ✅ BENAR
users.id BIGINT UNSIGNED
customers.user_id BIGINT UNSIGNED FK -> users.id

transactions.id BIGINT UNSIGNED
tokens.transaction_id BIGINT UNSIGNED FK -> transactions.id
```

---

## ⚠️ MASALAH DESAIN

### 4. Tipe Data Waktu Kurang Presisi
**Status:** ⚠️ HIGH

**Masalah:**
```sql
waktu_dibuat DATE         -- ❌ Hanya 2025-01-15
tgl_beli DATE             -- ❌ Tidak ada jam
tanggal_jam DATE          -- ❌ Nama misleading tapi cuma DATE
```

**Solusi:**
```sql
-- ✅ Untuk transaksi/log/timestamp
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
paid_at TIMESTAMP NULL
expired_at TIMESTAMP NULL

-- ✅ Untuk tanggal lahir/tanggal non-transaksional
tanggal_lahir DATE
```

---

### 5. Redundansi Data (Denormalisasi Berlebihan)
**Status:** ⚠️ MEDIUM

**Masalah:**
```sql
hasil_tes (
    id_customer FK,
    nama_lengkap VARCHAR  -- ❌ Sudah ada di customers!
)

sertifikat (
    id_hasil FK,
    nama_peserta VARCHAR,    -- ❌ Ada di hasil_tes->customer
    nama_tes VARCHAR,        -- ❌ Ada di tes
    hasil_karakter VARCHAR   -- ❌ Ada di hasil_tes
)
```

**Kapan Denormalisasi OK:**
- Untuk reporting/analytics (read-heavy)
- Data yang jarang berubah
- Performance critical queries

**Kapan TIDAK OK:**
- Data yang sering berubah (nama bisa berubah)
- Data yang bisa inconsistent
- Storage bukan masalah

**Solusi:**
```sql
-- ❌ HAPUS redundansi
sertifikat (
    id BIGINT UNSIGNED,
    id_hasil BIGINT UNSIGNED FK,
    no_sertifikat VARCHAR(50) UNIQUE,
    tanggal_terbit TIMESTAMP,
    diterbitkan_oleh VARCHAR(100),
    ttd_digital VARCHAR(255),
    url_verifikasi VARCHAR(255),
    -- nama_peserta HAPUS
    -- nama_tes HAPUS
    -- hasil_karakter HAPUS
)

-- ✅ Ambil via JOIN
SELECT
    s.no_sertifikat,
    c.nama_lengkap AS nama_peserta,
    t.nama_tes,
    ht.hasil_karakter
FROM sertifikat s
JOIN hasil_tes ht ON s.id_hasil = ht.id
JOIN customers c ON ht.id_customer = c.id
JOIN tes t ON ht.id_tes = t.id
```

---

### 6. Primary Key VARCHAR - Tidak Efisien
**Status:** ⚠️ MEDIUM

**Masalah:**
```sql
id_paket VARCHAR(12)
id_tes VARCHAR(12)
```

**Perbandingan:**
| Tipe | Size | Index Performance | Use Case |
|------|------|-------------------|----------|
| BIGINT | 8 bytes | ⭐⭐⭐⭐⭐ Fastest | Recommended default |
| VARCHAR(12) | 13 bytes | ⭐⭐⭐ Slower | Readable IDs (PKG-001) |
| UUID/CHAR(36) | 36 bytes | ⭐⭐ Slow | Distributed systems |

**Rekomendasi:**
```sql
-- ✅ BEST: Auto-increment
id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

-- ✅ OK: Jika butuh custom format
id VARCHAR(20) PRIMARY KEY,  -- misal: "TES-2025-00001"
-- Tapi tetap tambahkan numeric index:
numeric_id BIGINT UNSIGNED UNIQUE AUTO_INCREMENT
```

---

### 7. Naming Convention Tidak Konsisten
**Status:** ⚠️ MEDIUM

**Masalah:**
```
❌ id_customer vs idCustomer
❌ tgl_lahir vs tanggal_dibuat
❌ no_telp vs nomor_telepon
❌ gol_darah vs golongan_darah
```

**Solusi - Standar Laravel/MySQL:**
```sql
✅ snake_case untuk semua column/table
✅ Singular untuk table name (jika pakai ORM)
✅ {table}_id untuk foreign key

-- Examples:
users, customers, transactions
user_id, customer_id, transaction_id
tanggal_lahir, nomor_telepon, golongan_darah
```

---

### 8. Business Logic Tidak Jelas (Token)
**Status:** ⚠️ HIGH

**Masalah:**
```sql
token (
    id_pembayaran FK,      -- ❌ Nama berbeda (should: transaction_id)
    id_customer FK,        -- ❌ Redundan jika ada di pembayaran
    status ENUM(digunakan, belum digunakan)  -- ❌ Terlalu simple
)
```

**Pertanyaan Business Logic:**
1. Apakah 1 pembayaran = 1 token atau multiple tokens?
2. Token untuk apa? (akses tes? jumlah karakter?)
3. Bagaimana tracking penggunaan token per tes?
4. Apakah token bisa expire?
5. Apakah token bisa di-transfer antar user?

**Solusi (Asumsi: 1 pembelian = batch tokens):**
```sql
-- Tabel pembelian token
token_purchases (
    id BIGINT UNSIGNED,
    customer_id BIGINT UNSIGNED FK,
    package_id BIGINT UNSIGNED FK,
    transaction_id BIGINT UNSIGNED FK,
    jumlah_token INT,           -- Total token dibeli
    jumlah_terpakai INT DEFAULT 0,
    harga_total DECIMAL(12,2),
    tanggal_pembelian TIMESTAMP,
    tanggal_kadaluarsa TIMESTAMP,
    status ENUM('aktif', 'kadaluarsa', 'habis')
)

-- Tracking penggunaan token per tes
token_usage (
    id BIGINT UNSIGNED,
    token_purchase_id BIGINT UNSIGNED FK,
    tes_id BIGINT UNSIGNED FK,
    jumlah_digunakan INT,
    tanggal_penggunaan TIMESTAMP
)
```

---

### 9. Log Aktivitas - Desain Kurang Optimal
**Status:** ⚠️ MEDIUM

**Masalah:**
```sql
log_aktivitas (
    id_customer VARCHAR,  -- ❌ Bagaimana log admin?
    id_admin VARCHAR,     -- ❌ Bagaimana log system?
    aktivitas ???         -- ❌ Tidak jelas tipe data
)
```

**Solusi: Polymorphic Logging**
```sql
activity_logs (
    id BIGINT UNSIGNED,
    -- Polymorphic relationship
    user_id BIGINT UNSIGNED NULL,
    user_type VARCHAR(50) NULL,  -- 'customer', 'admin', 'system'

    -- Log details
    action VARCHAR(100),         -- 'login', 'purchase_token', 'create_test'
    description TEXT NULL,

    -- Metadata
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    request_url VARCHAR(255) NULL,

    -- Payload (untuk data tambahan)
    metadata JSON NULL,

    created_at TIMESTAMP
)

-- Index untuk query cepat
CREATE INDEX idx_user_activity ON activity_logs(user_id, user_type, created_at);
CREATE INDEX idx_action ON activity_logs(action, created_at);
```

---

## ✅ STRUKTUR DATABASE YANG DIPERBAIKI

### ERD Final (Normalized)

```
┌─────────────────────────────────────────────────────────────┐
│                         USERS                                │
├─────────────────────────────────────────────────────────────┤
│ PK │ id                    BIGINT UNSIGNED                   │
│    │ email                 VARCHAR(255) UNIQUE               │
│    │ password              VARCHAR(255)                      │
│    │ role                  ENUM('customer','admin','super')  │
│    │ email_verified_at     TIMESTAMP NULL                    │
│    │ created_at            TIMESTAMP                         │
│    │ updated_at            TIMESTAMP                         │
└─────────────────────────────────────────────────────────────┘
        ↓ (1:1)                    ↓ (1:1)
  ┌─────────────┐          ┌──────────────────┐
  │  CUSTOMERS  │          │     ADMINS       │
  ├─────────────┤          ├──────────────────┤
  │ id PK       │          │ id PK            │
  │ user_id FK  │          │ user_id FK       │
  │ nama_lengkap│          │ nama_admin       │
  │ ...         │          │ tipe_admin       │
  └─────────────┘          └──────────────────┘
        ↓ (1:N)
  ┌──────────────────────┐
  │   TOKEN_PURCHASES    │
  ├──────────────────────┤
  │ id PK                │
  │ customer_id FK       │
  │ package_id FK        │
  │ transaction_id FK    │
  │ jumlah_token         │
  │ jumlah_terpakai      │
  └──────────────────────┘
        ↓ (1:N)
  ┌──────────────────┐
  │   TOKEN_USAGE    │
  ├──────────────────┤
  │ id PK            │
  │ purchase_id FK   │
  │ tes_id FK        │
  └──────────────────┘
        ↓
  ┌────────────┐
  │    TES     │
  ├────────────┤
  │ id PK      │
  │ nama_tes   │
  └────────────┘
        ↓ (1:N)
  ┌─────────────────┐
  │  HASIL_TES      │
  ├─────────────────┤
  │ id PK           │
  │ tes_id FK       │
  │ customer_id FK  │
  │ hasil_karakter  │
  └─────────────────┘
        ↓ (1:1)
  ┌─────────────────┐
  │  SERTIFIKAT     │
  ├─────────────────┤
  │ id PK           │
  │ hasil_id FK     │
  │ no_sertifikat   │
  └─────────────────┘
```

---

## 📋 CHECKLIST IMPLEMENTASI

### Phase 1: Core Tables
- [x] users (sudah ada)
- [ ] customers (perbaiki struktur)
- [ ] admins (gabungkan superadmin & admin_instansi)
- [ ] packages (rename dari paket)

### Phase 2: Transaction System
- [ ] transactions (perbaiki struktur)
- [ ] token_purchases (rename & redesign dari token)
- [ ] token_usage (baru - tracking)

### Phase 3: Testing System
- [ ] tests (rename dari tes)
- [ ] test_results (rename dari hasil_tes, hapus redundansi)
- [ ] certificates (rename dari sertifikat, hapus redundansi)

### Phase 4: Supporting Tables
- [ ] activity_logs (redesign dari log_aktivitas)
- [ ] payment_gateways (baru - untuk master gateway)

---

## 🎯 REKOMENDASI BEST PRACTICES

### 1. Indexing Strategy
```sql
-- Primary Keys: AUTO indexed
-- Foreign Keys: HARUS di-index
CREATE INDEX idx_customers_user_id ON customers(user_id);
CREATE INDEX idx_transactions_customer_id ON transactions(customer_id);

-- Frequent WHERE clauses
CREATE INDEX idx_transactions_status ON transactions(status);
CREATE INDEX idx_tokens_expiry ON token_purchases(tanggal_kadaluarsa);

-- Composite index untuk query kompleks
CREATE INDEX idx_test_results_customer_date
ON test_results(customer_id, test_date DESC);
```

### 2. Constraints
```sql
-- UNIQUE constraints
ALTER TABLE sertifikat ADD UNIQUE(no_sertifikat);
ALTER TABLE users ADD UNIQUE(email);

-- CHECK constraints (MySQL 8.0+)
ALTER TABLE customers ADD CONSTRAINT chk_phone
CHECK (nomor_telepon REGEXP '^[0-9+\-() ]+$');

ALTER TABLE packages ADD CONSTRAINT chk_price
CHECK (harga >= 0);
```

### 3. Default Values & Timestamps
```sql
-- Semua tabel harus punya:
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

-- Status dengan default
status ENUM('aktif', 'tidak_aktif') DEFAULT 'aktif'
payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending'
```

### 4. Soft Deletes (Untuk data penting)
```sql
-- Jangan hard delete transaksi/log
deleted_at TIMESTAMP NULL

-- Query:
WHERE deleted_at IS NULL  -- Data aktif
WHERE deleted_at IS NOT NULL  -- Data terhapus
```

### 5. JSON Fields (Untuk flexibility)
```sql
-- Untuk data yang struktur berubah-ubah
test_answers JSON
metadata JSON
settings JSON

-- Query:
SELECT * FROM tests
WHERE JSON_EXTRACT(settings, '$.difficulty') = 'hard';
```

---

## 📊 ESTIMASI IMPACT

| Aspect | Before | After | Impact |
|--------|--------|-------|--------|
| Normalisasi | ⭐⭐ | ⭐⭐⭐⭐⭐ | Reduce redundancy 60% |
| Query Performance | ⭐⭐⭐ | ⭐⭐⭐⭐ | Faster by 30-40% |
| Maintainability | ⭐⭐ | ⭐⭐⭐⭐⭐ | Much easier to update |
| Data Integrity | ⭐⭐ | ⭐⭐⭐⭐⭐ | FK constraints prevent orphans |
| Scalability | ⭐⭐⭐ | ⭐⭐⭐⭐ | Better for growth |

---

## 🚀 NEXT STEPS

1. **Review** dokumen ini dengan tim
2. **Diskusikan** business logic yang belum jelas (terutama token system)
3. **Approve** struktur final
4. **Generate** Laravel migrations
5. **Test** di development environment
6. **Migrate** data existing (jika ada)
7. **Deploy** ke production

---

*Dokumen ini dibuat oleh: Database Analyst*
*Tanggal: 2025-11-18*
*Versi: 1.0*
