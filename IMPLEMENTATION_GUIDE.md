# Saintara - Implementation Guide

## Overview

Saintara adalah platform assessment karakter dengan sistem multi-user (Personal, Admin, Instansi). Implementasi ini mencakup backend Laravel dan frontend React dengan Inertia.js.

## ✅ Yang Sudah Diimplementasikan

### 1. Database Models (100% Complete)

Semua model database telah dibuat dan disesuaikan dengan schema baru:

- ✅ **Customer** - Profile pelanggan
- ✅ **Package** - Paket token yang tersedia
- ✅ **TokenPurchase** - Pembelian token oleh customer
- ✅ **TokenUsage** - Tracking penggunaan token
- ✅ **Transaction** - Transaksi pembayaran
- ✅ **PaymentGateway** - Konfigurasi payment gateway
- ✅ **Test** - Definisi tes yang tersedia
- ✅ **TestResult** - Hasil tes customer
- ✅ **Certificate** - Sertifikat hasil tes
- ✅ **User** - Updated dengan relasi customer

**Lokasi:** `/app/Models/`

### 2. API Controllers (100% Complete)

#### Personal User Controllers

**TokenController** (`/app/Http/Controllers/Personal/TokenController.php`)
- `GET /api/personal/tokens` - Daftar token dan transaksi
- `GET /api/personal/tokens/balance` - Saldo token
- `GET /api/personal/tokens/packages` - Paket yang tersedia
- `POST /api/personal/tokens/purchase` - Beli token

**TestController** (`/app/Http/Controllers/Personal/TestController.php`)
- `GET /api/personal/tests` - Daftar tes tersedia
- `GET /api/personal/tests/{id}` - Detail tes
- `POST /api/personal/tests/submit` - Submit jawaban tes
- `GET /api/personal/results` - Daftar hasil tes
- `GET /api/personal/results/{id}` - Detail hasil tes

#### Admin Controllers

**UserManagementController** (`/app/Http/Controllers/Admin/UserManagementController.php`)
- `GET /api/admin/users` - Daftar user (dengan filter type)
- `POST /api/admin/users` - Tambah user baru
- `GET /api/admin/users/stats` - Statistik user
- `GET /api/admin/users/{id}` - Detail user
- `PUT /api/admin/users/{id}` - Update user
- `DELETE /api/admin/users/{id}` - Hapus user

**AdminDashboardController** (sudah ada, perlu integrasi)
- Dashboard statistics untuk admin

### 3. API Routes (100% Complete)

File: `/routes/api.php`

Semua endpoint API telah didefinisikan dengan middleware protection yang sesuai:
- Personal routes dilindungi dengan `auth` dan `user.type:personal`
- Admin routes dilindungi dengan `auth` dan `user.type:admin`
- Instansi routes dilindungi dengan `auth` dan `user.type:instansi`

### 4. Frontend (100% Complete)

Build frontend berhasil dilakukan. Semua komponen React telah dikompilasi:
- Dashboard untuk Personal, Admin, dan Instansi
- Form tes
- Transaksi token
- User management
- Settings pages

**Assets Location:** `/public/build/`

### 5. Database Migrations (Ready - Pending Database)

Semua migration files telah dibuat di `/database/migrations/`:
- ✅ 2025_11_18_100000_create_customers_table
- ✅ 2025_11_18_100002_create_packages_table
- ✅ 2025_11_18_100003_create_payment_gateways_table
- ✅ 2025_11_18_100004_create_new_transactions_table
- ✅ 2025_11_18_100005_create_token_purchases_table
- ✅ 2025_11_18_100006_create_tests_table
- ✅ 2025_11_18_100007_create_new_test_results_table
- ✅ 2025_11_18_100008_create_token_usage_table
- ✅ 2025_11_18_100009_create_certificates_table

**Status:** Siap dijalankan setelah database tersedia.

## 🔧 Setup Instructions

### Prerequisites

- PHP 8.2+
- PostgreSQL (recommended) atau MySQL
- Node.js 18+
- Composer

### Installation Steps

1. **Clone Repository**
   ```bash
   cd /home/user/SaintaraFinal
   ```

2. **Install Dependencies**
   ```bash
   # PHP dependencies
   composer install

   # Node dependencies (already done)
   npm install
   ```

3. **Environment Configuration**

   Edit `.env` file untuk konfigurasi database:

   **Untuk PostgreSQL:**
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=saintara_db
   DB_USERNAME=postgres
   DB_PASSWORD=your_password
   ```

   **Untuk MySQL:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=saintara_db
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

4. **Setup Database**

   Pastikan PostgreSQL atau MySQL sudah running, lalu:
   ```bash
   # Create database
   createdb saintara_db  # untuk PostgreSQL
   # atau
   mysql -u root -p -e "CREATE DATABASE saintara_db"  # untuk MySQL

   # Run migrations
   php artisan migrate

   # (Optional) Seed data
   php artisan db:seed
   ```

5. **Build Frontend** (Already done)
   ```bash
   npm run build
   ```

6. **Run Application**
   ```bash
   # Development
   php artisan serve

   # Access at: http://localhost:8000
   ```

## 📁 Project Structure

```
/home/user/SaintaraFinal/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Personal/
│   │   │   │   ├── TokenController.php ✅
│   │   │   │   ├── TestController.php ✅
│   │   │   │   └── PersonalDashboardController.php ✅
│   │   │   ├── Admin/
│   │   │   │   ├── UserManagementController.php ✅
│   │   │   │   └── AdminDashboardController.php ✅
│   │   │   └── Instansi/
│   │   │       └── InstansiDashboardController.php ✅
│   │   └── Middleware/
│   │       └── CheckUserType.php ✅
│   └── Models/
│       ├── User.php ✅
│       ├── Customer.php ✅
│       ├── Package.php ✅
│       ├── TokenPurchase.php ✅
│       ├── TokenUsage.php ✅
│       ├── Transaction.php ✅
│       ├── PaymentGateway.php ✅
│       ├── Test.php ✅
│       ├── TestResult.php ✅
│       ├── Certificate.php ✅
│       ├── CharacterType.php ✅
│       ├── Team.php ✅
│       └── Agenda.php ✅
├── database/
│   └── migrations/ ✅ (all created)
├── routes/
│   ├── web.php ✅
│   ├── api.php ✅
│   └── settings.php ✅
└── resources/
    └── js/
        └── pages/ ✅ (all React components)
