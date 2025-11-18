# SCHEMA MISMATCH DETAILS - SPECIFIC CODE LOCATIONS & REQUIRED FIXES

## 1. InstansiDashboardController BROKEN QUERIES

**File:** `/home/user/SaintaraFinal/app/Http/Controllers/Instansi/InstansiDashboardController.php`

### Issue 1: Query Using Non-Existent Fields (Lines 17-21)
```php
// BROKEN: test_type, institution_name fields don't exist in test_results table
$institutionName = $user->testResults()
    ->where('test_type', 'instansi')          // ❌ FIELD DOESN'T EXIST
    ->whereNotNull('institution_name')        // ❌ FIELD DOESN'T EXIST
    ->latest()
    ->value('institution_name');
```

**Error:** Will return NULL or throw database error

**Fix Required:**
1. Add `test_type` enum column to test_results table
2. Add `institution_name` varchar column to test_results table
3. Populate these fields when creating test results for institutions

### Issue 2: Query Using Wrong Relationship (Lines 24-28)
```php
// BROKEN: user_id doesn't exist; test_results uses customer_id
$testResults = TestResult::where('test_type', 'instansi')
    ->where('user_id', $user->id)             // ❌ FIELD DOESN'T EXIST
    ->with('characterType')
    ->latest()
    ->paginate(10);
```

**Error:** Query returns no results

**Fix Required:**
- Use customer relationship chain: `User → Customer → TestResults`
- Or join through customer table

### Issue 3: Grouping by Non-Existent Field (Lines 40)
```php
// BROKEN: character_type_id foreign key doesn't exist in test_results
$characterDistribution = TestResult::where('test_type', 'instansi')
    ->where('user_id', $user->id)
    ->with('characterType')
    ->get()
    ->groupBy('character_type_id')           // ❌ FIELD DOESN'T EXIST
```

**Error:** Returns empty groups

**Fix Required:**
- Add `character_type_id` foreign key to test_results
- Or create relationship based on `hasil_karakter` field

---

## 2. AdminDashboardController BROKEN QUERIES

**File:** `/home/user/SaintaraFinal/app/Http/Controllers/Admin/AdminDashboardController.php`

### Issue 1: Wrong Field Name (Lines 25-27)
```php
// BROKEN: test_date field doesn't exist; actual field is tanggal_tes
$totalTestsThisMonth = TestResult::whereMonth('test_date', $currentMonth)
    ->whereYear('test_date', $currentYear)   // ❌ FIELD IS CALLED tanggal_tes
    ->count();
```

**Error:** Database error - column 'test_date' not found in test_results table

**Correct Field:** `tanggal_tes`

### Issue 2: Query Using Non-Existent Field (Lines 44-46)
```php
// BROKEN: test_type field doesn't exist in test_results table
$personalTests = TestResult::where('test_type', 'personal')->count();
$institutionTests = TestResult::where('test_type', 'instansi')->count();
$giftTests = TestResult::where('test_type', 'gift')->count();
```

**Error:** Returns 0 for all (field doesn't exist)

**Fix Required:**
- Add `test_type` enum column to test_results table

### Issue 3: Query Using Deprecated Token Table (Lines 49-52)
```php
// BROKEN: Token table has been renamed to old_tokens
$tokenSalesThisMonth = Token::whereMonth('created_at', $currentMonth)
    ->whereYear('created_at', $currentYear)
    ->where('payment_status', 'paid')
    ->sum('price');
```

**Error:** Table 'saintara_db.tokens' doesn't exist (it's now 'old_tokens')

**Fix Required:**
```php
// USE THIS INSTEAD:
$tokenSalesThisMonth = TokenPurchase::whereMonth('created_at', $currentMonth)
    ->whereYear('created_at', $currentYear)
    ->whereHas('transaction', function($q) {
        $q->where('status_pembayaran', 'dibayar');
    })
    ->sum(DB::raw('(SELECT harga FROM packages WHERE id = package_id)'));
    
// OR BETTER: Join with transaction to verify payment status
$tokenSalesThisMonth = Transaction::whereMonth('created_at', $currentMonth)
    ->whereYear('created_at', $currentYear)
    ->where('status_pembayaran', 'dibayar')
    ->sum('jumlah_bayar');
```

### Issue 4: Query Using Wrong Table (Lines 61-65)
```php
// BROKEN: Looking for relationships that don't match
$approvalRequests = Transaction::where('status', 'pending')    // ❌ FIELD DOESN'T EXIST
    ->latest()
    ->take(3)
    ->with(['user', 'team'])                  // ❌ RELATIONSHIPS DON'T EXIST
```

**Error:** Transaction table has `status_pembayaran`, not `status`. No `user` or `team` relationships.

**Fix Required:**
```php
$approvalRequests = Transaction::where('status_pembayaran', 'pending')
    ->latest()
    ->take(3)
    ->with(['customer.user'])  // Proper relationship path
    ->get();
```

---

## 3. Migration Inconsistencies

### Migration Issue: Old_tokens Table

**File:** `/home/user/SaintaraFinal/database/migrations/2025_11_18_100005_create_token_purchases_table.php`

Lines 14-17:
```php
// Migration renames old tokens table:
if (Schema::hasTable('tokens')) {
    Schema::rename('tokens', 'old_tokens');
}
```

**Problem:** 
- User model still references Token model with `hasMany('tokens')`
- AdminDashboardController queries Token model
- This causes queries on a non-existent table

**Files That Need Updates:**
1. `/home/user/SaintaraFinal/app/Models/User.php` - Remove Token relationship
2. `/home/user/SaintaraFinal/app/Http/Controllers/Admin/AdminDashboardController.php` - Use TokenPurchase instead

