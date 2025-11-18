# ERD Database Saintara (Final - Corrected)

## Database Schema Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                            USERS                                 │
├─────────────────────────────────────────────────────────────────┤
│ PK │ id                      BIGINT UNSIGNED                     │
│    │ name                    VARCHAR(255)                        │
│    │ email                   VARCHAR(255) UNIQUE                 │
│    │ password                VARCHAR(255)                        │
│    │ role                    ENUM('customer','admin','super')    │
│    │ user_type               ENUM('personal','admin','instansi') │
│    │ email_verified_at       TIMESTAMP NULL                      │
│    │ created_at              TIMESTAMP                           │
│    │ updated_at              TIMESTAMP                           │
└─────────────────────────────────────────────────────────────────┘
          │                                │
          │ 1:1                            │ 1:1
          ↓                                ↓
┌─────────────────────────────┐  ┌──────────────────────────────┐
│        CUSTOMERS             │  │          ADMINS              │
├─────────────────────────────┤  ├──────────────────────────────┤
│ PK │ id                     │  │ PK │ id                      │
│ FK │ user_id                │  │ FK │ user_id                 │
│    │ nama_lengkap           │  │    │ tipe_admin              │
│    │ nama_panggilan         │  │    │ nama_admin              │
│    │ nomor_telepon          │  │    │ nama_instansi           │
│    │ tanggal_lahir          │  │    │ nomor_telepon           │
│    │ jenis_kelamin          │  │    │ status_akun             │
│    │ golongan_darah         │  │    │ created_at              │
│    │ negara                 │  │    │ updated_at              │
│    │ kota                   │  │    │ deleted_at              │
│    │ created_at             │  └──────────────────────────────┘
│    │ updated_at             │
│    │ deleted_at             │
└─────────────────────────────┘
          │ 1:N
          ↓
┌──────────────────────────────────────────────────────────┐
│                    TOKEN_PURCHASES                        │
├──────────────────────────────────────────────────────────┤
│ PK │ id                                                   │
│ FK │ customer_id          -> customers.id                │
│ FK │ transaction_id       -> transactions.id             │
│ FK │ package_id           -> packages.id                 │
│    │ kode_token           VARCHAR(50) UNIQUE             │
│    │ jumlah_token         INT                            │
│    │ jumlah_terpakai      INT DEFAULT 0                  │
│    │ jumlah_tersisa       VIRTUAL (computed)             │
│    │ status               ENUM('aktif','habis',...)      │
│    │ tanggal_pembelian    TIMESTAMP                      │
│    │ tanggal_kadaluarsa   TIMESTAMP                      │
│    │ created_at           TIMESTAMP                      │
│    │ updated_at           TIMESTAMP                      │
│    │ deleted_at           TIMESTAMP NULL                 │
└──────────────────────────────────────────────────────────┘
          │ 1:N                              ↑
          ↓                                  │ N:1
┌─────────────────────────────┐    ┌────────────────────────────┐
│      TOKEN_USAGE            │    │      TRANSACTIONS          │
├─────────────────────────────┤    ├────────────────────────────┤
│ PK │ id                     │    │ PK │ id                    │
│ FK │ token_purchase_id      │    │ FK │ customer_id           │
│ FK │ test_result_id         │    │ FK │ package_id            │
│    │ jumlah_digunakan       │    │ FK │ payment_gateway_id    │
│    │ keterangan             │    │    │ kode_transaksi UNIQUE │
│    │ tanggal_penggunaan     │    │    │ jumlah_bayar          │
│    │ created_at             │    │    │ status_pembayaran     │
│    │ updated_at             │    │    │ metode_pembayaran     │
└─────────────────────────────┘    │    │ gateway_transaction_id│
          │                         │    │ payment_url           │
          │                         │    │ payment_metadata      │
          │ N:1                     │    │ waktu_dibuat          │
          ↓                         │    │ waktu_dibayar         │
