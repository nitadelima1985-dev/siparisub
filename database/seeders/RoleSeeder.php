<?php

namespace Database\Seeders;

use App\Enums\RoleCode;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (RoleCode::cases() as $role) {
            Role::updateOrCreate(
                ['code' => $role->value],
                [
                    'name' => $role->label(),
                    'description' => $this->descriptionFor($role),
                ]
            );
        }
    }

    private function descriptionFor(RoleCode $role): string
    {
        return match ($role) {
            RoleCode::SuperAdmin => 'Mengelola seluruh konfigurasi sistem, user, role, dan audit.',
            RoleCode::AdminDinas => 'Mengelola data strategis pariwisata dan validasi lintas stakeholder.',
            RoleCode::AdminPokdarwis => 'Mengelola informasi destinasi, paket wisata, dan aktivitas Pokdarwis.',
            RoleCode::AdminHumas => 'Mengelola publikasi, promosi, dan komunikasi pariwisata.',
            RoleCode::KontenKreator => 'Membuat dan mengusulkan konten kreatif pariwisata.',
            RoleCode::ReviewerAkademik => 'Melakukan review akademik dan memberi rekomendasi pengembangan.',
        };
    }
}
