# Non-Functional Requirements - Platform Saintara

## 1. Performance Requirements

### 1.1 Response Time
- **Web Pages:** Halaman web harus dimuat dalam waktu maksimal 3 detik pada koneksi internet standar (10 Mbps)
- **API Endpoints:** Response time API maksimal 500ms untuk 95% request
- **Database Queries:** Query database harus dieksekusi dalam waktu < 200ms untuk operasi standar
- **Search Operations:** Pencarian data harus mengembalikan hasil dalam < 1 detik
- **Test Submission:** Proses submit jawaban tes harus selesai dalam < 2 detik
- **Certificate Generation:** Pembuatan sertifikat harus selesai dalam < 5 detik

### 1.2 Throughput
- **Concurrent Users:** Sistem harus mampu menangani minimal 1000 concurrent users
- **API Rate Limit:**
  - Personal users: 100 requests/minute
  - Admin users: 500 requests/minute
  - Payment Gateway: 50 requests/minute
- **Peak Load:** Sistem harus mampu menangani 3x traffic normal saat peak hours

### 1.3 Resource Usage
- **Memory:** Backend server maksimal menggunakan 2GB RAM per instance
- **CPU:** CPU usage rata-rata < 70% pada normal load
- **Storage:** Database growth maksimal 10GB per bulan
- **Bandwidth:** Upload/download file maksimal 5MB per request

### 1.4 Caching Strategy
- **Static Assets:** Cache selama 7 hari (images, CSS, JS)
- **API Responses:**
  - Master data (packages, tests): Cache 1 jam
  - User profile: Cache 15 menit, invalidate on update
  - Test results: No cache (real-time data)
- **Database Query Cache:** Enable untuk master data tables

## 2. Security Requirements

### 2.1 Authentication & Authorization
- **Password Policy:**
  - Minimal 8 karakter
  - Kombinasi huruf besar, kecil, angka, dan simbol
  - Password harus di-hash menggunakan bcrypt/argon2
  - Maximum 5 failed login attempts sebelum account lock (15 menit)

- **Session Management:**
  - Session timeout: 30 menit inactive
  - Maximum concurrent sessions: 3 per user
  - Secure session cookies (HttpOnly, Secure, SameSite)

- **Two-Factor Authentication (2FA):**
  - Optional untuk personal users
  - Mandatory untuk admin users
  - Support TOTP (Time-based One-Time Password)

- **Role-Based Access Control (RBAC):**
  - Strict separation antara Personal, Admin, Instansi
  - Privilege escalation prevention
  - Activity logging untuk semua admin actions

### 2.2 Data Protection
- **Encryption:**
  - Data in transit: TLS 1.3 (minimum TLS 1.2)
  - Data at rest: AES-256 encryption untuk sensitive data
  - Database encryption untuk kolom sensitif:
    - Nomor telepon
    - Tanggal lahir
    - Payment metadata
    - Digital signatures

- **Personal Identifiable Information (PII):**
  - Email, nama lengkap, nomor telepon harus di-protect
  - Tidak boleh di-log dalam plain text
  - Harus comply dengan data protection regulations

- **Payment Security:**
  - PCI DSS compliance untuk payment data
  - Tidak menyimpan full credit card data
  - Token-based payment processing
  - Payment gateway data encryption

### 2.3 API Security
- **Authentication:** Bearer token (JWT) dengan expiry 24 jam
- **Authorization:** Endpoint-level permission checks
- **Input Validation:** Sanitasi semua input untuk mencegah injection attacks
- **Rate Limiting:** Prevent brute force dan DDoS attacks
- **CORS Policy:** Whitelist specific domains only
- **API Versioning:** Support untuk backward compatibility

### 2.4 Security Monitoring
- **Audit Logging:**
  - Log semua authentication attempts
  - Log semua data modifications
  - Log payment transactions
  - Log admin actions
  - Retention: 2 tahun minimum

- **Intrusion Detection:**
  - Alert pada suspicious activities
  - Monitor unusual login patterns
  - Track failed authentication attempts
  - Monitor SQL injection attempts

- **Vulnerability Management:**
  - Regular security audits (quarterly)
  - Dependency vulnerability scanning
  - Code security scanning (SAST)
  - Penetration testing (annually)