---

## 4. Missing Model Relationships

### TestResult Model Missing Relationships

**File:** `/home/user/SaintaraFinal/app/Models/TestResult.php`

**Missing:**
```php
// Need to add:
public function characterType(): BelongsTo
{
    return $this->belongsTo(CharacterType::class);
}

public function adminInstansi(): BelongsTo
{
    return $this->belongsTo(AdminInstansi::class, 'institution_id');
}
```

**Current Relationships (lines 38-61):**
```php
public function test(): BelongsTo
public function customer(): BelongsTo
public function tokenPurchase(): BelongsTo
public function certificate(): HasOne
public function tokenUsage(): HasOne
```

---

## 5. Migration Files Needed

### Migration 1: Add test_type to test_results

```php
// Create new migration file:
// database/migrations/2025_11_18_150000_add_test_type_to_test_results.php

Schema::table('test_results', function (Blueprint $table) {
    $table->enum('test_type', ['personal', 'instansi', 'gift'])
        ->default('personal')
        ->after('customer_id');
    $table->index('test_type');
});
```

### Migration 2: Add character_type_id to test_results

```php
// Create new migration file:
// database/migrations/2025_11_18_150001_add_character_type_to_test_results.php

Schema::table('test_results', function (Blueprint $table) {
    $table->foreignId('character_type_id')
        ->nullable()
        ->after('test_id')
        ->constrained('character_types')
        ->onDelete('set null');
});
```

### Migration 3: Add institution_name to test_results

```php
// Create new migration file:
// database/migrations/2025_11_18_150002_add_institution_name_to_test_results.php

Schema::table('test_results', function (Blueprint $table) {
    $table->string('institution_name', 255)
        ->nullable()
        ->after('hasil_karakter');
});
```

---

## 6. Code Files That NEED FIXING

### Priority 1 - MUST FIX (Breaking Application)

1. **InstansiDashboardController.php** - 4 critical issues
   - Lines 17-21: test_type, institution_name queries
   - Lines 24-28: user_id relationship
   - Lines 31-33: user_id relationship
   - Line 40: character_type_id grouping

2. **AdminDashboardController.php** - 4 critical issues
   - Lines 25-27: test_date field name
   - Lines 44-46: test_type field
   - Lines 49-52: Token table deprecation
   - Lines 61-65: status field and relationships

### Priority 2 - SHOULD FIX (Data Consistency)

3. **User.php Model**
   - Line 70-73: Remove `tokens()` relationship
   - Replace with proper relationship to Customer and their TokenPurchases

4. **TestResult.php Model**
   - Add `characterType()` relationship
   - Add `adminInstansi()` relationship

### Priority 3 - NICE TO HAVE (Enhancement)

5. **database_schema.sql** - Update to match migrations
   - Add super_admins table definition
   - Add admin_instansi table definition
   - Add character_types table definition
   - Add agendas table definition
   - Add teams table definition
   - Add test_questions table definition
   - Remove outdated admins table definition
   - Update activity_logs definition for polymorphic relationship

---

## 7. Test Results with Broken Fields

When you try to access these pages with current code:

### InstansiDashboardController
- **URL:** `/instansi/dashboardInstansi`
- **Status:** WILL CRASH or return empty data
- **Error Types:**
  - SQL error: Unknown column 'test_type'
  - Returns NULL for institutionName
  - Empty array for characterDistribution

### AdminDashboardController  
- **URL:** `/admin/dashboardAdmin`
- **Status:** WILL CRASH or return incomplete data
- **Error Types:**
  - SQL error: Unknown column 'test_date'
  - SQL error: Table 'tokens' doesn't exist
  - Returns 0 for all test type counts
  - Returns empty approval requests

---

## 8. Dependency Chain for Fixes

```
1. Create migrations for new test_results fields
   ↓
2. Run migrations
   ↓
3. Update InstansiDashboardController
   ↓
4. Update AdminDashboardController
   ↓
5. Update TestResult model relationships
   ↓
6. Update User model (remove Token)
   ↓
7. Test both dashboard pages
   ↓
8. Update database_schema.sql
```

---

## 9. Validation Script

```php
// Add this to test if schema is correct:
// php artisan tinker

// Check if fields exist:
Schema::hasColumn('test_results', 'test_type');  // Should be true
Schema::hasColumn('test_results', 'character_type_id');  // Should be true
Schema::hasColumn('test_results', 'institution_name');  // Should be true
Schema::hasColumn('test_results', 'tanggal_tes');  // Should be true

// Check if old table is gone:
Schema::hasTable('tokens');  // Should be false
Schema::hasTable('old_tokens');  // Should be true

// Check test data:
TestResult::where('test_type', 'instansi')->count();
TestResult::whereNotNull('character_type_id')->count();
```

---

## 10. Summary of Changes Needed

| File | Changes | Lines | Priority |
|------|---------|-------|----------|
| InstansiDashboardController.php | Remove test_type/institution_name queries, fix user_id to customer_id, fix character_type_id | 17-47 | CRITICAL |
| AdminDashboardController.php | Fix test_date to tanggal_tes, remove test_type queries, use TokenPurchase instead of Token | 25-65 | CRITICAL |
| User.php | Remove hasMany('tokens') relationship | 70-73 | HIGH |
| TestResult.php | Add characterType and adminInstansi relationships | - | HIGH |
| Database migrations | Create 3 new migrations for test_results fields | - | HIGH |
| database_schema.sql | Complete rewrite with all 18 tables | - | MEDIUM |

