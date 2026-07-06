<?php

namespace Database\Seeders;

use App\Enums\OrganizationType;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = [
            [
                'name' => 'Dinas Pariwisata Kabupaten Subang',
                'organization_type' => OrganizationType::DinasPariwisata,
                'description' => 'Aktor pemerintah daerah yang mengoordinasikan pengelolaan dan publikasi informasi pariwisata Kabupaten Subang.',
                'address' => 'Kabupaten Subang',
            ],
            [
                'name' => 'FKIP Universitas Subang',
                'organization_type' => OrganizationType::Universitas,
                'description' => 'Aktor akademik pendukung kajian, edukasi, dan penguatan tata kelola kolaboratif pariwisata.',
                'address' => 'Universitas Subang',
            ],
            [
                'name' => 'LPPM Universitas Subang',
                'organization_type' => OrganizationType::Lppm,
                'description' => 'Lembaga penelitian dan pengabdian masyarakat yang mendukung riset dan pendampingan pariwisata daerah.',
                'address' => 'Universitas Subang',
            ],
            [
                'name' => 'Pokdarwis Contoh',
                'organization_type' => OrganizationType::Pokdarwis,
                'description' => 'Kelompok sadar wisata contoh untuk pengelolaan data destinasi berbasis masyarakat.',
                'address' => 'Kabupaten Subang',
            ],
            [
                'name' => 'Humas Destinasi Contoh',
                'organization_type' => OrganizationType::HumasDestinasi,
                'description' => 'Aktor humas destinasi contoh yang mendukung publikasi informasi operasional dan promosi.',
                'address' => 'Kabupaten Subang',
            ],
        ];

        foreach ($organizations as $organization) {
            Organization::updateOrCreate(
                ['slug' => Str::slug($organization['name'])],
                [
                    ...$organization,
                    'organization_type' => $organization['organization_type']->value,
                    'is_active' => true,
                ]
            );
        }
    }
}