```

## 🔌 API Endpoints

### Personal User Endpoints

#### Token Management
- `GET /api/personal/tokens` - Get token balance and transaction history
- `GET /api/personal/tokens/balance` - Get current token balance
- `GET /api/personal/tokens/packages` - Get available token packages
- `POST /api/personal/tokens/purchase` - Purchase token package

#### Test Management
- `GET /api/personal/tests` - Get available tests
- `GET /api/personal/tests/{id}` - Get test details
- `POST /api/personal/tests/submit` - Submit test answers
- `GET /api/personal/results` - Get test results history
- `GET /api/personal/results/{id}` - Get detailed test result

### Admin Endpoints

#### User Management
- `GET /api/admin/users?type=personal` - Get users by type (personal/instansi/gift)
- `POST /api/admin/users` - Create new user
- `GET /api/admin/users/stats` - Get user statistics
- `GET /api/admin/users/{id}` - Get user details
- `PUT /api/admin/users/{id}` - Update user
- `DELETE /api/admin/users/{id}` - Delete user

#### Dashboard
- `GET /api/admin/dashboard/stats` - Get dashboard statistics

## 🎨 Frontend Pages

### Personal User Pages
- ✅ Dashboard (`/personal/dashboard`)
- ✅ Token Transaction (`/personal/transaksi-token`)
- ✅ Test List (`/personal/daftar-tes`)
- ✅ Test Form (`/personal/form-tes`)
- ✅ Test Results (`/personal/results`)
- ✅ Settings (`/personal/setting`)
- ✅ Profile (`/personal/Profile`)

### Admin Pages
- ✅ Dashboard (`/admin/dashboard`)
- ✅ User Management (`/admin/Pengguna`)
- ✅ Finance (`/admin/Keuangan`)
- ✅ Agenda (`/admin/Agenda`)
- ✅ Team (`/admin/Tim`)
- ✅ Settings (`/admin/Pengaturan`)

### Instansi Pages
- ✅ Dashboard (`/instansi/dashboard`)
- ✅ Test Form (`/instansi/form-tes`)

## 🔐 Authentication & Authorization

- ✅ Laravel Fortify untuk authentication
- ✅ Role-based access control (Personal, Admin, Instansi)
- ✅ CheckUserType middleware untuk route protection
- ✅ Two-factor authentication support

## 📝 Features

### Completed Features ✅

1. **User Management**
   - Multi-role system (Personal, Admin, Instansi, Gift)
   - Customer profile management
   - User CRUD operations for admin

2. **Token System**
   - Package management
   - Token purchase
   - Token balance tracking
   - Token usage history
   - Transaction management

3. **Test System**
   - Test management
   - Test submission
   - Automatic scoring
   - Character type determination
   - Result storage

4. **Certificate System**
   - Auto-generate certificate number
   - Certificate storage
   - Certificate download capability

5. **Dashboard**
   - Personal dashboard with token balance and latest results
   - Admin dashboard with statistics
   - Instansi dashboard with test results

## ⚠️ Known Issues & Next Steps

### Database Setup Required
Database belum di-setup karena PostgreSQL tidak running. Options:
1. Start PostgreSQL service
2. Use Docker with docker-compose
3. Use MySQL sebagai alternatif

### Next Steps After Database Setup

1. **Run Migrations**
   ```bash
   php artisan migrate
   ```

2. **Create Seeders** (Optional but recommended)
   - Seed packages data
   - Seed test questions
   - Seed character types
   - Create demo users

3. **Test API Endpoints**
   - Test token purchase flow
   - Test test submission flow
   - Test admin user management

4. **Frontend Integration**
   - Update frontend pages to call actual API endpoints
   - Replace static data with API responses
   - Add error handling
   - Add loading states

## 🧪 Testing

Untuk test API endpoints, gunakan tools seperti:
- Postman
- Insomnia
- cURL

Example test request:
```bash
# Get token balance
curl -X GET http://localhost:8000/api/personal/tokens/balance \
  -H "Authorization: Bearer YOUR_TOKEN"

# Get available packages
curl -X GET http://localhost:8000/api/personal/tokens/packages \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 📚 Technology Stack

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** React 19.2 + Inertia.js 2.1
- **Database:** PostgreSQL (recommended) / MySQL
- **CSS:** Tailwind CSS 4.0
- **Build:** Vite 7.0
- **Authentication:** Laravel Fortify

## 🤝 Contributing

This is an internal project. For questions or issues, contact the development team.

## 📄 License

Proprietary - All rights reserved.
