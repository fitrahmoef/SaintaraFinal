# SAINTARA APPLICATION - COMPREHENSIVE DATABASE SCHEMA ANALYSIS

## Executive Summary
The Saintara application is a comprehensive character/personality test platform with multi-user roles (personal, admin, instansi), token-based payment system, and test management features. However, **there are significant gaps between the frontend/backend implementation and the database schema** that need immediate resolution.

---

## SECTION 1: FRONTEND PAGES & FEATURES DISCOVERED

### 1.1 Authentication Pages (7 pages)
- `/login` - User login
- `/register` - User registration  
- `/forgot-password` - Password recovery
- `/reset-password` - Password reset
- `/verify-email` - Email verification
- `/confirm-password` - Password confirmation
- `/two-factor-challenge` - Two-factor authentication

### 1.2 Personal User Pages (10 pages)
- `/personal/dashboard` - Personal dashboard
- `/personal/profile` - User profile management
- `/personal/daftar-tes` - Test registration/listing
- `/personal/form-tes-personal` - Test form/questionnaire
- `/personal/results` - Test results view
- `/personal/transaksi-token` - Token purchase & transaction history
- `/personal/hadiah-donasi` - Rewards/donations
- `/personal/bantuan` - Help/support
- `/personal/setting` - Settings

### 1.3 Admin Pages (8 pages)
- `/admin/dashboard-admin` - Admin dashboard
- `/admin/profile` - Admin profile
- `/admin/agenda` - Event/agenda management (Talkshow, Webinar, Seminar, Workshop)
- `/admin/pengguna` - User management
- `/admin/keuangan` - Financial reports (Income, Transactions)
- `/admin/tim` - Team management (Attendance, Work reports)
- `/admin/bantuan` - Support/help
- `/admin/settings` - System settings

### 1.4 Instansi (Institution) Pages (2 pages)
- `/instansi/dashboard` - Institution dashboard
- `/instansi/form-tes-instansi` - Batch test submission

### 1.5 Shared Pages (3 pages)
- `/landing` - Landing page
- `/calendar` - Calendar view
- Settings: appearance, password, profile, two-factor

---

## SECTION 2: BACKEND CONTROLLERS & API ROUTES

### 2.1 Personal User Controllers
**Personal/TokenController.php** - Token management
- `index()` - Get token balance and transactions
- `packages()` - Get available packages
- `purchase()` - Purchase tokens via Midtrans
- `balance()` - Get token balance

**Personal/TestController.php** - Test management
- `index()` - List available tests
- `show($id)` - Get test details with questions
- `submit()` - Submit test answers
- `submitBatch()` - Batch submission (Instansi)
- `results()` - Get user's test results
- `resultDetail($id)` - Get specific result

**Personal/CertificateController.php** - Certificate management
- `download($id)` - Download certificate
- `view($id)` - View certificate
- `downloadTestResult($id)` - Download test result
- `verify($nomor_sertifikat)` - Public certificate verification

**Personal/PersonalDashboardController.php** - Dashboard data

### 2.2 Admin Controllers
**Admin/AdminDashboardController.php** - Dashboard stats
- `index()` - Get dashboard statistics

**Admin/UserManagementController.php** - User management
- `index()` - List users with filtering
- `store()` - Create user
- `show($id)` - Get user details
- `update($id)` - Update user
- `destroy($id)` - Delete user
- `stats()` - User statistics

### 2.3 Instansi Controllers
**Instansi/InstansiDashboardController.php** - Institution dashboard
- `index()` - Get institution dashboard with test statistics

### 2.4 Payment Controller
**PaymentController.php** - Payment processing
- `handleNotification()` - Midtrans webhook callback
- `getTransactionStatus()` - Get transaction status
- `cancelTransaction()` - Cancel transaction
- `paymentSuccess()` - Payment success page
- `paymentError()` - Payment error page

---

## SECTION 3: IMPLEMENTED MODELS (15 Total)

| Model | Purpose | Key Fields |
|-------|---------|-----------|
| User | Authentication & base user | name, email, password, user_type (personal/admin/instansi) |
| Customer | Customer profile | user_id, nama_lengkap, tanggal_lahir, jenis_kelamin, golongan_darah |
| Package | Token packages | nama_paket, harga, jumlah_token, masa_aktif_hari |
| TokenPurchase | Token purchases (NEW) | customer_id, transaction_id, kode_token, jumlah_token |
| Token | Legacy token system | user_id, package_type, token_amount (DEPRECATED?) |
| Transaction | Payment transactions | customer_id, package_id, kode_transaksi, status_pembayaran |
| PaymentGateway | Payment gateways | nama_gateway, kode_gateway, config |
| Test | Test definitions | nama_tes, jenis_tes, jumlah_soal, durasi_menit |
| TestQuestion | Test questions | test_id, nomor_soal, pertanyaan, tipe_soal |
| TestResult | Test completion | test_id, customer_id, token_purchase_id, hasil_karakter, skor |
| TokenUsage | Token usage tracking | token_purchase_id, test_result_id |
| Certificate | Test certificates | test_result_id, nomor_sertifikat, url_verifikasi |
| Agenda | Events/agendas | title, type, start_time, status |
| Team | Internal team management | name, department, position, salary |
| CharacterType | Character types | name, code, description, strengths, challenges |

