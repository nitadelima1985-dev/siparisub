<<<<<<< HEAD
# SIPARSUB

SIPARSUB adalah fondasi aplikasi Laravel 11 untuk Sistem Informasi Pariwisata Kabupaten Subang berbasis collaborative governance dengan dashboard multi-role.

## Fitur tahap awal

- Laravel 11 dan MySQL.
- Login email dan password.
- Role-based access control untuk `super_admin`, `admin_dinas`, `admin_pokdarwis`, `admin_humas`, `konten_kreator`, dan `reviewer_akademik`.
- Layout terpisah untuk halaman publik dan admin panel.
- Dashboard Bootstrap dengan menu berbeda sesuai role.
- Middleware `role` untuk pembatasan akses route.
- Seeder role dan akun super admin awal.
- Fondasi database inti pariwisata: destinasi, media, artikel, event, approval workflow, dan activity log.

## Setup

Pastikan PHP 8.2+, Composer, MySQL, dan ekstensi PHP untuk Laravel sudah tersedia.

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

Jalankan migrasi dan seeder:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Akun awal dari seeder:

- Email: `superadmin@siparsub.test`
- Password: `password`

Nilai ini bisa diubah melalui `ADMIN_EMAIL`, `ADMIN_PASSWORD`, dan `ADMIN_NAME` di `.env` sebelum menjalankan seeder.

## Struktur penting