### 2.5 Protection Against Common Attacks
- **SQL Injection:** Prepared statements dan ORM (Eloquent)
- **XSS (Cross-Site Scripting):** Input sanitization dan output encoding
- **CSRF:** CSRF tokens untuk semua state-changing operations
- **Clickjacking:** X-Frame-Options header
- **Command Injection:** Input validation dan sanitization
- **File Upload:** File type validation, size limits, virus scanning

## 3. Scalability Requirements

### 3.1 Horizontal Scalability
- **Application Servers:** Load balancer untuk distribute traffic ke multiple instances
- **Database:** Read replicas untuk distribute read operations
- **File Storage:** Distributed object storage (S3-compatible)
- **Session Storage:** Redis cluster untuk shared sessions

### 3.2 Vertical Scalability
- **Database:** Support untuk database server upgrade tanpa downtime
- **Application:** Efficient resource usage untuk maximize server capacity
- **Caching:** Multi-layer caching (application, database, CDN)

### 3.3 Data Growth
- **Storage Planning:** Capacity untuk 100,000 users dan 1 juta test results
- **Database Partitioning:**
  - activity_logs: Partition by year
  - test_results: Partition by year
  - transactions: Partition by year
- **Archive Strategy:**
  - Activity logs > 2 tahun → archive table
  - Test results > 5 tahun → archive table
  - Transactions > 7 tahun → archive table (tax compliance)

### 3.4 Geographic Distribution
- **CDN:** Static assets served via CDN untuk faster delivery
- **Multiple Regions:** Support untuk multi-region deployment (future)
- **Latency:** Response time < 200ms dalam region yang sama

## 4. Availability & Reliability

### 4.1 Uptime
- **Target Availability:** 99.5% uptime (43.8 hours downtime/year)
- **Scheduled Maintenance:** Maximum 4 jam per bulan (off-peak hours)
- **Unplanned Downtime:** Maximum 1 jam per incident
- **Recovery Time Objective (RTO):** 4 jam
- **Recovery Point Objective (RPO):** 1 jam (maximum data loss)

### 4.2 Backup & Recovery
- **Database Backup:**
  - Full backup: Daily (retained 30 days)
  - Incremental backup: Every 6 hours (retained 7 days)
  - Transaction logs: Continuous (retained 7 days)

- **File Backup:**
  - Daily backup untuk uploaded files
  - Retained 14 days

- **Disaster Recovery:**
  - Backup stored in different geographic location
  - Tested recovery procedure quarterly
  - Documented recovery runbook

### 4.3 Error Handling
- **Graceful Degradation:** Sistem tetap berfungsi meski ada service yang down
- **Circuit Breaker Pattern:** Prevent cascade failures
- **Retry Mechanism:** Automatic retry untuk transient failures (max 3 attempts)
- **Fallback:** Alternative responses saat primary service unavailable

### 4.4 Monitoring & Alerting
- **Health Checks:**
  - Application health endpoint
  - Database connectivity check
  - External service availability check

- **Metrics Monitoring:**
  - Response time
  - Error rate
  - CPU/Memory usage
  - Database performance
  - Active users

- **Alerting:**
  - Critical alerts: < 5 menit response time
  - High priority: < 15 menit response time
  - Medium priority: < 1 jam response time
  - Alert channels: Email, SMS, Slack

## 5. Maintainability

### 5.1 Code Quality
- **Coding Standards:** PSR-12 untuk PHP, Airbnb style guide untuk JavaScript
- **Code Review:** Semua changes harus di-review sebelum merge
- **Static Analysis:** PHPStan/Psalm level 5+ untuk PHP code
- **Linting:** ESLint untuk JavaScript/TypeScript code
- **Code Coverage:** Minimum 70% test coverage untuk critical paths

### 5.2 Documentation
- **Code Documentation:**
  - PHPDoc untuk semua public methods
  - JSDoc untuk complex functions
  - README untuk setiap major module

- **API Documentation:**
  - OpenAPI/Swagger specification
  - Request/response examples
  - Error code documentation

- **Architecture Documentation:**
  - System architecture diagram
  - Database ERD (✅ sudah ada)
  - Data flow diagrams
  - Deployment diagrams

