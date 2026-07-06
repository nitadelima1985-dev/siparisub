<?php

namespace Database\Seeders;

use App\Models\ArticleCategory;
use App\Models\DestinationCategory;
use App\Models\District;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TourismCoreSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedDestinationCategories();
        $this->seedDistricts();
        $this->seedArticleCategories();
    }

    private function seedDestinationCategories(): void
    {
        $categories = [
            ['name' => 'Wisata Alam', 'description' => 'Destinasi berbasis lanskap alam, pegunungan, air terjun, dan panorama.'],
            ['name' => 'Wisata Budaya', 'description' => 'Destinasi yang menampilkan tradisi, sejarah, seni, dan budaya lokal.'],
            ['name' => 'Wisata Edukasi', 'description' => 'Destinasi untuk pembelajaran, observasi, dan pengalaman edukatif.'],
            ['name' => 'Wisata Kuliner', 'description' => 'Destinasi yang berfokus pada pengalaman kuliner khas Subang.'],
            ['name' => 'Desa Wisata', 'description' => 'Kawasan desa dengan potensi atraksi, amenitas, dan aktivitas masyarakat.'],
            ['name' => 'Rekreasi Keluarga', 'description' => 'Destinasi rekreasi yang ramah keluarga dan pengunjung umum.'],
        ];

        foreach ($categories as $category) {
            DestinationCategory::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedDistricts(): void
    {
        $districts = [
            'Binong',
            'Blanakan',
            'Ciasem',
            'Ciater',
            'Cibogo',
            'Cijambe',
            'Cikaum',
            'Cipeundeuy',
            'Cipunagara',
            'Cisalak',
            'Compreng',
            'Dawuan',
            'Jalancagak',
            'Kalijati',
            'Kasomalang',
            'Legonkulon',
            'Pabuaran',
            'Pagaden',
            'Pagaden Barat',
            'Pamanukan',
            'Patokbeusi',
            'Purwadadi',
            'Pusakajaya',
            'Pusakanagara',
            'Sagalaherang',
            'Serangpanjang',
            'Subang',
            'Sukasari',
            'Tambakdahan',
            'Tanjungsiang',
        ];

        foreach ($districts as $district) {
            District::updateOrCreate(
                ['slug' => Str::slug($district)],
                ['name' => $district]
            );
        }
    }

    private function seedArticleCategories(): void
    {
        $categories = [
            ['name' => 'Berita Pariwisata', 'description' => 'Informasi terbaru tentang pariwisata Kabupaten Subang.'],
            ['name' => 'Cerita Perjalanan', 'description' => 'Artikel naratif dan pengalaman wisatawan.'],
            ['name' => 'Panduan Wisata', 'description' => 'Tips, rute, dan rekomendasi perjalanan.'],
            ['name' => 'Kajian Akademik', 'description' => 'Tulisan berbasis kajian, review, dan rekomendasi akademik.'],
            ['name' => 'Promosi Event', 'description' => 'Publikasi kegiatan dan agenda pariwisata.'],
        ];

        foreach ($categories as $category) {
            ArticleCategory::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
