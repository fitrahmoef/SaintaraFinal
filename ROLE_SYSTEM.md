# Sistem Role dan Permission Saintara

## Overview

Sistem ini mengimplementasikan Role-Based Access Control (RBAC) yang komprehensif untuk aplikasi Saintara. Semua user memiliki role yang menentukan apa yang dapat mereka lakukan dalam sistem.

## Hierarki Role

```
SUPERADMIN (Level 100)
    ↓
ADMIN (Level 50)
    ↓
INSTANSI (Level 30)
    ↓
PERSONAL (Level 10)
    ↓
GIFT (Level 5)
```

## Role Descriptions

### 1. SUPERADMIN
- **Level**: 100 (Tertinggi)
- **Dashboard**: Personal Dashboard (dengan akses penuh ke semua fitur)
- **Akses**: Full CRUD access ke semua fitur sistem
- **Kemampuan**:
  - Dapat mengakses semua dashboard (personal, admin, instansi)
  - Bypass semua permission checks
  - Dapat membuat, mengedit, dan menghapus user dengan role apapun
  - Dapat mempromosikan user menjadi superadmin
  - Akses ke semua menu dan fitur tanpa batasan

### 2. ADMIN
- **Level**: 50
- **Dashboard**: Admin Dashboard
- **Akses**: Full management access
- **Kemampuan**:
  - User Management (CRUD semua user kecuali superadmin)
  - Test & Question Management (CRUD)
  - Package Management (CRUD)
  - Transaction Management (CRUD + Approval)
  - Agenda Management (CRUD)
  - Team Management (View, Approve, Delete)
  - Audit Logs Access
  - System Settings Management

### 3. INSTANSI
- **Level**: 30
- **Dashboard**: Instansi Dashboard
- **Akses**: Institution-specific features
- **Kemampuan**:
  - Manage Employees (bulk upload, manage sub-accounts)
  - View Institution Reports
  - View All Test Results (untuk employees)
  - Purchase Tokens
  - View & Create Transactions
  - View Packages

### 4. PERSONAL
- **Level**: 10
- **Dashboard**: Personal Dashboard
- **Akses**: Personal user features
- **Kemampuan**:
  - Take Psychological Tests
  - View Own Test Results
  - Download Certificates
  - Purchase Tokens
  - Manage Own Profile
  - Create & View Team Registrations
  - View Agendas

### 5. GIFT
- **Level**: 5
- **Dashboard**: Personal Dashboard
- **Akses**: Limited personal features
- **Kemampuan**:
  - Take Tests (dengan token yang diberikan)
  - View Own Test Results
  - Download Certificates
  - View Token Balance

## Permissions Matrix