---

## SECTION 4: DATABASE SCHEMA ANALYSIS

### 4.1 Actual Database Tables (from migrations)
1. **users** - User authentication
2. **customers** - Customer profiles
3. **super_admins** - Superadmin profiles (added via migration)
4. **admin_instansi** - Institution admin profiles (added via migration)
5. **character_types** - Character types reference
6. **agendas** - Event/agenda management
7. **tokens** - Legacy token table (renamed to old_tokens by migration)
8. **teams** - Internal team staff
9. **packages** - Token packages
10. **payment_gateways** - Payment gateway definitions
11. **transactions** - Payment transactions
12. **token_purchases** - Token purchase records
13. **tests** - Test definitions
14. **test_questions** - Test questions
15. **test_results** - Test completion results
16. **token_usage** - Token usage tracking
17. **certificates** - Test certificates
18. **activity_logs** - System activity logging

### 4.2 Table Discrepancies with database_schema.sql

The **database_schema.sql file is OUT OF DATE** with these issues:

**Missing Tables (in schema.sql but exist in migrations):**
- `super_admins`
- `admin_instansi`
- `character_types`
- `agendas`
- `teams`
- `test_questions`

**Extra table in schema.sql (not in actual migrations):**
- `admins` - Should be `super_admins` and `admin_instansi`

**Inaccurate table definitions in schema.sql:**
- `activity_logs` uses `nullableMorphs()` in migration but `user_id` and `user_type` defined separately in schema.sql

---

## SECTION 5: CRITICAL GAPS & MISMATCHES

### 5.1 Missing Database Fields (Referenced in Code)
Controllers are querying fields that **DO NOT EXIST** in the database schema:

| Missing Field | Table | Referenced In | Impact |
|---|---|---|---|
| `test_type` | test_results | AdminDashboardController, InstansiDashboardController | Cannot filter tests by type (personal/instansi/gift) |
| `character_type_id` | test_results | InstansiDashboardController | Cannot group test results by character type |
| `institution_name` | test_results | InstansiDashboardController | Cannot retrieve institution name |
| `test_date` | test_results | AdminDashboardController | Using wrong field name (actual: `tanggal_tes`) |
| `user_id` | test_results | InstansiDashboardController | Using wrong relationship (actual: `customer_id`) |

### 5.2 Dual Token System Conflict
**CRITICAL ISSUE:** Application has TWO token systems:

1. **Legacy Token Model/Table**
   - Direct relationship: `User → Token`
   - Fields: user_id, package_type, token_amount, price, payment_status, etc.
   - Migration: `2025_11_18_053701_create_tokens_table.php`

2. **New TokenPurchase Model/Table**
   - Relationship: `Customer → Transaction → TokenPurchase`
   - Fields: customer_id, transaction_id, kode_token, jumlah_token, etc.
   - Migration: `2025_11_18_100005_create_token_purchases_table.php`

**Status:** The migration renames old_tokens to preserve legacy data, but:
- Controllers use `TokenPurchase` (correct)
- User model still references `Token.php` model
- AdminDashboardController queries: `Token::whereMonth(...)->where('payment_status', 'paid')`
- This will fail because `Token` table is renamed to `old_tokens`

### 5.3 Missing Relationship Fields in TestResult
TestResult model is missing fields needed for the InstansiDashboardController:

```php
// Controller expects:
TestResult::where('test_type', 'instansi')
            ->where('user_id', $user->id)
            ->groupBy('character_type_id')
            ->with('characterType')
```

**But TestResult actually has:**
- `test_id`, `customer_id`, `token_purchase_id` (NOT user_id)
- `hasil_karakter` (string result, NOT character_type_id FK)
- No test_type or institution_name fields

---

## SECTION 6: FEATURE-TO-DATABASE MAPPING

### Feature: User Authentication & Roles
**Expected Tables:** users, roles, role_user
**Actual Tables:** users (with user_type enum: personal/admin/instansi) ✓
**Status:** PARTIALLY IMPLEMENTED
**Gap:** No `role` column in users (has `user_type` instead). Schema.sql shows `role` enum but migration doesn't create it.

