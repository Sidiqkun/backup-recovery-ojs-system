# 📚 Sistem Backup & Restore OJS

Aplikasi panel kontrol berbasis Laravel untuk mengelola backup dan restore data **Open Journal Systems (OJS)** secara aman dan terpusat.

---

## ⚡ Quick Start (Lokal / Laragon)

```bash
composer install
npm install && npm run dev
copy .env.example .env
php artisan key:generate
# → Isi .env (lihat bagian Konfigurasi)
php artisan config:clear
php artisan serve
```

Buka: `http://localhost:8000`

---

## 🔑 Konfigurasi Wajib di `.env`

### 1. Email Admin (Satu-satunya akun yang bisa login)
```ini
ADMIN_EMAIL=emailkamu@gmail.com
```

### 2. Gmail SMTP untuk Kirim OTP
> Buat App Password di: https://myaccount.google.com/apppasswords

```ini
MAIL_MAILER=smtp
MAIL_SCHEME=ssl
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=emailkamu@gmail.com
MAIL_PASSWORD=app_password_16_digit_tanpa_spasi
MAIL_FROM_ADDRESS="emailkamu@gmail.com"
MAIL_FROM_NAME="OJS Backup System"
```

### 3. Path Folder File OJS
```ini
OJS_FILES_PATH=C:\laragon\www\ojs3\files
# Di Linux/cPanel: OJS_FILES_PATH=/home/user/public_html/ojs/files
```

Setelah mengubah `.env`:
```bash
php artisan config:clear
```

---

## 🛠️ Bypass Login untuk Testing (Dev Only)

Tambahkan ke `.env`:
```ini
DEV_SKIP_LOGIN=true
```

Akses: `http://localhost:8000/dev-login` → langsung masuk ke Dashboard.

> ⚠️ Route ini **otomatis tidak aktif** di production (`APP_ENV=production`).
> Set `DEV_SKIP_LOGIN=false` sebelum deploy.

---

## 🌟 Fitur Utama

| Fitur | URL | Keterangan |
|---|---|---|
| ⭐ Full Backup | `/full-backup` | DB + Files dalam 1 ZIP — **Direkomendasikan** |
| Backup Database | `/backup` | SQL dump saja |
| Backup File OJS | `/backup-file` | ZIP folder files/ saja |
| ⚠️ Full Restore | `/full-restore` | Restore DB + Files — **Tidak bisa di-undo** |
| Restore Database | `/restore` | Upload .sql |
| Restore File OJS | `/restore-file` | Upload .zip files |
| Data Jurnal | `/journal` | Daftar jurnal dari DB OJS |

---

## 🛠️ File Debug

Jalankan dari terminal di dalam folder proyek:

| File | Perintah | Fungsi |
|---|---|---|
| `debug_system.php` | `php debug_system.php` | Cek DB, binary, ekstensi, folder |
| `db_explore.php` | `php db_explore.php` | Cek folder file OJS |
| `test_full_backup.php` | `php test_full_backup.php` | Test full backup tanpa browser |
| `test_full_restore.php` | `php test_full_restore.php` | Test full restore |
| `truncate_for_test.php` | `php truncate_for_test.php` | ⚠️ Kosongkan DB (testing) |

---

## 🚨 Troubleshooting

**Email OTP tidak masuk?**
- Pastikan `MAIL_MAILER=smtp` (bukan `log`)
- Pastikan App Password 16 digit sudah benar
- Coba port alternatif: `MAIL_PORT=587` + `MAIL_SCHEME=tls`
- Cek log: `storage/logs/laravel.log`

**Backup gagal?**
- Jalankan `php debug_system.php` → cek Binary Check
- Pastikan folder `storage/` writable

**Login gagal "Email tidak terdaftar"?**
- Pastikan `ADMIN_EMAIL` di `.env` **persis sama** dengan email yang diketik
- Jalankan `php artisan config:clear`

**Upload file terlalu besar saat restore?**
- Edit `php.ini` Laragon: `upload_max_filesize=500M`, `post_max_size=500M`

---

## 📦 Checklist Sebelum Deploy ke Production

- [ ] `APP_ENV=production` dan `APP_DEBUG=false`
- [ ] `DEV_SKIP_LOGIN=false` (atau hapus barisnya)
- [ ] `ADMIN_EMAIL` sudah diisi email production
- [ ] Gmail App Password sudah diisi
- [ ] `OJS_FILES_PATH` mengarah ke path server production
- [ ] `php artisan config:cache` sudah dijalankan
- [ ] Folder `storage/` writable (`chmod -R 775 storage/`)

---

## 📊 Status Kesiapan Sistem

```
Lokal (Development): ████████████░░  90% SIAP
Production Deploy:   ██████████░░░░  75% SIAP
```

**Satu hal yang masih perlu dilakukan:**
→ Isi Gmail App Password di `.env` dan test kirim OTP sungguhan.

---

*Lihat dokumentasi lengkap di: `dokumentasi_sistem.md`*