| Permission | Superadmin | Admin | Instansi | Personal | Gift |
|-----------|-----------|-------|----------|----------|------|
| **User Management** |
| View Users | ✓ | ✓ | ✗ | ✗ | ✗ |
| Create Users | ✓ | ✓ | ✗ | ✗ | ✗ |
| Update Users | ✓ | ✓ | ✗ | ✗ | ✗ |
| Delete Users | ✓ | ✓ | ✗ | ✗ | ✗ |
| Manage User Types | ✓ | ✓ | ✗ | ✗ | ✗ |
| **Test Management** |
| View Tests | ✓ | ✓ | ✗ | ✗ | ✗ |
| Create Tests | ✓ | ✓ | ✗ | ✗ | ✗ |
| Update Tests | ✓ | ✓ | ✗ | ✗ | ✗ |
| Delete Tests | ✓ | ✓ | ✗ | ✗ | ✗ |
| Take Tests | ✓ | ✗ | ✗ | ✓ | ✓ |
| View All Test Results | ✓ | ✓ | ✓ | ✗ | ✗ |
| **Package Management** |
| View Packages | ✓ | ✓ | ✓ | ✓ | ✗ |
| Create Packages | ✓ | ✓ | ✗ | ✗ | ✗ |
| Update Packages | ✓ | ✓ | ✗ | ✗ | ✗ |
| Delete Packages | ✓ | ✓ | ✗ | ✗ | ✗ |
| **Transaction Management** |
| View Transactions | ✓ | ✓ | ✓ | ✓ | ✗ |
| Create Transactions | ✓ | ✓ | ✓ | ✓ | ✗ |
| Update Transactions | ✓ | ✓ | ✗ | ✗ | ✗ |
| Delete Transactions | ✓ | ✓ | ✗ | ✗ | ✗ |
| Approve Transactions | ✓ | ✓ | ✗ | ✗ | ✗ |
| **Token Management** |
| View Tokens | ✓ | ✓ | ✓ | ✓ | ✓ |
| Purchase Tokens | ✓ | ✗ | ✓ | ✓ | ✗ |
| Manage All Tokens | ✓ | ✓ | ✗ | ✗ | ✗ |
| **Institution Management** |
| Manage Employees | ✓ | ✓ | ✓ | ✗ | ✗ |
| Bulk Upload Employees | ✓ | ✓ | ✓ | ✗ | ✗ |
| View Institution Reports | ✓ | ✓ | ✓ | ✗ | ✗ |
| **Team Management** |
| View Teams | ✓ | ✓ | ✗ | ✓ | ✗ |
| Create Teams | ✓ | ✓ | ✗ | ✓ | ✗ |
| Update Teams | ✓ | ✓ | ✗ | ✗ | ✗ |
| Delete Teams | ✓ | ✓ | ✗ | ✗ | ✗ |
| Approve Teams | ✓ | ✓ | ✗ | ✗ | ✗ |
| **Profile & Results** |
| Update Own Profile | ✓ | ✓ | ✓ | ✓ | ✓ |
| View Own Results | ✓ | ✓ | ✓ | ✓ | ✓ |
| Download Certificates | ✓ | ✗ | ✗ | ✓ | ✓ |
| **System Administration** |
| View Audit Logs | ✓ | ✓ | ✗ | ✗ | ✗ |
| Manage System Settings | ✓ | ✓ | ✗ | ✗ | ✗ |
| Access All Features | ✓ | ✗ | ✗ | ✗ | ✗ |

## Penggunaan dalam Code

### 1. Mengecek Role User

```php
// Di Controller atau Blade
if (auth()->user()->isSuperAdmin()) {
    // Logika khusus superadmin
}

if (auth()->user()->isAdmin()) {
    // Includes both admin and superadmin
}

if (auth()->user()->hasRole(Role::INSTANSI)) {
    // Specific role check
}

if (auth()->user()->hasAnyRole([Role::ADMIN, Role::SUPERADMIN])) {
    // Check multiple roles
}
```

### 2. Mengecek Permission

```php
// Di Controller
use App\Enums\Permission;
use Illuminate\Support\Facades\Gate;

// Check permission and abort if unauthorized
Gate::authorize(Permission::VIEW_USERS->value);

// Check permission and get boolean result
if (Gate::allows(Permission::CREATE_USERS->value)) {
    // User has permission
}

// Using User model
if (auth()->user()->hasPermission(Permission::DELETE_USERS)) {
    // User has permission
}
```

### 3. Middleware pada Routes

```php
// Protect route by user type
Route::get('/admin/dashboard', [AdminController::class, 'index'])
    ->middleware(['auth', 'user.type:admin']);

// Protect route by permission
Route::post('/admin/users', [UserController::class, 'store'])
    ->middleware(['auth', 'permission:create_users']);

// Multiple permissions (user needs any one)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'permission:access_admin_dashboard,access_personal_dashboard']);
```

### 4. Menggunakan Gate dalam Blade/Inertia

```php
@can('view_users')
    <a href="/admin/users">Manage Users</a>
@endcan

@can('create_tests')
    <button>Create Test</button>
@endcan
```