### Feature: Customer Registration & Profiles
**Expected Tables:** customers, customer_profiles
**Actual Tables:** customers ✓
**Status:** IMPLEMENTED ✓
**Missing Fields:** None identified

### Feature: Token Management
**Expected Tables:** token_purchases, token_packages, token_usage
**Actual Tables:** token_purchases ✓, packages ✓, token_usage ✓, tokens (legacy)
**Status:** PARTIALLY IMPLEMENTED
**Gaps:**
- Two token systems causing confusion
- Old `tokens` table conflicts with new `token_purchases`
- Need to fully migrate from Token to TokenPurchase

### Feature: Payment Processing
**Expected Tables:** transactions, payment_gateways
**Actual Tables:** transactions ✓, payment_gateways ✓
**Status:** IMPLEMENTED ✓
**Integration:** Midtrans integrated via MidtransService

### Feature: Test Management
**Expected Tables:** tests, test_questions, test_results
**Actual Tables:** tests ✓, test_questions ✓, test_results ✓
**Status:** IMPLEMENTED ✓
**Missing Fields:**
- test_results missing `test_type` (personal/instansi/gift)
- test_results missing `character_type_id` for linking to character types
- test_results missing `institution_name` for instansi tests

### Feature: Character Types
**Expected Tables:** character_types
**Actual Tables:** character_types ✓
**Status:** IMPLEMENTED ✓
**Fields:** name, code, description, strengths, challenges, communication_style, image_path

### Feature: Certificate Generation
**Expected Tables:** certificates
**Actual Tables:** certificates ✓
**Status:** IMPLEMENTED ✓
**Fields:** nomor_sertifikat, diterbitkan_oleh, url_verifikasi, format_file

### Feature: Activity Logging
**Expected Tables:** activity_logs
**Actual Tables:** activity_logs ✓
**Status:** IMPLEMENTED ✓
**Note:** Uses polymorphic relationship (user_type + user_id)

### Feature: Team Management (Admin)
**Expected Tables:** teams
**Actual Tables:** teams ✓
**Status:** IMPLEMENTED ✓
**Fields:** name, email, department, position, salary, commission, status, join_date

### Feature: Event/Agenda Management
**Expected Tables:** agendas
**Actual Tables:** agendas ✓
**Status:** IMPLEMENTED ✓
**Types:** talkshow, webinar, seminar, workshop

### Feature: Institution/Instansi Management
**Expected Tables:** admin_instansi, instansi_profile
**Actual Tables:** admin_instansi ✓
**Status:** IMPLEMENTED ✓
**Fields:** nama_admin, nama_instansi, alamat_instansi, status_akun, tanggal_bergabung

### Feature: Admin Dashboard & Reporting
**Expected Tables:** Many (for aggregated data)
**Status:** PARTIALLY BROKEN
**Issues:**
- AdminDashboardController queries non-existent fields
- TestResult filtering by test_type fails
- Token sales query uses old Token table (now old_tokens)
- User relationship queries incorrect field

---

## SECTION 7: CRITICAL ISSUES SUMMARY

### 🔴 CRITICAL (Must Fix Immediately)

1. **Database Schema Mismatch**
   - database_schema.sql is outdated and doesn't match migrations
   - Missing tables: super_admins, admin_instansi, character_types, agendas, teams, test_questions
   - Extra table definition: admins (should be split into super_admins and admin_instansi)

2. **Broken InstansiDashboardController**
   - Queries non-existent fields: test_type, character_type_id, institution_name, user_id
   - Uses wrong relationship (User instead of Customer)
   - Will cause runtime errors when accessed

3. **Broken AdminDashboardController**
   - Queries non-existent field: test_date (should be tanggal_tes)
   - Queries non-existent field: test_type
   - Queries old Token table (renamed to old_tokens)
   - Dashboard stats will fail

4. **Dual Token System**
   - Two different token implementations (Token and TokenPurchase)
   - Old Token table renamed to old_tokens but still referenced in controllers
   - Risk of data inconsistency and failed queries

### 🟠 HIGH PRIORITY (Should Fix Soon)

5. **Missing Fields in TestResult Table**
   - Need `test_type` enum (personal/instansi/gift)
   - Need `character_type_id` foreign key
   - Need `institution_name` text field or join with admin_instansi

6. **Incomplete Relationship Configuration**
   - TestResult should have optional relationship to character_types
   - TestResult should have optional relationship to admin_instansi

7. **Missing User Type Profiles**
   - While super_admins and admin_instansi exist, they're not fully connected in all controllers
   - No complete relationship chain: User → SuperAdmin/AdminInstansi/Customer