### 5.3 Version Control
- **Git Workflow:** GitFlow atau trunk-based development
- **Commit Messages:** Conventional commits format
- **Branching Strategy:**
  - main/master: Production-ready code
  - develop: Integration branch
  - feature/*: Feature development
  - hotfix/*: Production fixes

- **Release Management:**
  - Semantic versioning (MAJOR.MINOR.PATCH)
  - Changelog untuk setiap release
  - Release notes documentation

### 5.4 Testing
- **Unit Tests:** Test individual components
- **Integration Tests:** Test component interactions
- **E2E Tests:** Test critical user flows
- **Performance Tests:** Load testing sebelum major releases
- **Security Tests:** Vulnerability scanning dan penetration testing

### 5.5 Continuous Integration/Deployment
- **CI Pipeline:**
  - Automated testing pada setiap commit
  - Code quality checks
  - Security scanning
  - Build validation

- **CD Pipeline:**
  - Automated deployment ke staging
  - Manual approval untuk production
  - Rollback capability
  - Blue-green deployment strategy

## 6. Usability Requirements

### 6.1 User Interface
- **Response Time:** UI interactions harus responsive < 100ms
- **Consistency:** Consistent design patterns across all pages
- **Error Messages:** User-friendly error messages (tidak technical)
- **Loading Indicators:** Visual feedback untuk long-running operations
- **Form Validation:** Real-time validation dengan clear error messages

### 6.2 Accessibility
- **WCAG 2.1 Level AA Compliance:**
  - Keyboard navigation support
  - Screen reader compatibility
  - Sufficient color contrast (minimum 4.5:1)
  - Alternative text untuk images
  - Semantic HTML structure

- **Responsive Design:**
  - Mobile-first approach
  - Support untuk screen sizes 320px - 2560px
  - Touch-friendly interfaces (minimum 44x44px touch targets)

### 6.3 User Experience
- **Learning Curve:** New users dapat complete first test dalam < 10 menit
- **Navigation:** Maximum 3 clicks untuk reach any page
- **Help & Support:**
  - Contextual help tooltips
  - FAQ section
  - Contact support form
  - Tutorial videos (optional)

### 6.4 Internationalization (Future)
- **Language Support:** Bahasa Indonesia (primary), English (future)
- **Date/Time Format:** Localized formats
- **Currency:** IDR (primary), support untuk multiple currencies (future)
- **Timezone:** Auto-detect user timezone

## 7. Compatibility Requirements

### 7.1 Browser Support
- **Desktop Browsers:**
  - Chrome (latest 2 versions)
  - Firefox (latest 2 versions)
  - Safari (latest 2 versions)
  - Edge (latest 2 versions)

- **Mobile Browsers:**
  - Chrome Mobile (latest)
  - Safari iOS (latest)
  - Samsung Internet (latest)

### 7.2 Device Support
- **Desktop:** 1366x768 resolution minimum
- **Tablet:** iPad and Android tablets (landscape & portrait)
- **Mobile:** iPhone 6 and newer, Android 8.0+ devices

### 7.3 Operating Systems
- **Desktop:** Windows 10+, macOS 10.15+, Ubuntu 20.04+
- **Mobile:** iOS 13+, Android 8.0+

### 7.4 Third-Party Integration
- **Payment Gateways:** Support multiple payment providers
- **Email Service:** SMTP/API-based email delivery
- **SMS Service:** OTP delivery via SMS gateway
- **Cloud Storage:** S3-compatible object storage

## 8. Compliance & Legal

### 8.1 Data Protection
- **Personal Data Protection:** Comply dengan UU PDP (Undang-Undang Perlindungan Data Pribadi)
- **Data Retention:** Clear retention policies untuk semua data types
- **Right to be Forgotten:** User dapat request data deletion
- **Data Portability:** User dapat export personal data

### 8.2 Audit & Compliance
- **Audit Trail:** Complete audit logs untuk semua transactions
- **Financial Records:** Retain untuk 7 tahun (tax compliance)
- **Certificate Validity:** Digital signatures untuk certificate authenticity
- **Data Breach Notification:** Protocol untuk notify users dalam 72 jam

### 8.3 Terms of Service
- **User Agreement:** Clear terms and conditions
- **Privacy Policy:** Transparent data usage policy
- **Cookie Policy:** Cookie consent dan preferences
- **Refund Policy:** Clear refund terms untuk token purchases

## 9. Portability Requirements

### 9.1 Deployment
- **Container Support:** Docker containerization untuk all services
- **Cloud Agnostic:** Deploy ke AWS, GCP, Azure, atau on-premise
- **Infrastructure as Code:** Terraform/CloudFormation untuk infra management
- **Environment Consistency:** Same configuration across dev, staging, production

### 9.2 Database
- **Database Migration:** Easy migration dari PostgreSQL ↔ MySQL
- **Schema Versioning:** Version-controlled database migrations
- **Data Export:** Support untuk data export dalam multiple formats (CSV, JSON, SQL)

### 9.3 Vendor Independence
- **No Vendor Lock-in:** Minimize dependencies pada specific cloud providers
- **Standard Technologies:** Use industry-standard technologies
- **API Abstraction:** Abstract third-party services behind interfaces

## 10. Operational Requirements

### 10.1 Monitoring
- **Application Monitoring:**
  - Real-time performance metrics
  - Error tracking dan reporting
  - User activity analytics

- **Infrastructure Monitoring:**
  - Server health metrics
  - Network performance
  - Storage usage

### 10.2 Logging
- **Application Logs:**
  - Structured logging (JSON format)
  - Centralized log aggregation
  - Log retention: 90 days active, 1 year archive

- **Access Logs:**
  - HTTP access logs
  - API access logs
  - Admin action logs

### 10.3 Support & Maintenance
- **Bug Fixes:** Critical bugs fixed within 24 hours
- **Security Patches:** Applied within 48 hours of disclosure
- **Feature Updates:** Monthly release cycle
- **Support Hours:** Email support 9am-5pm GMT+7 (weekdays)

### 10.4 Configuration Management
- **Environment Variables:** All configs via environment variables
- **Secret Management:** Encrypted secret storage (Vault, AWS Secrets Manager)
- **Feature Flags:** Toggle features without deployment
- **Configuration Versioning:** Track config changes

## 11. Performance Benchmarks

### 11.1 Key Performance Indicators (KPIs)

| Metric | Target | Measurement |
|--------|--------|-------------|
| Page Load Time | < 3s | 95th percentile |
| API Response Time | < 500ms | 95th percentile |
| Database Query Time | < 200ms | Average |
| Uptime | 99.5% | Monthly |
| Error Rate | < 0.1% | Per 1000 requests |
| Concurrent Users | 1000+ | Peak load |
| Time to First Byte (TTFB) | < 200ms | 95th percentile |
| First Contentful Paint (FCP) | < 1.8s | 75th percentile |
| Largest Contentful Paint (LCP) | < 2.5s | 75th percentile |
| Cumulative Layout Shift (CLS) | < 0.1 | 75th percentile |

### 11.2 Load Testing Scenarios

| Scenario | Users | Duration | Success Criteria |
|----------|-------|----------|------------------|
| Normal Load | 100 concurrent | 30 min | Response time < 500ms, 0% errors |
| Peak Load | 500 concurrent | 15 min | Response time < 1s, < 0.1% errors |
| Stress Test | 1000 concurrent | 10 min | Response time < 2s, < 1% errors |
| Spike Test | 0→1000 in 1 min | 5 min | System remains stable |
| Endurance Test | 200 concurrent | 4 hours | No memory leaks, stable performance |

## 12. Quality Attributes Summary

| Quality Attribute | Priority | Target | Current Status |
|-------------------|----------|--------|----------------|
| Performance | High | < 500ms API response | ⚠️ To be tested |
| Security | Critical | Zero critical vulnerabilities | ⚠️ Security audit needed |
| Scalability | Medium | 1000 concurrent users | ⚠️ Load testing needed |
| Availability | High | 99.5% uptime | ⚠️ To be monitored |
| Maintainability | High | 70% code coverage | ⚠️ Tests to be added |
| Usability | Medium | WCAG 2.1 Level AA | ⚠️ Accessibility audit needed |
| Reliability | High | < 0.1% error rate | ⚠️ To be monitored |
| Portability | Low | Multi-cloud support | ✅ Docker ready |

## 13. Next Steps

### 13.1 Immediate Actions
1. ✅ Setup monitoring tools (Prometheus, Grafana)
2. ⚠️ Implement comprehensive logging
3. ⚠️ Setup automated testing pipeline
4. ⚠️ Conduct security audit
5. ⚠️ Perform load testing

### 13.2 Short-term (1-3 months)
1. Achieve 70% code coverage
2. Complete accessibility audit
3. Implement all security requirements
4. Setup disaster recovery procedures
5. Create comprehensive documentation

### 13.3 Long-term (3-6 months)
1. Implement multi-region deployment
2. Achieve 99.9% uptime
3. Support 5000 concurrent users
4. Complete PCI DSS compliance
5. Implement advanced analytics

---

**Document Version:** 1.0
**Last Updated:** 2025-11-18
**Maintained By:** Saintara Development Team
**Review Schedule:** Quarterly