- `app/Enums/RoleCode.php` mendefinisikan role, label, dan menu dashboard per role.
- `app/Models/Role.php` model role dan relasi ke user.
- `app/Models/User.php` model user, relasi role, dan helper `hasRole()`.
- `app/Http/Requests/Auth/LoginRequest.php` validasi dan proses autentikasi login.
- `app/Http/Middleware/EnsureUserHasRole.php` middleware pembatasan akses berdasarkan role.
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php` controller login/logout.
- `app/Http/Controllers/Dashboard/DashboardController.php` dashboard berdasarkan role pengguna.
- `database/migrations/*roles*` dan `*users*` menyiapkan tabel role, user, password reset, dan session.
- `database/seeders/RoleSeeder.php` mengisi role awal.
- `database/seeders/DatabaseSeeder.php` menjalankan role seeder dan membuat super admin awal.
- `routes/web.php` mendefinisikan halaman publik, auth, dashboard, dan contoh route terproteksi role.
- `resources/views/layouts/public.blade.php` layout halaman publik.
- `resources/views/layouts/dashboard.blade.php` layout admin panel.
- `app/Enums/WorkflowStatus.php` enum status workflow untuk destinasi, artikel, event, dan approval.
- `database/migrations/2026_07_05_000003_create_tourism_core_tables.php` migration tabel inti pariwisata.
- `database/seeders/TourismCoreSeeder.php` master data kategori destinasi, kecamatan, dan kategori artikel.

## Database inti pariwisata

Migration inti membuat tabel:

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

Status workflow yang digunakan:

- `draft`
- `submitted`
- `under_review`
- `revision_needed`
- `approved`
- `published`
- `archived`

Untuk project yang sudah pernah menjalankan migration tahap auth, cukup jalankan:

```bash
php artisan migrate
php artisan db:seed --class=TourismCoreSeeder
```

Atau jalankan semua seeder idempotent:

```bash
php artisan db:seed
```

## Contoh pembatasan akses route

```php
Route::view('/dashboard/users', 'dashboard.placeholder', ['title' => 'Manajemen Pengguna'])
    ->middleware('role:super_admin');
```

Untuk beberapa role:

```php
Route::middleware('role:super_admin,admin_dinas')->group(function () {
    // route khusus super admin dan admin dinas
});
```

## Modul manajemen destinasi

Modul destinasi internal tersedia di:

```text
/dashboard/destinations
```

Fitur yang tersedia:

- Daftar destinasi dengan filter kategori, kecamatan, dan status workflow.
- Tambah, edit, dan detail destinasi.
- Simpan draft dan submit review.
- Upload cover image destinasi ke disk `public`.
- Review workflow untuk reviewer, admin dinas, dan super admin.

Hak akses utama:

- `super_admin` dan `admin_dinas` dapat melihat serta mengedit semua destinasi.
- `admin_pokdarwis` dan `admin_humas` dapat membuat dan mengedit destinasi yang dibuat sendiri.
- `reviewer_akademik` dapat melihat dan memberi review, tetapi tidak mengedit data inti.
- `konten_kreator` tidak memiliki akses pengelolaan data inti destinasi.

Jika cover image tidak tampil, pastikan symlink storage sudah dibuat:

```bash
php artisan storage:link
```

## Workflow approval destinasi

Alur status destinasi:

- `draft` dibuat oleh pengusul.
- `submitted` setelah tombol Submit for Review dikirim.
- `under_review` ditandai oleh `reviewer_akademik`.
- `revision_needed` dikirim reviewer dengan catatan revisi.
- `approved` ditetapkan oleh `admin_dinas` atau `super_admin`.
- `published` hanya dapat dilakukan oleh `admin_dinas` atau `super_admin` setelah approved.
- `archived` hanya dapat dilakukan oleh `admin_dinas` atau `super_admin`.

Semua perubahan workflow memperbarui `destinations.workflow_status`, membuat atau memperbarui record `approvals`, dan menambah histori `approval_logs` melalui `App\Services\DestinationWorkflowService`.

Route action utama:

- `POST /dashboard/destinations/{destination}/submit`
- `POST /dashboard/destinations/{destination}/review/under-review`
- `POST /dashboard/destinations/{destination}/review/revision-needed`
- `POST /dashboard/destinations/{destination}/approve`
- `POST /dashboard/destinations/{destination}/publish`
- `POST /dashboard/destinations/{destination}/archive`

Untuk halaman publik, gunakan scope Eloquent `Destination::published()` agar destinasi yang belum published tidak tampil.
## Modul CRUD event wisata

Modul event internal tersedia di:

```text
/dashboard/events
```

Fitur yang tersedia:

- Daftar event dengan filter status workflow, destinasi, dan rentang tanggal.
- Tambah, edit, dan detail event.
- Upload cover atau poster event ke disk `public`.
- Simpan draft dan submit review.
- Review oleh `reviewer_akademik` ke `under_review` atau `revision_needed`.
- Approve, publish, dan archive oleh `admin_dinas` atau `super_admin`.
- Semua transisi workflow dicatat lewat `App\Services\EventWorkflowService` ke `events`, `approvals`, dan `approval_logs`.

Untuk halaman publik, gunakan scope Eloquent `Event::published()` agar event yang belum published tidak tampil.
## Modul CRUD artikel

Modul artikel internal tersedia di:

```text
/dashboard/articles
```

Fitur yang tersedia:

- Daftar artikel dengan filter status workflow, kategori, dan destinasi.
- Tambah, edit, dan detail artikel.
- Upload featured image ke disk `public`.
- Simpan draft dan submit review.
- Review oleh `reviewer_akademik` ke `under_review` atau `revision_needed`.
- Approve, publish, dan archive oleh `admin_dinas` atau `super_admin`.
- Semua transisi workflow dicatat lewat `App\Services\ArticleWorkflowService` ke `articles`, `approvals`, dan `approval_logs`.

Untuk halaman publik, gunakan scope Eloquent `Article::published()` agar artikel yang belum published tidak tampil.
## Dashboard role-based

Halaman `/dashboard` menampilkan statistik berbeda sesuai role pengguna:

- `super_admin` dan `admin_dinas`: total destinasi, event, artikel, pending review, revision needed, published, pengajuan terbaru, dan konten yang perlu diputuskan.
- `admin_pokdarwis`: konten miliknya, status draft/submitted/revision needed, dan daftar revisi terbaru.
- `admin_humas`: destinasi, event, artikel miliknya, dan revisi yang harus ditindaklanjuti.
- `konten_kreator`: statistik artikel miliknya berdasarkan status workflow.
- `reviewer_akademik`: total submitted, under review, revision needed, dan konten terbaru yang perlu direview.
## Email notification workflow

Workflow destinasi, event, dan artikel dapat mengirim notifikasi internal dan email melalui `App\Services\WorkflowNotificationService`.

Jalankan migration baru untuk tabel notifikasi:

```bash
php artisan migrate
```

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

Jika memakai Gmail, gunakan App Password, bukan password utama akun. Untuk pengiriman email lewat queue, ubah `QUEUE_CONNECTION` ke driver queue yang dipakai dan jalankan worker:

```bash
php artisan queue:work
```

Cara test cepat:

1. Pastikan SMTP di `.env` valid.
2. Jalankan `php artisan config:clear`.
3. Login sebagai pengusul lalu submit destinasi/event/artikel.
4. Cek email user `admin_dinas` dan `reviewer_akademik` aktif.
5. Login sebagai reviewer/admin, kirim revisi/approve/publish.
6. Cek email pengusul dan tabel `notifications`.
=======
# siparisub
sistem informasi pariwisata subang
>>>>>>> b3ed20ac6feafb090e3bbf954283aebb93ac437f
