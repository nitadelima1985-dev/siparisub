# SIPARISUB

SIPARISUB adalah aplikasi Laravel 11 untuk Sistem Informasi Pariwisata Kabupaten Subang berbasis collaborative governance. Aplikasi ini menyediakan website publik, dashboard multi-role, manajemen destinasi, event, artikel, workflow approval, notifikasi internal, dan notifikasi email.

## Fitur Utama

- Laravel 11 dan MySQL.
- Login menggunakan email dan password.
- Role-based access control untuk `super_admin`, `admin_dinas`, `admin_pokdarwis`, `admin_humas`, `konten_kreator`, dan `reviewer_akademik`.
- Layout terpisah untuk website publik dan dashboard internal.
- Dashboard role-based dengan statistik sesuai peran pengguna.
- CRUD destinasi, event, artikel, pengguna, dan organisasi/aktor kolaborasi.
- Workflow approval untuk destinasi, event, dan artikel.
- Menu Approval terpusat untuk review, approve, publish, dan archive konten.
- Notifikasi internal dashboard dengan ikon lonceng.
- Notifikasi email workflow melalui SMTP.
- Website publik untuk destinasi, event, artikel, landing page, dan statistik dinamis.

## Kebutuhan Sistem

Pastikan environment memiliki:

- PHP sesuai `composer.json` project.
- Composer.
- MySQL/MariaDB.
- Ekstensi PHP umum Laravel: `fileinfo`, `pdo_mysql`, `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `curl`, dan `zip`.

Jika memakai hosting shared seperti Hostinger, pastikan versi PHP, ekstensi PHP, dan akses Composer/SSH tersedia. Jika Composer tidak tersedia di hosting, upload folder `vendor` dari lokal.

## Setup Lokal

Jalankan perintah berikut dari folder project `SIPARISUB`:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Buat database MySQL:

```sql
CREATE DATABASE siparsub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Sesuaikan koneksi database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siparsub
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migration, seeder, storage link, dan server lokal:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Akun awal dari seeder:

- Email: `superadmin@siparsub.test`
- Password: `password`

Nilai ini bisa diubah melalui `ADMIN_EMAIL`, `ADMIN_PASSWORD`, dan `ADMIN_NAME` di `.env` sebelum menjalankan seeder.

## Struktur Penting

- `app/Enums/RoleCode.php`: daftar role dan label role.
- `app/Enums/WorkflowStatus.php`: status workflow konten.
- `app/Models/User.php`: user, relasi role, organisasi, approval, dan notifikasi.
- `app/Models/Role.php`: role user.
- `app/Models/Destination.php`: destinasi wisata.
- `app/Models/Event.php`: event wisata.
- `app/Models/Article.php`: artikel/konten promosi.
- `app/Models/Approval.php`: record approval polymorphic.
- `app/Models/ApprovalLog.php`: histori perubahan workflow.
- `app/Models/Notification.php`: notifikasi internal dashboard.
- `app/Services/DestinationWorkflowService.php`: workflow destinasi.
- `app/Services/EventWorkflowService.php`: workflow event.
- `app/Services/ArticleWorkflowService.php`: workflow artikel.
- `app/Services/WorkflowNotificationService.php`: penyimpanan notifikasi internal dan pengiriman email.
- `app/Mail/WorkflowNotificationMail.php`: email notifikasi workflow.
- `resources/views/layouts/public.blade.php`: layout website publik.
- `resources/views/layouts/dashboard.blade.php`: layout dashboard admin.
- `routes/web.php`: route publik, auth, dashboard, approval, dan notifikasi.

## Database Inti

Migration inti menyiapkan tabel:

- `roles`
- `users`
- `destination_categories`
- `districts`
- `destinations`
- `destination_media`
- `article_categories`
- `articles`
- `events`
- `approvals`
- `approval_logs`
- `activity_logs`
- `organizations`
- `notifications`

Status workflow yang digunakan:

- `draft`
- `submitted`
- `under_review`
- `revision_needed`
- `approved`
- `published`
- `archived`

Untuk project yang sudah pernah menjalankan migration awal, cukup jalankan:

```bash
php artisan migrate
php artisan db:seed
```

## Modul Dashboard

Route dashboard utama:

```text
/dashboard
```

Menu dashboard penting:

- `/dashboard/destinations`: data destinasi wisata.
- `/dashboard/events`: data event wisata.
- `/dashboard/articles`: artikel/konten promosi.
- `/dashboard/approvals`: approval terpusat untuk destinasi, event, dan artikel.
- `/dashboard/notifications`: notifikasi internal.
- `/dashboard/users`: manajemen pengguna.
- `/dashboard/organizations`: manajemen organisasi/aktor kolaborasi.
- `/dashboard/reports`: statistik dan laporan.

Hak akses utama:

- `super_admin` dan `admin_dinas`: mengelola data utama, approval, publish, archive, user, organisasi, dan laporan.
- `admin_pokdarwis` dan `admin_humas`: membuat dan mengelola konten miliknya sesuai modul.
- `konten_kreator`: membuat/mengelola artikel dan konten sesuai izin.
- `reviewer_akademik`: melakukan review dan memberi catatan revisi.

## Workflow Approval

Workflow berlaku untuk destinasi, event, dan artikel:

