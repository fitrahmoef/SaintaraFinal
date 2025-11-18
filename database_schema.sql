-- ============================================================================
-- SAINTARA DATABASE SCHEMA (CORRECTED VERSION)
-- ============================================================================
-- Database: saintara_db
-- DBMS: MySQL 8.0+
-- Character Set: utf8mb4
-- Collation: utf8mb4_unicode_ci
-- Created: 2025-11-18
-- ============================================================================

-- Create database
CREATE DATABASE IF NOT EXISTS saintara_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE saintara_db;

-- ============================================================================
-- 1. USERS TABLE (Master Authentication)
-- ============================================================================
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin', 'superadmin') DEFAULT 'customer',
    user_type ENUM('personal', 'admin', 'instansi') DEFAULT 'personal',
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. CUSTOMERS TABLE (Customer Profile)
-- ============================================================================
CREATE TABLE customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    nama_lengkap VARCHAR(255) NOT NULL,
    nama_panggilan VARCHAR(100) NULL,
    nomor_telepon VARCHAR(20) NULL,
    tanggal_lahir DATE NULL,
    jenis_kelamin ENUM('pria', 'wanita') NULL,
    golongan_darah ENUM('A', 'B', 'AB', 'O', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NULL,
    negara VARCHAR(100) NULL,
    kota VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_nama_lengkap (nama_lengkap)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. ADMINS TABLE (Admin Profile - Superadmin & Admin Instansi)
-- ============================================================================
CREATE TABLE admins (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    tipe_admin ENUM('superadmin', 'admin_instansi') DEFAULT 'admin_instansi',
    nama_admin VARCHAR(255) NOT NULL,
    nama_instansi VARCHAR(255) NULL,
    nomor_telepon VARCHAR(20) NULL,
    status_akun ENUM('aktif', 'tidak_aktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_tipe_admin (tipe_admin),
    INDEX idx_status_akun (status_akun)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. PACKAGES TABLE (Token Packages)
-- ============================================================================
CREATE TABLE packages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_paket VARCHAR(255) NOT NULL,
    harga DECIMAL(12, 2) NOT NULL,
    deskripsi TEXT NULL,
    tipe_paket ENUM('dasar', 'standar', 'premium') DEFAULT 'dasar',
    jumlah_token INT NOT NULL DEFAULT 1,
    masa_aktif_hari INT NOT NULL DEFAULT 365,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    INDEX idx_tipe_paket (tipe_paket),
    INDEX idx_is_active (is_active),
    CONSTRAINT chk_packages_harga_positive CHECK (harga >= 0),
    CONSTRAINT chk_packages_token_positive CHECK (jumlah_token > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. PAYMENT_GATEWAYS TABLE (Master Payment Gateway)
-- ============================================================================
CREATE TABLE payment_gateways (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_gateway VARCHAR(255) NOT NULL,
    kode_gateway VARCHAR(50) NOT NULL UNIQUE,
    logo_url VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    config JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_kode_gateway (kode_gateway),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 6. TRANSACTIONS TABLE (Payment Transactions)
-- ============================================================================
CREATE TABLE transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT UNSIGNED NOT NULL,
    package_id BIGINT UNSIGNED NOT NULL,
    payment_gateway_id BIGINT UNSIGNED NULL,

    -- Payment details
    kode_transaksi VARCHAR(50) NOT NULL UNIQUE,
    jumlah_bayar DECIMAL(12, 2) NOT NULL,
    status_pembayaran ENUM('pending', 'dibayar', 'gagal', 'kadaluarsa', 'refund') DEFAULT 'pending',
    metode_pembayaran VARCHAR(100) NULL,

    -- Gateway specific
    gateway_transaction_id VARCHAR(255) NULL,
    payment_url TEXT NULL,
    payment_metadata JSON NULL,

    -- Timestamps
    waktu_dibuat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    waktu_dibayar TIMESTAMP NULL,
    waktu_kadaluarsa TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE RESTRICT,
    FOREIGN KEY (payment_gateway_id) REFERENCES payment_gateways(id) ON DELETE SET NULL,

    INDEX idx_customer_id (customer_id),
    INDEX idx_package_id (package_id),
    INDEX idx_status_pembayaran (status_pembayaran),
    INDEX idx_kode_transaksi (kode_transaksi),
    INDEX idx_waktu_dibuat (waktu_dibuat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 7. TOKEN_PURCHASES TABLE (Token Purchase Records)
-- ============================================================================
CREATE TABLE token_purchases (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED NOT NULL,
    package_id BIGINT UNSIGNED NOT NULL,

    -- Token details
    kode_token VARCHAR(50) NOT NULL UNIQUE,
    jumlah_token INT NOT NULL,
    jumlah_terpakai INT NOT NULL DEFAULT 0,
    jumlah_tersisa INT GENERATED ALWAYS AS (jumlah_token - jumlah_terpakai) VIRTUAL,

    -- Status & dates
    status ENUM('aktif', 'habis', 'kadaluarsa') DEFAULT 'aktif',
    tanggal_pembelian TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tanggal_kadaluarsa TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE RESTRICT,

    INDEX idx_customer_id (customer_id),
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_status (status),
    INDEX idx_tanggal_kadaluarsa (tanggal_kadaluarsa),
    INDEX idx_kode_token (kode_token),

    CONSTRAINT chk_token_jumlah_positive CHECK (jumlah_token > 0),
    CONSTRAINT chk_token_terpakai_positive CHECK (jumlah_terpakai >= 0),
    CONSTRAINT chk_token_terpakai_valid CHECK (jumlah_terpakai <= jumlah_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 8. TESTS TABLE (Master Test Data)
-- ============================================================================
CREATE TABLE tests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_tes VARCHAR(255) NOT NULL,
    deskripsi_tes TEXT NULL,
    jenis_tes ENUM('kepribadian', 'minat_bakat', 'kecerdasan', 'lainnya') DEFAULT 'kepribadian',
    jumlah_soal INT NOT NULL DEFAULT 0,
    durasi_menit INT NOT NULL DEFAULT 30,
    token_required INT NOT NULL DEFAULT 1,
    metadata JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    INDEX idx_jenis_tes (jenis_tes),
    INDEX idx_is_active (is_active),
    INDEX idx_nama_tes (nama_tes)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 9. TEST_RESULTS TABLE (Test Results & Answers)
-- ============================================================================
CREATE TABLE test_results (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    test_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    token_purchase_id BIGINT UNSIGNED NULL,

    -- Hasil tes
    hasil_karakter VARCHAR(200) NULL,
    deskripsi_hasil TEXT NULL,
    skor INT NULL,
    jawaban JSON NULL,
    analisis JSON NULL,

    -- Metadata
    tanggal_tes TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    waktu_mulai TIMESTAMP NULL,
    waktu_selesai TIMESTAMP NULL,
    durasi_detik INT GENERATED ALWAYS AS (TIMESTAMPDIFF(SECOND, waktu_mulai, waktu_selesai)) VIRTUAL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (token_purchase_id) REFERENCES token_purchases(id) ON DELETE SET NULL,

    INDEX idx_test_id (test_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_token_purchase_id (token_purchase_id),
    INDEX idx_tanggal_tes (tanggal_tes),
    INDEX idx_customer_test (customer_id, test_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 10. TOKEN_USAGE TABLE (Track Token Usage per Test)
-- ============================================================================
CREATE TABLE token_usage (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    token_purchase_id BIGINT UNSIGNED NOT NULL,
    test_result_id BIGINT UNSIGNED NOT NULL UNIQUE,

    -- Usage details
    jumlah_digunakan INT NOT NULL DEFAULT 1,
    keterangan VARCHAR(255) NULL,
    tanggal_penggunaan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (token_purchase_id) REFERENCES token_purchases(id) ON DELETE CASCADE,
    FOREIGN KEY (test_result_id) REFERENCES test_results(id) ON DELETE CASCADE,

    INDEX idx_token_purchase_id (token_purchase_id),
    INDEX idx_test_result_id (test_result_id),
    INDEX idx_tanggal_penggunaan (tanggal_penggunaan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 11. CERTIFICATES TABLE (Test Certificates)
-- ============================================================================
CREATE TABLE certificates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    test_result_id BIGINT UNSIGNED NOT NULL UNIQUE,

    -- Certificate details
    nomor_sertifikat VARCHAR(50) NOT NULL UNIQUE,
    diterbitkan_oleh VARCHAR(100) DEFAULT 'Saintara',
    ttd_digital VARCHAR(255) NULL,
    url_verifikasi VARCHAR(255) UNIQUE NULL,
    format_file ENUM('pdf', 'jpg', 'png') DEFAULT 'pdf',
    file_path VARCHAR(255) NULL,

    -- Status & metadata
    is_active BOOLEAN DEFAULT TRUE,
    tanggal_terbit TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    metadata JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (test_result_id) REFERENCES test_results(id) ON DELETE CASCADE,

    INDEX idx_test_result_id (test_result_id),
    INDEX idx_nomor_sertifikat (nomor_sertifikat),
    INDEX idx_url_verifikasi (url_verifikasi),
    INDEX idx_is_active (is_active),
    INDEX idx_tanggal_terbit (tanggal_terbit)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 12. ACTIVITY_LOGS TABLE (System Activity Logging)
-- ============================================================================
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- Polymorphic relationship
    user_id BIGINT UNSIGNED NULL,
    user_type VARCHAR(50) NULL,

    -- Activity details
    action VARCHAR(100) NOT NULL,
    description TEXT NULL,
    module VARCHAR(50) NULL,

    -- Request metadata
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    request_url VARCHAR(255) NULL,
    request_method VARCHAR(10) NULL,

    -- Additional data
    properties JSON NULL,
    log_level ENUM('info', 'warning', 'error', 'critical') DEFAULT 'info',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_user_type_id (user_type, user_id),
    INDEX idx_action (action),
    INDEX idx_module (module),
    INDEX idx_log_level (log_level),
    INDEX idx_created_at (created_at),
    INDEX idx_user_activity (user_type, user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- SAMPLE DATA FOR TESTING
-- ============================================================================

-- Insert sample payment gateways
INSERT INTO payment_gateways (nama_gateway, kode_gateway, is_active) VALUES
('Midtrans', 'MIDTRANS', TRUE),
('Xendit', 'XENDIT', TRUE),
('Manual Transfer Bank', 'MANUAL_BANK', TRUE);

-- Insert sample packages
INSERT INTO packages (nama_paket, harga, deskripsi, tipe_paket, jumlah_token, masa_aktif_hari) VALUES
('Paket Dasar', 50000.00, '1 Token untuk 1 Tes Kepribadian', 'dasar', 1, 30),
('Paket Standar', 200000.00, '5 Token untuk 5 Tes', 'standar', 5, 90),
('Paket Premium', 500000.00, '15 Token untuk 15 Tes', 'premium', 15, 365);

-- Insert sample test
INSERT INTO tests (nama_tes, deskripsi_tes, jenis_tes, jumlah_soal, durasi_menit, token_required) VALUES
('Tes Kepribadian MBTI', 'Tes untuk mengetahui tipe kepribadian Anda berdasarkan Myers-Briggs Type Indicator', 'kepribadian', 60, 30, 1),
('Tes Minat Bakat', 'Tes untuk mengidentifikasi minat dan bakat Anda', 'minat_bakat', 50, 25, 1),
('Tes Kecerdasan (IQ)', 'Tes untuk mengukur tingkat kecerdasan intelektual', 'kecerdasan', 40, 45, 1);

-- ============================================================================
-- VIEWS (Optional - untuk kemudahan query)
-- ============================================================================

-- View: Customer dengan total transaksi dan token
CREATE OR REPLACE VIEW v_customer_summary AS
SELECT
    c.id,
    c.nama_lengkap,
    u.email,
    COUNT(DISTINCT t.id) as total_transaksi,
    SUM(CASE WHEN t.status_pembayaran = 'dibayar' THEN t.jumlah_bayar ELSE 0 END) as total_pembelian,
    COALESCE(SUM(tp.jumlah_token), 0) as total_token_dibeli,
    COALESCE(SUM(tp.jumlah_terpakai), 0) as total_token_terpakai,
    COALESCE(SUM(tp.jumlah_token - tp.jumlah_terpakai), 0) as total_token_tersisa,
    COUNT(DISTINCT tr.id) as total_tes_diambil,
    COUNT(DISTINCT cert.id) as total_sertifikat
FROM customers c
JOIN users u ON c.user_id = u.id
LEFT JOIN transactions t ON c.id = t.customer_id
LEFT JOIN token_purchases tp ON c.id = tp.customer_id AND tp.deleted_at IS NULL
LEFT JOIN test_results tr ON c.id = tr.customer_id AND tr.deleted_at IS NULL
LEFT JOIN certificates cert ON tr.id = cert.test_result_id AND cert.deleted_at IS NULL
WHERE c.deleted_at IS NULL
GROUP BY c.id, c.nama_lengkap, u.email;

-- View: Active tokens untuk customer
CREATE OR REPLACE VIEW v_active_tokens AS
SELECT
    tp.*,
    c.nama_lengkap,
    p.nama_paket,
    (tp.jumlah_token - tp.jumlah_terpakai) as sisa_token,
    DATEDIFF(tp.tanggal_kadaluarsa, NOW()) as hari_tersisa
FROM token_purchases tp
JOIN customers c ON tp.customer_id = c.id
JOIN packages p ON tp.package_id = p.id
WHERE tp.status = 'aktif'
  AND tp.tanggal_kadaluarsa > NOW()
  AND (tp.jumlah_token - tp.jumlah_terpakai) > 0
  AND tp.deleted_at IS NULL;

-- ============================================================================
-- STORED PROCEDURES (Optional - untuk business logic)
-- ============================================================================

DELIMITER //

-- Procedure: Use token untuk tes
CREATE PROCEDURE sp_use_token(
    IN p_customer_id BIGINT,
    IN p_test_id BIGINT,
    OUT p_token_purchase_id BIGINT,
    OUT p_success BOOLEAN,
    OUT p_message VARCHAR(255)
)
BEGIN
    DECLARE v_token_count INT;
    DECLARE v_token_id BIGINT;

    -- Check if customer has available tokens
    SELECT id INTO v_token_id
    FROM token_purchases
    WHERE customer_id = p_customer_id
      AND status = 'aktif'
      AND tanggal_kadaluarsa > NOW()
      AND (jumlah_token - jumlah_terpakai) > 0
      AND deleted_at IS NULL
    ORDER BY tanggal_kadaluarsa ASC
    LIMIT 1;

    IF v_token_id IS NULL THEN
        SET p_success = FALSE;
        SET p_message = 'Tidak ada token yang tersedia';
        SET p_token_purchase_id = NULL;
    ELSE
        -- Update token usage
        UPDATE token_purchases
        SET jumlah_terpakai = jumlah_terpakai + 1,
            status = CASE
                WHEN (jumlah_token - jumlah_terpakai - 1) = 0 THEN 'habis'
                ELSE 'aktif'
            END
        WHERE id = v_token_id;

        SET p_success = TRUE;
        SET p_message = 'Token berhasil digunakan';
        SET p_token_purchase_id = v_token_id;
    END IF;
END //

DELIMITER ;

-- ============================================================================
-- TRIGGERS (Optional - untuk auto-update)
-- ============================================================================

DELIMITER //

-- Trigger: Auto update token status saat kadaluarsa
CREATE TRIGGER trg_update_token_status_before_update
BEFORE UPDATE ON token_purchases
FOR EACH ROW
BEGIN
    IF NEW.tanggal_kadaluarsa < NOW() AND NEW.status != 'kadaluarsa' THEN
        SET NEW.status = 'kadaluarsa';
    END IF;

    IF (NEW.jumlah_token - NEW.jumlah_terpakai) <= 0 AND NEW.status = 'aktif' THEN
        SET NEW.status = 'habis';
    END IF;
END //

DELIMITER ;

-- ============================================================================
-- EVENTS (Optional - untuk scheduled tasks)
-- ============================================================================

-- Enable event scheduler
SET GLOBAL event_scheduler = ON;

-- Event: Auto expire tokens setiap hari
CREATE EVENT IF NOT EXISTS evt_expire_tokens
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_DATE + INTERVAL 1 DAY
DO
    UPDATE token_purchases
    SET status = 'kadaluarsa'
    WHERE tanggal_kadaluarsa < NOW()
      AND status = 'aktif'
      AND deleted_at IS NULL;

-- ============================================================================
-- GRANT PERMISSIONS (Adjust sesuai kebutuhan)
-- ============================================================================
/*
-- Create application user
CREATE USER 'saintara_app'@'localhost' IDENTIFIED BY 'your_secure_password';

-- Grant permissions
GRANT SELECT, INSERT, UPDATE, DELETE ON saintara_db.* TO 'saintara_app'@'localhost';

-- Grant execute for stored procedures
GRANT EXECUTE ON saintara_db.* TO 'saintara_app'@'localhost';

FLUSH PRIVILEGES;
*/

-- ============================================================================
-- END OF SCHEMA
-- ============================================================================

-- Show all tables
SHOW TABLES;

-- Show table sizes
SELECT
    TABLE_NAME as 'Table',
    ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) as 'Size (MB)'
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'saintara_db'
ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC;
