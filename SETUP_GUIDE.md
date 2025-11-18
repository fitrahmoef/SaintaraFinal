# 🚀 Panduan Setup Platform Saintara

## 📋 Prerequisites

- PHP 8.2 atau lebih tinggi
- Composer
- Node.js & NPM
- Database (PostgreSQL recommended / MySQL / SQLite)
- Git

## 🔧 Instalasi

### 1. Clone Repository & Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install NPM dependencies
npm install
```

### 2. Setup Environment

```bash
# Copy file .env example
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Konfigurasi Database

Edit file `.env` dan sesuaikan dengan database Anda:

#### Untuk PostgreSQL (Recommended):
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=saintara_db
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

#### Untuk MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=saintara_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### Untuk SQLite (Development only):
```env
DB_CONNECTION=sqlite
```

### 4. Buat Database

#### PostgreSQL:
```bash
createdb saintara_db
# atau
psql -U postgres -c "CREATE DATABASE saintara_db;"
```

#### MySQL:
```bash
mysql -u root -p -e "CREATE DATABASE saintara_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

#### SQLite:
```bash
touch database/database.sqlite
```

### 5. Run Migrations & Seeders

```bash
# Fresh install dengan data lengkap
php artisan migrate:fresh --seed
```

Seeder akan mengisi database dengan:
- ✅ **13 Paket** (Personal, Instansi, Sekolah, Gift)
- ✅ **15 Payment Gateways** (Midtrans, E-wallet, Bank Transfer, dll)
- ✅ **9 Character Types** (Tipe karakter Saintara)
- ✅ **5 Sample Tests** (Tes Dasar, Standar, Premium, Instansi, Sekolah)
- ✅ **3 Test Users**:
  - Personal: `test@example.com` / `password`
  - Admin: `admin@saintara.com` / `admin123`
  - Instansi: `instansi@example.com` / `password`

### 6. Build Frontend Assets

```bash
# Development mode (with watch)
npm run dev

# atau Production build
npm run build
```

### 7. Jalankan Server

```bash
# Development server
php artisan serve
```

Aplikasi akan berjalan di: `http://localhost:8000`

---

## 🎯 Flow User

### Untuk User Personal:

1. **Register** → `/register`
   - Isi data: Name, Email, Password, Phone, Negara, Kota
   - User type: Personal

2. **Login** → `/login`
   - Email & Password

3. **Dashboard** → `/dashboardPersonal`
   - Lihat token balance
   - Lihat hasil tes terakhir

4. **Beli Token** → `/transaksiToken`
   - Pilih paket (Dasar/Standar/Premium)
   - Pilih metode pembayaran
   - Bayar & dapatkan token

5. **Ikuti Tes** → `/daftarTes`
   - Pilih tes yang tersedia
   - Isi form tes
   - Submit jawaban

6. **Lihat Hasil** → `/hasilTes`
   - Download PDF hasil
   - Download sertifikat

### Untuk Instansi/Organisasi:

1. **Register** → `/register`
   - User type: Instansi
   - Isi data admin & organisasi

2. **Dashboard** → `/dashboardInstansi`
   - Upload data peserta (Excel/CSV)
   - Monitor hasil tes tim

3. **Laporan Tim** → Analisis chemistry & penempatan

---

## 📦 Data Paket yang Tersedia

### Personal:
- **Paket Dasar** - Rp 99.000 (10 analisis)
- **Paket Standar** - Rp 199.000 (25 analisis + konsultasi)
- **Paket Premium** - Rp 349.000 (35+ analisis + konsultasi 3x + AI access)

### Instansi:
- **Laporan Umum** - Rp 75.000/orang (min 10 peserta)
- **Lengkap & Penempatan** - Rp 120.000/orang
- **+ Pelatihan Online** - Rp 180.000/orang
- **+ Pelatihan Tatap Muka** - Rp 250.000/orang (min 20 peserta)

### Sekolah:
- **Laporan Umum** - Rp 50.000/siswa (min 30 siswa)
- **Lengkap & Karir** - Rp 85.000/siswa

### Gift/Donasi:
- **Gift Paket Dasar** - Rp 99.000
- **Donasi 1 Tes** - Rp 50.000
- **Donasi 10 Tes** - Rp 450.000

---

## 🎨 9 Tipe Karakter Saintara

