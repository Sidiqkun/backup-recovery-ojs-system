# 📚 Dokumentasi Lengkap: Sistem Backup & Restore OJS

**Versi:** 1.0 | **Framework:** Laravel 11 | **Database OJS:** MySQL (`dbs_ojs`)

---

## 🗂️ Daftar Isi

1. [Gambaran Sistem](#1-gambaran-sistem)
2. [Struktur Folder & File Penting](#2-struktur-folder--file-penting)
3. [Cara Pemasangan (Fresh Install)](#3-cara-pemasangan-fresh-install)
4. [Konfigurasi Gmail untuk Verifikasi OTP](#4-konfigurasi-gmail-untuk-verifikasi-otp)
5. [Cara Modifikasi (Ubah Email Admin)](#5-cara-modifikasi-ubah-email-admin)
6. [Fitur & Cara Penggunaan](#6-fitur--cara-penggunaan)
7. [File Debug & Diagnostik](#7-file-debug--diagnostik)
8. [Troubleshooting](#8-troubleshooting)
9. [Deployment ke Server Online](#9-deployment-ke-server-online)

---

## 1. Gambaran Sistem

Sistem ini adalah aplikasi **Laravel** terpisah yang berfungsi sebagai panel kontrol untuk OJS (Open Journal Systems). Aplikasi ini **tidak mengubah core OJS** — ia hanya membaca database OJS dan mengelola folder file OJS dari luar.

### Alur Autentikasi
```
Admin buka /login
  → Masukkan email
  → Sistem memverifikasi email dengan ADMIN_EMAIL di .env
  → Kode OTP 6 digit dikirim via Gmail ke email admin
  → Admin masukkan kode OTP
  → Berhasil masuk ke Dashboard
```

### Teknologi
| Komponen | Teknologi |
|---|---|
| Backend | PHP 8.x + Laravel 11 |
| Frontend | Blade + Tailwind CSS |
| Database | MySQL (membaca `dbs_ojs` OJS) |
| Email OTP | Gmail SMTP (App Password) |
| Backup Engine | `mysqldump` / `ZipArchive` PHP |

---

## 2. Struktur Folder & File Penting

```
si-backupojs/
├── .env                          ← 🔑 FILE UTAMA KONFIGURASI (tidak boleh di-commit)
├── .env.example                  ← Template .env untuk referensi
├── README.md                     ← Dokumentasi singkat (disimpan di repo)
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── OtpAuthController.php     ← Logika login & OTP
│   │   │   ├── BackupController.php      ← Backup database (SQL)
│   │   │   ├── RestoreController.php     ← Restore database
│   │   │   ├── JournalController.php     ← Daftar jurnal dari OJS
│   │   │   └── DashboardController.php   ← Halaman dashboard
│   │   └── Middleware/
│   │       └── AdminAuthMiddleware.php   ← Melindungi route dari akses tanpa login
│   └── Mail/
│       └── OtpMail.php                   ← Kelas email OTP
│
├── resources/views/
│   ├── auth/
│   │   ├── login.blade.php               ← Halaman login (input email)
│   │   └── verify-otp.blade.php          ← Halaman input kode OTP
│   ├── emails/
│   │   └── otp.blade.php                 ← Template HTML email OTP
│   ├── backup.blade.php                  ← Halaman backup database
│   ├── restore.blade.php                 ← Halaman restore database
│   ├── full-backup.blade.php             ← Halaman full backup (DB + Files)
│   ├── full-restore.blade.php            ← Halaman full restore
│   ├── backup-file.blade.php             ← Halaman backup file OJS saja
│   ├── restore-file.blade.php            ← Halaman restore file OJS saja
│   └── journal.blade.php                 ← Daftar jurnal dari database OJS
│
├── routes/web.php                        ← Daftar semua URL/route
│
├── storage/app/
│   ├── backups/                          ← File backup database (.sql)
│   ├── file-backups/                     ← File backup folder OJS (.zip)
│   └── full-backups/                     ← File full backup gabungan (.zip)
│
│── debug_system.php                      ← 🛠️ Skrip diagnostik sistem
├── db_explore.php                        ← 🛠️ Skrip cek folder file OJS
├── test_full_backup.php                  ← 🛠️ Skrip uji proses full backup
└── test_full_restore.php                 ← 🛠️ Skrip uji proses full restore
```

---

## 3. Cara Pemasangan (Fresh Install)

### Prasyarat
- PHP 8.1+
- MySQL 8.x atau MariaDB
- Composer
- Node.js + NPM
- Laragon (Windows) atau XAMPP

### Langkah-langkah

**1. Clone/Ekstrak proyek ke folder Laragon**
```
C:\laragon\www\si-backupojs\
```

**2. Install dependensi PHP**
```bash
composer install
```

**3. Install dependensi frontend**
```bash
npm install
npm run dev
```
> Atau `npm run build` untuk production.

**4. Buat file `.env`**
```bash
cp .env.example .env
php artisan key:generate
```

**5. Isi konfigurasi di file `.env`** (lihat bagian 4 & 5)

**6. Pastikan aplikasi bisa membaca database OJS**
Buat database `dbs_ojs` di MySQL dan impor data OJS Anda.

**7. Bersihkan cache**
```bash
php artisan config:clear
php artisan cache:clear
```

**8. Jalankan aplikasi**
```bash
php artisan serve
```
Buka: `http://localhost:8000`

---

## 4. Konfigurasi Gmail untuk Verifikasi OTP

> [!IMPORTANT]
> Ini adalah satu-satunya konfigurasi yang diperlukan agar email OTP dikirim ke Gmail sungguhan.

### Langkah A: Buat App Password di Google

1. Buka [https://myaccount.google.com/security](https://myaccount.google.com/security)
2. Aktifkan **Verifikasi 2 Langkah (2-Step Verification)**
3. Buka [https://myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)
4. Pada kolom "App Name", ketik: `OJS Backup System`
5. Klik **Create** → Salin kode 16 digit yang muncul (contoh: `abcd efgh ijkl mnop`)

### Langkah B: Isi di file `.env`

Buka file `.env` di root proyek dan ganti bagian berikut:

```ini
# =============================================
# GANTI INI: Email admin yang berhak login & menerima OTP
# =============================================
ADMIN_EMAIL=emailkamu@gmail.com

# =============================================
# KONFIGURASI EMAIL (Gmail SMTP)
# =============================================
MAIL_MAILER=smtp
MAIL_SCHEME=ssl
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=emailkamu@gmail.com
MAIL_PASSWORD=abcdefghijklmnop   ← Kode 16 digit (tanpa spasi)
MAIL_FROM_ADDRESS="emailkamu@gmail.com"
MAIL_FROM_NAME="OJS Backup System"
```

> **Catatan:** `ADMIN_EMAIL` dan `MAIL_USERNAME` harus **email yang sama** (keduanya email Gmail Anda).

### Langkah C: Bersihkan cache konfigurasi
```bash
php artisan config:clear
```

### Langkah D: Test kirim email
Coba login di `http://localhost:8000/login` → masukkan email Anda → kode OTP akan masuk ke inbox Gmail Anda dalam beberapa detik.

---

## 5. Cara Modifikasi (Ubah Email Admin)

Karena sistem ini menggunakan **satu email admin saja**, cara modifikasinya sangat mudah:

### Hanya Edit 3 Baris di `.env`

```ini
ADMIN_EMAIL=email_baru@gmail.com       ← Baris 1: email yang boleh login
MAIL_USERNAME=email_baru@gmail.com     ← Baris 2: email pengirim OTP
MAIL_FROM_ADDRESS="email_baru@gmail.com" ← Baris 3: nama pengirim di inbox
```

Lalu jalankan:
```bash
php artisan config:clear
```

> **Tidak perlu mengubah file PHP apapun.** Seluruh logika sudah otomatis membaca dari `.env`.

---

## 6. Fitur & Cara Penggunaan

### A. 🔒 Login dengan OTP
1. Buka URL aplikasi → Anda akan diarahkan ke `/login`
2. Masukkan email admin (sesuai `ADMIN_EMAIL` di `.env`)
3. Klik **Kirim Kode OTP**
4. Buka inbox Gmail Anda → cari email dari "OJS Backup System"
5. Masukkan 6 digit kode ke halaman verifikasi
6. Kode berlaku **5 menit** saja

---

### B. 🗄️ Backup Database (SQL)
- **URL:** `/backup`
- **Format Output:** `backup_ojs_YYYY-MM-DD_HH-MM-SS.sql`
- **Lokasi file:** `storage/app/backups/`
- **Gunakan jika:** Hanya data database yang perlu disimpan (artikel, jurnal, user)
- **Cara:** Klik tombol "Buat Backup Sekarang" → tunggu → link download muncul

---

### C. 📦 Backup File OJS (ZIP)
- **URL:** `/backup-file`
- **Format Output:** `.zip` berisi isi folder `files/` OJS
- **Lokasi file:** `storage/app/file-backups/`
- **Gunakan jika:** PDF artikel, cover jurnal, atau file attachment hilang/rusak

---

### D. 🌟 Full Backup (Paling Direkomendasikan)
- **URL:** `/full-backup`
- **Format Output:** `full_backup_YYYY-MM-DD_HH-MM-SS.zip` berisi: `database.sql` + folder `files/`
- **Lokasi file:** `storage/app/full-backups/`
- **Gunakan jika:** Mau migrasi server, disaster recovery, atau backup rutin bulanan

---

### E. 🔄 Restore
| Jenis | URL | Aksi |
|---|---|---|
| Database saja | `/restore` | Upload file `.sql` |
| File OJS saja | `/restore-file` | Upload file `.zip` file backup |
| Full (DB + Files) | `/full-restore` | Upload file `.zip` full backup |

> ⚠️ **PERINGATAN:** Proses restore akan **menghapus** data yang ada saat ini dan menggantinya dengan isi backup. Tidak bisa di-undo.

---

### F. 📋 Daftar Jurnal
- **URL:** `/journal`
- Menampilkan semua jurnal yang terdaftar di database OJS
- Menampilkan jumlah artikel per jurnal

---

## 7. File Debug & Diagnostik

Terdapat beberapa skrip PHP diagnostik di root proyek. Jalankan dengan `php namafile.php` dari terminal di dalam folder proyek.

### `debug_system.php`
**Kapan digunakan:** Saat pertama kali setup atau terjadi error aneh.
```bash
php debug_system.php
```
**Mengecek:**
- Variabel environment (APP_URL, ADMIN_EMAIL, OJS_FILES_PATH, DB_DATABASE)
- Koneksi database dan jumlah tabel
- Lokasi binary `mysqldump.exe` dan `mysql.exe` di Laragon
- Ekstensi PHP yang dibutuhkan (`ZipArchive`)
- Keberadaan folder-folder backup

---

### `db_explore.php`
**Kapan digunakan:** Untuk memastikan folder file OJS ditemukan dan valid.
```bash
php db_explore.php
```
**Menampilkan:**
- Path folder OJS files
- Jumlah total file dan ukuran (MB)
- Jenis file (PDF, DOCX, dll.)
- 5 folder teratas di dalam folder OJS

---

### `test_full_backup.php`
**Kapan digunakan:** Saat ingin menguji apakah proses full backup berjalan tanpa error, tanpa harus buka browser.
```bash
php test_full_backup.php
```
**Menjalankan:** Proses backup lengkap (dump DB + zip files) dan melaporkan hasilnya.

---

### `test_full_restore.php`
**Kapan digunakan:** Untuk menguji proses restore dari file backup yang sudah ada.
```bash
php test_full_restore.php
```
**Menjalankan:** Proses extract zip + restore SQL ke database.

---

### `truncate_for_test.php`
**Kapan digunakan:** Saat ingin mengosongkan database OJS untuk pengujian restore.
```bash
php truncate_for_test.php
```
> ⚠️ **HATI-HATI:** File ini akan menghapus data. Hanya gunakan di environment development/testing.

---

## 8. Troubleshooting

### ❌ Email OTP Tidak Terkirim

| Kemungkinan Penyebab | Solusi |
|---|---|
| `MAIL_MAILER` masih `log` | Ganti ke `smtp` di `.env` |
| App Password salah | Pastikan kode 16 digit tanpa spasi |
| 2-Step Verification belum aktif | Aktifkan di akun Google dulu |
| Port 465 diblokir (jarang di lokal) | Coba ganti ke `MAIL_PORT=587` dan `MAIL_SCHEME=tls` |

**Cara cek log email saat masih `MAIL_MAILER=log`:**
```
storage/logs/laravel.log
```
Cari baris bertanda `To:` atau `Subject: Kode Verifikasi`.

---

### ❌ Proses Backup Gagal

| Kemungkinan Penyebab | Solusi |
|---|---|
| `mysqldump.exe` tidak ditemukan | Jalankan `php debug_system.php` dan cek bagian Binary Check |
| Folder `storage/app/backups` tidak ada | Sistem akan otomatis membuatnya, pastikan permission folder `storage/` writable |
| File backup berukuran 0 byte | Password database salah atau database tidak ada |

---

### ❌ Restore Gagal dengan "File Too Large"

Edit `php.ini` di Laragon:
1. Buka Laragon → klik kanan tray icon → PHP → `php.ini`
2. Cari dan ubah:
```ini
upload_max_filesize = 500M
post_max_size = 500M
max_execution_time = 300
memory_limit = 512M
```
3. Restart Laragon

---

### ❌ Login Gagal ("Email tidak terdaftar sebagai Admin")

Pastikan `ADMIN_EMAIL` di `.env` **persis sama** dengan email yang Anda ketik di halaman login (case-sensitive). Lalu jalankan:
```bash
php artisan config:clear
```

---

## 9. Deployment ke Server Online

### Checklist Deployment

- [ ] Upload semua file (kecuali `node_modules`, `vendor`, `.env`)
- [ ] Jalankan `composer install --no-dev` di server
- [ ] Buat file `.env` baru di server (jangan copy dari lokal)
- [ ] Isi `.env` server dengan data production (DB, email, dll)
- [ ] Jalankan `php artisan key:generate`
- [ ] Jalankan `php artisan config:cache`
- [ ] Set `APP_DEBUG=false` dan `APP_ENV=production`
- [ ] Pastikan folder `storage/` writable: `chmod -R 775 storage/`
- [ ] Pastikan `OJS_FILES_PATH` mengarah ke path file OJS yang benar di server

### Contoh `.env` untuk Production (cPanel/VPS Linux)

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://backupojs.universitasanda.ac.id

ADMIN_EMAIL=admin@universitasanda.ac.id

DB_HOST=localhost
DB_DATABASE=nama_db_ojs_di_server
DB_USERNAME=db_user_server
DB_PASSWORD=password_db_server

OJS_FILES_PATH=/home/username/public_html/ojs/files

MAIL_MAILER=smtp
MAIL_SCHEME=ssl
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=admin@universitasanda.ac.id
MAIL_PASSWORD=app_password_16_digit
MAIL_FROM_ADDRESS="admin@universitasanda.ac.id"
MAIL_FROM_NAME="OJS Backup System"
```

---

*Dokumentasi ini dibuat untuk administrator sistem OJS. Perbarui jika ada perubahan konfigurasi.*