┌───────────────────────────────┐  │    │ waktu_kadaluarsa      │
│       TEST_RESULTS            │  │    │ created_at            │
├───────────────────────────────┤  │    │ updated_at            │
│ PK │ id                       │  │    │ deleted_at            │
│ FK │ test_id                  │  └────────────────────────────┘
│ FK │ customer_id              │           ↑ N:1
│ FK │ token_purchase_id        │           │
│    │ hasil_karakter           │  ┌────────────────────────────┐
│    │ deskripsi_hasil          │  │        PACKAGES            │
│    │ skor                     │  ├────────────────────────────┤
│    │ jawaban JSON             │  │ PK │ id                    │
│    │ analisis JSON            │  │    │ nama_paket            │
│    │ tanggal_tes              │  │    │ harga                 │
│    │ waktu_mulai              │  │    │ deskripsi             │
│    │ waktu_selesai            │  │    │ tipe_paket            │
│    │ durasi_detik VIRTUAL     │  │    │ jumlah_token          │
│    │ ip_address               │  │    │ masa_aktif_hari       │
│    │ created_at               │  │    │ is_active             │
│    │ updated_at               │  │    │ created_at            │
│    │ deleted_at               │  │    │ updated_at            │
└───────────────────────────────┘  │    │ deleted_at            │
          │ N:1                    └────────────────────────────┘
          ↓                                 ↑ N:1
┌───────────────────────────────┐          │
│          TESTS                │  ┌───────────────────────────┐
├───────────────────────────────┤  │   PAYMENT_GATEWAYS        │
│ PK │ id                       │  ├───────────────────────────┤
│    │ nama_tes                 │  │ PK │ id                   │
│    │ deskripsi_tes            │  │    │ nama_gateway         │
│    │ jenis_tes                │  │    │ kode_gateway UNIQUE  │
│    │ jumlah_soal              │  │    │ logo_url             │
│    │ durasi_menit             │  │    │ is_active            │
│    │ token_required           │  │    │ config JSON          │
│    │ metadata JSON            │  │    │ created_at           │
│    │ is_active                │  │    │ updated_at           │
│    │ created_at               │  └───────────────────────────┘
│    │ updated_at               │
│    │ deleted_at               │
└───────────────────────────────┘
          ↑ N:1
          │
┌───────────────────────────────┐
│       CERTIFICATES            │
├───────────────────────────────┤
│ PK │ id                       │
│ FK │ test_result_id UNIQUE    │  ← One-to-One with test_results
│    │ nomor_sertifikat UNIQUE  │
│    │ diterbitkan_oleh         │
│    │ ttd_digital              │
│    │ url_verifikasi UNIQUE    │
│    │ format_file              │
│    │ file_path                │
│    │ is_active                │
│    │ tanggal_terbit           │
│    │ metadata JSON            │
│    │ created_at               │
│    │ updated_at               │
│    │ deleted_at               │
└───────────────────────────────┘

┌──────────────────────────────────────────────────────────┐
│                    ACTIVITY_LOGS                          │
├──────────────────────────────────────────────────────────┤
│ PK │ id                                                   │
│    │ user_id         (Polymorphic)                       │
│    │ user_type       (Polymorphic - Customer/Admin)      │
│    │ action          VARCHAR(100)                        │
│    │ description     TEXT                                │
│    │ module          VARCHAR(50)                         │
│    │ ip_address      VARCHAR(45)                         │
│    │ user_agent      TEXT                                │
│    │ request_url     VARCHAR                             │
│    │ request_method  VARCHAR(10)                         │
│    │ properties      JSON                                │
│    │ log_level       ENUM('info','warning','error',...)  │
│    │ created_at      TIMESTAMP                           │
└──────────────────────────────────────────────────────────┘
```

---

## Relasi Antar Tabel

### 1. Users → Customers (1:1)
- Satu user hanya bisa menjadi satu customer
- Customer profile terpisah dari credential

### 2. Users → Admins (1:1)
- Satu user bisa menjadi admin (superadmin atau admin_instansi)
- Tipe admin dibedakan dengan field `tipe_admin`

### 3. Customers → Token_Purchases (1:N)
- Satu customer bisa membeli token berkali-kali
- Setiap pembelian tercatat terpisah

### 4. Token_Purchases → Token_Usage (1:N)
- Satu pembelian token bisa digunakan untuk multiple tes
- Tracking penggunaan per tes

### 5. Token_Usage → Test_Results (N:1)
- Setiap tes result terhubung ke 1 token usage
- One-to-one (enforced by unique constraint)

### 6. Test_Results → Certificates (1:1)
- Satu hasil tes bisa generate satu sertifikat
- Tidak semua hasil tes punya sertifikat

### 7. Transactions → Token_Purchases (1:N)
- Satu transaksi bisa create multiple token purchases
- Biasanya 1:1, tapi support bundle di masa depan

### 8. Customers → Transactions (1:N)
- Customer bisa punya banyak transaksi
- History transaksi lengkap

### 9. Customers → Test_Results (1:N)
- Customer bisa ambil tes berkali-kali
- History hasil tes

### 10. Tests → Test_Results (1:N)
- Satu tes bisa diambil oleh banyak customer
- Master data tes vs instance hasil

---

## Data Flow: User Purchase → Take Test → Get Certificate

```
1. REGISTER/LOGIN
   User → (login) → Authentication

