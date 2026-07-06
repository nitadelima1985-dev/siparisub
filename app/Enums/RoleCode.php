<?php

namespace App\Enums;

enum RoleCode: string
{
    case SuperAdmin = 'super_admin';
    case AdminDinas = 'admin_dinas';
    case AdminPokdarwis = 'admin_pokdarwis';
    case AdminHumas = 'admin_humas';
    case KontenKreator = 'konten_kreator';
    case ReviewerAkademik = 'reviewer_akademik';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::AdminDinas => 'Admin Dinas',
            self::AdminPokdarwis => 'Admin Pokdarwis',
            self::AdminHumas => 'Admin Humas',
            self::KontenKreator => 'Konten Kreator',
            self::ReviewerAkademik => 'Reviewer Akademik',
        };
    }

    public function dashboardMenus(): array
    {
        return match ($this) {
            self::SuperAdmin => [
                'Manajemen Pengguna',
                'Konfigurasi Role',
                'Audit Aktivitas',
                'Master Data Pariwisata',
            ],
            self::AdminDinas => [
                'Validasi Destinasi',
                'Data Event Kabupaten',
                'Laporan Kinerja Pariwisata',
                'Kolaborasi Stakeholder',
            ],
            self::AdminPokdarwis => [
                'Profil Destinasi',
                'Paket Wisata',
                'Agenda Pokdarwis',
                'Usulan Konten',
            ],
            self::AdminHumas => [
                'Publikasi Berita',
                'Kalender Promosi',
                'Media Sosial',
                'Rilis Informasi',
            ],
            self::KontenKreator => [
                'Draft Artikel',
                'Galeri Foto dan Video',
                'Ide Kampanye',
                'Status Review Konten',
            ],
            self::ReviewerAkademik => [
                'Review Artikel',
                'Catatan Kajian',
                'Rekomendasi Pengembangan',
                'Arsip Validasi Akademik',
            ],
        };
    }
}