## Security Features

### 1. Superadmin Protection
- Hanya superadmin yang dapat membuat user superadmin lain
- Hanya superadmin yang dapat mengedit atau menghapus user superadmin
- Non-superadmin tidak dapat mempromosikan user menjadi superadmin

### 2. Self-Protection
- User tidak dapat menghapus akun mereka sendiri
- Perubahan role memerlukan validasi hierarki

### 3. Mass Assignment Protection
- `user_type` dihapus dari `fillable` array
- Harus menggunakan `setUserType()` method untuk mengubah role
- Validasi otomatis saat set role baru

### 4. Hierarchy Enforcement
- Higher-level roles dapat manage lower-level roles
- Level-based comparison: `$user->hasHigherAuthorityThan($otherUser)`

## File-File Utama

### 1. Enums
- `app/Enums/Role.php` - Definisi semua role
- `app/Enums/Permission.php` - Definisi semua permission

### 2. Models
- `app/Models/User.php` - User model dengan role helper methods

### 3. Providers
- `app/Providers/RolePermissionServiceProvider.php` - Gate definitions untuk permissions

### 4. Middleware
- `app/Http/Middleware/CheckUserType.php` - Validasi role user
- `app/Http/Middleware/CheckPermission.php` - Validasi permissions

### 5. Controllers
- `app/Http/Controllers/Admin/UserManagementController.php` - User CRUD dengan permission checks

### 6. Migrations
- `database/migrations/2025_11_19_000001_add_superadmin_and_gift_to_user_type_enum.php`

## Dashboard Routing Logic

Setelah login, user akan diarahkan ke dashboard sesuai role mereka:

```php
Route::get('/dashboard', function () {
    switch (auth()->user()->user_type) {
        case 'superadmin':
            return redirect()->route('personal.dashboard'); // Superadmin menggunakan personal dashboard
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'instansi':
            return redirect()->route('instansi.dashboard');
        case 'gift':
        case 'personal':
        default:
            return redirect()->route('personal.dashboard');
    }
})->middleware('auth');
```

## Cara Membuat User Superadmin

### Via Tinker (Recommended untuk first superadmin)

```bash
php artisan tinker
```

```php
$user = new App\Models\User;
$user->name = 'Super Admin';
$user->email = 'superadmin@saintara.com';
$user->password = Hash::make('password123');
$user->setUserType('superadmin');
$user->save();
```

### Via Admin Panel (Jika sudah ada superadmin)

1. Login sebagai superadmin
2. Buka User Management
3. Create New User
4. Pilih role "superadmin" dari dropdown
5. Only superadmin dapat melihat dan memilih opsi ini

## Testing Role System

```php
// Test role hierarchy
$superadmin = User::where('user_type', 'superadmin')->first();
$admin = User::where('user_type', 'admin')->first();

$superadmin->hasHigherAuthorityThan($admin); // true

// Test permissions
$admin->hasPermission(Permission::VIEW_USERS); // true
$personal->hasPermission(Permission::VIEW_USERS); // false

// Test gate
Gate::forUser($superadmin)->allows(Permission::ACCESS_ALL_FEATURES); // true
```

## Best Practices

1. **Selalu gunakan Permission checks** untuk aksi-aksi penting, jangan hanya check role
2. **Superadmin bypass semua checks** - hati-hati saat testing
3. **Gunakan Gate::authorize()** untuk automatic 403 response jika unauthorized
4. **Jangan hardcode role strings** - gunakan `Role::ADMIN->value` atau enum
5. **Log semua perubahan role** - sudah implemented via AuditLogMiddleware
6. **Minimal privilege principle** - berikan permission seminimal mungkin

## Future Enhancements

Sistem ini dapat dikembangkan dengan:
- Dynamic role creation via admin panel
- Custom permission sets per organization
- Role templates
- Permission inheritance
- Time-based role assignment
- Multi-role support per user