2. PURCHASE TOKEN
   Customer → pilih Package
   → Create Transaction (status: pending)
   → Payment Gateway → bayar
   → Update Transaction (status: dibayar)
   → Create Token_Purchase (aktif)

3. TAKE TEST
   Customer → pilih Test
   → Check Token_Purchase (tersisa > 0)
   → Create Test_Result (mulai tes)
   → Submit jawaban
   → Calculate skor
   → Update Test_Result (selesai)
   → Create Token_Usage
   → Update Token_Purchase (terpakai++)

4. GET CERTIFICATE
   Test_Result → (jika lulus/selesai)
   → Generate Certificate
   → Generate PDF
   → Generate verification URL
   → Send email

5. LOGGING
   Setiap action → Activity_Log
```

---

## Indexing Strategy

### High Priority Indexes (Performance Critical)

```sql
-- Foreign Keys (AUTO indexed by constraint)
customers.user_id
admins.user_id
transactions.customer_id, package_id
token_purchases.customer_id, transaction_id
test_results.customer_id, test_id
certificates.test_result_id

-- Frequently Queried
transactions.status_pembayaran
transactions.waktu_dibuat
token_purchases.status
token_purchases.tanggal_kadaluarsa
test_results.tanggal_tes
certificates.nomor_sertifikat (UNIQUE)

-- Composite Indexes
test_results(customer_id, test_id)
test_results(customer_id, tanggal_tes DESC)
activity_logs(user_type, user_id, created_at)
```

---

## Constraints & Business Rules

### Check Constraints

```sql
-- Financial
packages.harga >= 0
packages.jumlah_token > 0

-- Tokens
token_purchases.jumlah_terpakai >= 0
token_purchases.jumlah_terpakai <= jumlah_token
token_usage.jumlah_digunakan > 0

-- Tests
tests.jumlah_soal >= 0
tests.durasi_menit > 0
```

### Unique Constraints

```sql
users.email
transactions.kode_transaksi
token_purchases.kode_token
certificates.nomor_sertifikat
certificates.url_verifikasi
certificates.test_result_id  -- One cert per result
token_usage.test_result_id   -- One usage per result
payment_gateways.kode_gateway
```

### Cascade Rules

```sql
-- ON DELETE CASCADE (hapus child saat parent dihapus)
users → customers (cascade)
users → admins (cascade)
customers → transactions (cascade)
transactions → token_purchases (cascade)
tests → test_results (cascade)
test_results → certificates (cascade)
test_results → token_usage (cascade)

-- ON DELETE RESTRICT (tidak bisa hapus jika masih ada child)
packages → transactions (restrict)
packages → token_purchases (restrict)