### 🟡 MEDIUM PRIORITY (Nice to Have)

8. **Activity Logging Polymorphic Relationship**
   - Current schema uses simple user_id/user_type columns
   - Migration uses `nullableMorphs()` which creates different column names
   - Could cause logging failures

9. **Certificate Public Verification**
   - Current implementation assumes unique url_verifikasi
   - No rate limiting or CSRF protection for public endpoint

---

## SECTION 8: RECOMMENDATIONS

### Phase 1: Immediate Fixes (1-2 days)
1. **Update database_schema.sql** to match actual migrations
2. **Fix InstansiDashboardController:**
   - Replace test_type queries with proper joins
   - Change user_id to customer_id
   - Add character_type relationship to TestResult model
   - Add institution_name field to test_results table OR join with admin_instansi

3. **Fix AdminDashboardController:**
   - Replace test_date with tanggal_tes
   - Add test_type field to test_results table
   - Update Token queries to use TokenPurchase model

4. **Remove Legacy Token References:**
   - Update User model to remove hasMany('tokens') relationship
   - Archive old_tokens table safely
   - Verify all test data is in token_purchases

### Phase 2: Schema Enhancement (2-3 days)
1. **Create migration to add TestResult fields:**
```sql
ALTER TABLE test_results ADD COLUMN test_type ENUM('personal', 'instansi', 'gift') DEFAULT 'personal' AFTER customer_id;
ALTER TABLE test_results ADD COLUMN character_type_id BIGINT UNSIGNED NULL AFTER test_id;
ALTER TABLE test_results ADD COLUMN institution_name VARCHAR(255) NULL AFTER hasil_karakter;
ALTER TABLE test_results ADD FOREIGN KEY (character_type_id) REFERENCES character_types(id) ON DELETE SET NULL;
ALTER TABLE test_results ADD INDEX idx_test_type (test_type);
```

2. **Create migration to fix activity_logs polymorphic relationship** if needed

3. **Create model relationships:**
   - TestResult → CharacterType
   - TestResult → AdminInstansi (for institution_name)

### Phase 3: Testing & Validation (1 day)
1. Test InstansiDashboardController with sample data
2. Test AdminDashboardController with sample data
3. Test payment flow end-to-end
4. Test token usage flow
5. Verify all dashboard statistics calculate correctly

### Phase 4: Documentation (ongoing)
1. Update ER diagram with all tables
2. Create database dictionary
3. Document all foreign key relationships
4. Create migration checklist

---

## SECTION 9: DETAILED TABLE STRUCTURE

### Current Tables (23 total)

```
1. users (base authentication)
2. customers (personal user profiles)
3. super_admins (superadmin profiles)
4. admin_instansi (institution admin profiles)
5. character_types (character/personality types)
6. agendas (events/workshops/webinars)
7. teams (internal staff)
8. packages (token packages)
9. payment_gateways (payment processors)
10. transactions (payment transactions)
11. token_purchases (token purchase records)
12. old_tokens (legacy token records - archived)
13. tests (test definitions)
14. test_questions (test questions)
15. test_results (test completion results)
16. token_usage (token usage tracking)
17. certificates (test certificates)
18. activity_logs (system activity log)
19. cache (Laravel cache table)
20. jobs (Laravel queue jobs)
21. job_batches (Laravel queue batches)
22. sessions (Laravel sessions)
23. personal_access_tokens (Sanctum API tokens)
```

---

## SECTION 10: ACTION ITEMS CHECKLIST

- [ ] Update database_schema.sql file with complete current schema
- [ ] Create migration to add test_type field to test_results
- [ ] Create migration to add character_type_id foreign key to test_results
- [ ] Create migration to add institution_name field to test_results
- [ ] Update InstansiDashboardController to use correct field names
- [ ] Update AdminDashboardController to use correct field names
- [ ] Remove legacy Token references from User model
- [ ] Archive/document old_tokens table
- [ ] Create TestResult → CharacterType relationship
- [ ] Add integration tests for dashboard controllers
- [ ] Create data migration script for any old_tokens data
- [ ] Update API documentation with correct field names
- [ ] Create database migration validation tests

---

## CONCLUSION

The Saintara application has a **well-designed feature set** with proper separation of concerns between personal users, admins, and institutions. However, **the database schema is out of sync with the actual implementation**, causing multiple broken features. The primary issues are:

1. **Outdated schema documentation** (database_schema.sql)
2. **Missing TestResult fields** required by Instansi and Admin controllers
3. **Dual token system** causing confusion and potential failures
4. **Broken controller queries** referencing non-existent fields

With the Phase 1 and 2 fixes, the application will be fully functional and properly aligned with the database schema.

