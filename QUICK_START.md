# 🚀 Quick Start Guide - Platform Saintara

## ✅ Setup yang Sudah Selesai

1. ✅ **Dependencies Installed**
   - Composer packages (139 packages)
   - NPM packages (478 packages)
   - Laravel DomPDF untuk PDF certificates
   - Laravel Fortify untuk authentication
   - Inertia.js untuk frontend integration

2. ✅ **Configuration Ready**
   - `.env` file configured
   - Application key generated
   - Environment variables set

## 🗄️ Database Setup (Required)

Pilih salah satu opsi berikut:

### Opsi 1: SQLite (Paling Mudah)

```bash
# Install SQLite PDO extension
sudo apt-get update
sudo apt-get install -y php8.4-sqlite3

# Atau jika PHP versi berbeda:
sudo apt-get install -y php-sqlite3

# Restart PHP
sudo service php8.4-fpm restart  # Jika pakai FPM

# Buat database file
touch database/database.sqlite

# Update .env (sudah diupdate ke SQLite)
# DB_CONNECTION=sqlite

# Run migrations
php artisan migrate:fresh --seed
```

### Opsi 2: MySQL

```bash
# Start MySQL service
sudo service mysql start

# Create database
mysql -u root -p -e "CREATE DATABASE saintara_db;"

# Update .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=saintara_db
DB_USERNAME=root
DB_PASSWORD=your_password

# Run migrations
php artisan migrate:fresh --seed
```

### Opsi 3: Laravel Sail (Docker - RECOMMENDED)

```bash
# Generate docker-compose.yml
php artisan sail:install

# Pilih: mysql, redis, meilisearch (optional)

# Start containers
./vendor/bin/sail up -d

# Run migrations
./vendor/bin/sail artisan migrate:fresh --seed

# Akses aplikasi
# http://localhost
```

### Opsi 4: PostgreSQL

```bash
# Start PostgreSQL
sudo service postgresql start

# Create database
sudo -u postgres psql -c "CREATE DATABASE saintara_db;"
sudo -u postgres psql -c "CREATE USER postgres WITH PASSWORD 'fitrah123';"
sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE saintara_db TO postgres;"

# Update .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=saintara_db
DB_USERNAME=postgres
DB_PASSWORD=fitrah123

# Run migrations
php artisan migrate:fresh --seed
```

## 🏃 Running the Application

### Development Mode

```bash
# Terminal 1: Laravel backend
php artisan serve
# Server akan jalan di http://localhost:8000

# Terminal 2: Vite frontend
npm run dev
# Vite dev server untuk hot reload

# Buka browser
# http://localhost:8000
```

### Dengan Laravel Sail

```bash
# Start semua services
./vendor/bin/sail up -d

# Build frontend assets
./vendor/bin/sail npm run dev

# Akses aplikasi
# http://localhost
```

## 🧪 Test Accounts

Setelah running `php artisan migrate:fresh --seed`, test accounts akan tersedia:

```
Personal Account:
Email: test@example.com
Password: password

Admin Account:
Email: admin@saintara.com
Password: admin123

Instansi Account:
Email: instansi@example.com
Password: password
```

## 📦 What's Included

### Database (23 Migrations)
- ✅ Users, Customers, Balances
- ✅ Packages, Transactions, Payments
- ✅ Tests, Results, Certificates
- ✅ Character Types & Analysis
- ✅ Sessions, Cache, Jobs

### Seeders (Sample Data)
- ✅ 13 Token Packages (5-100 tokens)
- ✅ 15 Payment Gateways (Midtrans, GoPay, OVO, dll)
- ✅ 9 Character Types (Saintara algorithm)
- ✅ 5 Test Types (Personal, Pasangan, Keluarga, dll)

### Backend Features
- ✅ Authentication (Login, Register, 2FA, Password Reset)
- ✅ API Routes (20+ endpoints)
- ✅ Controllers (Personal, Admin, Instansi)
- ✅ Middleware (Role-based access)
- ✅ PDF Certificate Generation (DomPDF)

### Frontend Features
- ✅ React + TypeScript + Inertia.js
- ✅ Tailwind CSS + Radix UI
- ✅ 20+ pages (Auth, Personal, Admin, Instansi)
- ✅ Modern responsive design

## 🐛 Troubleshooting

### Error: "could not find driver"
SQLite PDO extension belum terinstall. Install dengan:
```bash
sudo apt-get install php-sqlite3
```

### Error: "SQLSTATE[HY000] [2002] Connection refused"
Database server belum jalan. Start service:
```bash
sudo service mysql start
# atau
sudo service postgresql start
```

### Port 8000 sudah dipakai
Gunakan port lain:
```bash
php artisan serve --port=8001
```

### NPM vulnerabilities
Fix dengan:
```bash
npm audit fix
```

## 📚 Next Steps

Setelah database setup berhasil:

1. **Test Core Flow**
   - Register user baru
   - Login
   - Beli token
   - Ikut tes
   - Lihat hasil

2. **Development**
   - Lihat `IMPLEMENTATION_STATUS.md` untuk roadmap
   - Lihat `IMPLEMENTATION_GUIDE.md` untuk developer guide
   - Lihat `DATABASE_REVIEW.md` untuk schema details

3. **Integration**
   - Setup Midtrans untuk payment gateway
   - Implement character analysis algorithm
   - Setup email notifications

## 🔗 Useful Commands

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check routes
php artisan route:list

# Database
php artisan migrate:status
php artisan db:seed
php artisan migrate:rollback

# Tinker (Laravel REPL)
php artisan tinker

# Queue workers (jika pakai queue)
php artisan queue:work

# Build for production
npm run build
```

## 💡 Tips

1. **Gunakan Laravel Sail** untuk konsistensi environment
2. **Enable debug mode** di `.env` untuk development:
   ```
   APP_DEBUG=true
   LOG_LEVEL=debug
   ```
3. **Watch logs** untuk debugging:
   ```bash
   tail -f storage/logs/laravel.log
   ```

## ✨ Features Ready to Implement

Prioritas Phase 1 (Core Flow):
- [ ] Connect transaksi-token page dengan API
- [ ] Connect daftar-tes page dengan API
- [ ] Connect hasil-tes page dengan API
- [ ] Implement Midtrans payment
- [ ] Implement character analysis algorithm
- [ ] Setup PDF certificate template

Lihat `IMPLEMENTATION_STATUS.md` untuk detail lengkap.

---

**Status**: Dependencies ready ✅ | Database setup needed 🔄 | Ready to develop 🚀