-- ON DELETE SET NULL (set null di child saat parent dihapus)
payment_gateways → transactions (set null)
token_purchases → test_results (set null - untuk archive)
```

---

## Soft Deletes

Tables yang support soft delete (deleted_at):

- ✅ customers
- ✅ admins
- ✅ packages
- ✅ transactions
- ✅ token_purchases
- ✅ tests
- ✅ test_results
- ✅ certificates
- ❌ activity_logs (permanent log)
- ❌ token_usage (permanent log)
- ❌ payment_gateways (master data)

---

## Security Considerations

### 1. Password Storage
```php
// NEVER store plain password
// Use bcrypt/argon2
$hashed = Hash::make($password);
```

### 2. Sensitive Data
```sql
-- Encrypt di application level:
- customers.nomor_telepon
- transactions.payment_metadata
- certificates.ttd_digital
```

### 3. PII (Personal Identifiable Information)
```sql
-- Data yang butuh protection:
- email
- nomor_telepon
- tanggal_lahir
- nama_lengkap
```

### 4. Audit Trail
```sql
-- activity_logs must capture:
- Who (user_id, user_type)
- What (action)
- When (created_at)
- Where (ip_address)
- How (request_method, request_url)
```

---

## Scalability Notes

### Partitioning Strategy (untuk future)

```sql
-- Partition by date for large tables
activity_logs: PARTITION BY RANGE (YEAR(created_at))
transactions: PARTITION BY RANGE (YEAR(waktu_dibuat))
test_results: PARTITION BY RANGE (YEAR(tanggal_tes))
```

### Caching Strategy

```
- Cache master data: packages, tests, payment_gateways
- Cache user profile: customers, admins (invalidate on update)
- Don't cache: transactions, test_results (transactional data)
```

### Archive Strategy

```sql
-- Move old data to archive tables after X years
activity_logs → activity_logs_archive (> 2 years)
test_results → test_results_archive (> 5 years)
transactions → transactions_archive (> 7 years - tax requirement)
```

---

## Migration Order

Run migrations in this exact order:

1. ✅ users (already exists)
2. customers
3. admins
4. packages
5. payment_gateways
6. transactions
7. token_purchases
8. tests
9. test_results
10. token_usage
11. certificates
12. activity_logs

**IMPORTANT:** Foreign key constraints require parent tables to exist first!

---

## Sample Queries

### Get Customer Full Profile with Stats
```sql
SELECT
    u.email,
    c.nama_lengkap,
    COUNT(DISTINCT tr.id) as total_transaksi,
    SUM(tp.jumlah_token) as total_token_dibeli,
    SUM(tp.jumlah_terpakai) as total_token_terpakai,
    COUNT(DISTINCT tes.id) as total_tes_diambil,
    COUNT(DISTINCT cert.id) as total_sertifikat
FROM users u
JOIN customers c ON u.id = c.user_id
LEFT JOIN transactions tr ON c.id = tr.customer_id AND tr.status_pembayaran = 'dibayar'
LEFT JOIN token_purchases tp ON c.id = tp.customer_id
LEFT JOIN test_results tes ON c.id = tes.customer_id
LEFT JOIN certificates cert ON tes.id = cert.test_result_id
WHERE u.id = ?
GROUP BY u.id;
```

### Get Active Tokens for Customer
```sql
SELECT
    tp.*,
    p.nama_paket,
    (tp.jumlah_token - tp.jumlah_terpakai) as sisa_token,
    DATEDIFF(tp.tanggal_kadaluarsa, NOW()) as hari_tersisa
FROM token_purchases tp
JOIN packages p ON tp.package_id = p.id
WHERE tp.customer_id = ?
  AND tp.status = 'aktif'
  AND tp.tanggal_kadaluarsa > NOW()
  AND (tp.jumlah_token - tp.jumlah_terpakai) > 0
ORDER BY tp.tanggal_kadaluarsa ASC;
```

### Get Test History with Certificate Status
```sql
SELECT
    t.nama_tes,
    tr.hasil_karakter,
    tr.skor,
    tr.tanggal_tes,
    CASE
        WHEN c.id IS NOT NULL THEN 'Ada Sertifikat'
        ELSE 'Belum Ada'
    END as status_sertifikat,
    c.nomor_sertifikat,
    c.url_verifikasi
FROM test_results tr
JOIN tests t ON tr.test_id = t.id
LEFT JOIN certificates c ON tr.id = c.test_result_id
WHERE tr.customer_id = ?
ORDER BY tr.tanggal_tes DESC;
```

### Transaction Report (Admin)
```sql
SELECT
    DATE(tr.waktu_dibuat) as tanggal,
    COUNT(*) as total_transaksi,
    SUM(CASE WHEN tr.status_pembayaran = 'dibayar' THEN 1 ELSE 0 END) as berhasil,
    SUM(CASE WHEN tr.status_pembayaran = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN tr.status_pembayaran = 'gagal' THEN 1 ELSE 0 END) as gagal,
    SUM(CASE WHEN tr.status_pembayaran = 'dibayar' THEN tr.jumlah_bayar ELSE 0 END) as total_revenue
FROM transactions tr
WHERE tr.waktu_dibuat BETWEEN ? AND ?
GROUP BY DATE(tr.waktu_dibuat)
ORDER BY tanggal DESC;
```

---

*ERD Final - Updated 2025-11-18*
*Database: saintara_db*
*DBMS: MySQL 8.0+*