1. Pengusul membuat konten sebagai `draft`.
2. Pengusul submit konten menjadi `submitted`.
3. Reviewer dapat mengubah ke `under_review` atau `revision_needed`.
4. `admin_dinas` atau `super_admin` dapat `approve`.
5. Konten yang sudah approved dapat `published`.
6. Konten dapat diarsipkan menjadi `archived`.
7. Arsip dapat dibuka kembali melalui aksi buka arsip.

Semua perubahan workflow dicatat pada:

- tabel konten masing-masing melalui field `workflow_status`,
- tabel `approvals`,
- tabel `approval_logs`,
- tabel `notifications`,
- email jika SMTP aktif.

Approval terpusat tersedia di:

```text
/dashboard/approvals
```

Tombol workflow pada detail konten tetap tersedia sebagai akses cepat, tetapi menu Approval adalah antrean utama untuk reviewer/admin.

## Notifikasi Internal dan Email

Notifikasi internal dashboard tersedia melalui ikon lonceng di topbar dan halaman:

```text
/dashboard/notifications
```

Workflow penting juga mengirim email melalui `WorkflowNotificationService`.

Aturan penerima email:

- Saat konten disubmit: `admin_dinas` dan `reviewer_akademik` menerima email.
- Saat konten butuh revisi: pengusul menerima email.
- Saat konten approved: pengusul menerima email.
- Saat konten published: pengusul menerima email.

Contoh konfigurasi SMTP di `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=alamat_email_pengirim
MAIL_PASSWORD=app_password_email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=alamat_email_pengirim
MAIL_FROM_NAME="SIPARISUB"
QUEUE_CONNECTION=sync
```

Jika menggunakan Gmail, gunakan App Password, bukan password utama akun.

Untuk email berbasis queue, ubah `QUEUE_CONNECTION` sesuai driver queue dan jalankan worker:

```bash
php artisan queue:work
```

Cara test notifikasi:

1. Pastikan tabel `notifications` sudah ada dengan `php artisan migrate`.
2. Pastikan user penerima memiliki email valid dan `is_active = true`.
3. Isi konfigurasi SMTP di `.env`.
4. Jalankan `php artisan config:clear`.
5. Submit destinasi/event/artikel dari akun pengusul.
6. Cek ikon lonceng dashboard dan email admin/reviewer.
7. Lakukan revisi, approve, atau publish dari menu Approval.
8. Cek notifikasi internal dan email pengusul.

## Website Publik

Route publik utama:

- `/`: landing page SIPARISUB.
- `/destinasi`: daftar destinasi published.
- `/destinasi/{slug}`: detail destinasi.
- `/event`: daftar event published.
- `/event/{slug}`: detail event.
- `/artikel`: daftar artikel published.
- `/artikel/{slug}`: detail artikel.
- `/peta-wisata`: halaman peta wisata interaktif jika menu diaktifkan kembali.

Konten publik hanya menampilkan data dengan:

- `workflow_status = published`
- `is_active = true`

Landing page memakai statistik dinamis dari database untuk potensi destinasi, aktor kolaborasi, agenda wisata tahun berjalan, dan persentase konten tervalidasi.

## Upload Gambar dan Storage

Gambar destinasi, event, artikel, logo organisasi, dan foto profil disimpan pada disk `public`.

Pastikan storage link sudah dibuat:

```bash
php artisan storage:link
```

Jika gambar tidak tampil di lokal atau hosting, cek:

- folder `storage/app/public`,
- symlink `public/storage`,
- permission folder storage,
- `APP_URL` di `.env`.

## Deployment ke Hostinger

Ada dua cara deployment Laravel ke Hostinger.

### Opsi 1: Upload dengan folder vendor

Gunakan opsi ini jika hosting tidak menyediakan Composer/SSH.

Upload folder/file penting berikut:

```text
app
bootstrap
config
database
public
resources
routes
storage
vendor
artisan
composer.json
composer.lock
.env
```

Pastikan folder `vendor` ikut terupload. Folder `vendor` diperlukan oleh Laravel karena berisi dependency Composer dan file autoload.

### Opsi 2: Install vendor di hosting

Jika hosting menyediakan SSH dan Composer, upload project tanpa `vendor`, lalu jalankan:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Untuk production, setelah konfigurasi sudah benar, jalankan optimasi:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Catatan Hostinger:

- Document root sebaiknya mengarah ke folder `public` Laravel.
- Jika tidak bisa mengubah document root, pindahkan isi folder `public` ke `public_html` dan sesuaikan path `index.php` dengan hati-hati.
- Jangan upload `.env` berisi credential ke tempat publik yang bisa diakses web.
- Pastikan permission `storage` dan `bootstrap/cache` writable.

## Troubleshooting Singkat

### Folder vendor tidak ada

Jalankan:

```bash
composer install
```

Atau upload folder `vendor` dari lokal ke hosting.

### Error bootstrap/cache tidak writable

Pastikan folder berikut ada dan writable:

```text
bootstrap/cache
storage
```

### Error could not find driver

Aktifkan ekstensi PHP `pdo_mysql`.

### Error fileinfo atau zip

Aktifkan ekstensi PHP `fileinfo` dan `zip` di `php.ini` atau panel hosting.

### Gambar upload tidak tampil

Jalankan:

```bash
php artisan storage:link
```

Jika di hosting shared symlink tidak didukung, salin isi `storage/app/public` ke lokasi publik yang sesuai atau gunakan konfigurasi storage hosting.

## Perintah Rutin

```bash
php artisan migrate
php artisan db:seed
php artisan optimize:clear
php artisan view:clear
php artisan route:list
```