1. **Pemikir Introvert** - Analitis, logis, introspektif
2. **Pemikir Extrovert** - Strategis, leader, asertif
3. **Pengamat Introvert** - Detail, praktis, konsisten
4. **Pengamat Extrovert** - Energik, action-oriented, adaptif
5. **Perasa Introvert** - Empati, idealis, kreatif
6. **Perasa Extrovert** - Sosial, supportive, komunikatif
7. **Pemimpi Introvert** - Visioner, inovatif, konseptual
8. **Pemimpi Extrovert** - Antusias, inspiratif, kreatif
9. **Penggerak** - Dinamis, pragmatis, natural leader

---

## 🔐 Default Login Credentials

### User Personal:
- Email: `test@example.com`
- Password: `password`

### Admin:
- Email: `admin@saintara.com`
- Password: `admin123`

### Instansi:
- Email: `instansi@example.com`
- Password: `password`

---

## 🛠️ Development Commands

```bash
# Run migrations
php artisan migrate

# Run seeders only
php artisan db:seed

# Fresh migration + seed
php artisan migrate:fresh --seed

# Clear cache
php artisan optimize:clear

# Create controller
php artisan make:controller ControllerName

# Create model
php artisan make:model ModelName

# Create seeder
php artisan make:seeder SeederName

# Run tests
php artisan test
# atau
./vendor/bin/pest

# Watch frontend changes
npm run dev

# Build for production
npm run build

# Code formatting (PHP)
./vendor/bin/pint

# Code formatting (JS/TS)
npm run format
```

---

## 📚 API Endpoints

### Personal API

```
GET    /api/personal/tokens          - Token & transaction history
GET    /api/personal/tokens/balance  - Token balance
GET    /api/personal/tokens/packages - Available packages
POST   /api/personal/tokens/purchase - Purchase token
GET    /api/personal/tests           - Available tests
GET    /api/personal/tests/{id}      - Test detail
POST   /api/personal/tests/submit    - Submit test
GET    /api/personal/results         - Test results
GET    /api/personal/results/{id}    - Result detail
```

### Admin API

```
GET    /api/admin/users              - List users
POST   /api/admin/users              - Create user
GET    /api/admin/users/stats        - User statistics
GET    /api/admin/users/{id}         - User detail
PUT    /api/admin/users/{id}         - Update user
DELETE /api/admin/users/{id}         - Delete user
GET    /api/admin/dashboard/stats    - Dashboard stats
```

### Instansi API

```
POST   /api/instansi/tests/submit-batch - Submit batch tests
```

---

## 🌐 Environment Variables

### Aplikasi
```env
APP_NAME=Saintara
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

### Database
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=saintara_db
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### Mail (untuk production)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@saintara.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Payment Gateway (Midtrans)
```env
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false
```

---

## 🐛 Troubleshooting

### Error: "could not find driver"
Pastikan PHP extension untuk database Anda sudah terinstall:
```bash
# PostgreSQL
sudo apt-get install php8.2-pgsql

# MySQL
sudo apt-get install php8.2-mysql

# SQLite
sudo apt-get install php8.2-sqlite3
```

### Error: "No application encryption key has been specified"
```bash
php artisan key:generate
```

### Error: NPM packages not found
```bash
npm install
```

### Frontend tidak muncul
```bash
npm run build
php artisan optimize:clear
```

---

## 📝 Catatan Penting

1. **Database Seeding**: Sudah include data lengkap untuk development
2. **Payment Gateway**: Untuk testing, gunakan Midtrans Sandbox
3. **Email**: Di development menggunakan `log` driver (cek `storage/logs`)
4. **2FA**: Fitur Two-Factor Authentication sudah tersedia via Laravel Fortify
5. **API**: Semua endpoint sudah protected dengan middleware auth

---

## 🚀 Next Steps

Yang sudah siap:
- ✅ Database schema & migrations
- ✅ Models & relationships
- ✅ Seeders lengkap
- ✅ Backend controllers & API
- ✅ Frontend pages & components
- ✅ Auth system (Login, Register, 2FA)

Yang perlu dikembangkan:
- ⏳ Payment gateway integration (Midtrans SDK)
- ⏳ Test questions database & algorithm
- ⏳ PDF certificate generation
- ⏳ Email notifications
- ⏳ Frontend-backend API integration lengkap

---

## 📞 Support

Jika ada pertanyaan atau issue:
1. Check documentation di `/docs`
2. Review code di `/app`, `/resources`, `/routes`
3. Check logs di `/storage/logs`

**Happy Coding! 🎉**
