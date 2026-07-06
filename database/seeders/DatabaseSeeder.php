<?php

namespace Database\Seeders;

use App\Enums\RoleCode;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            OrganizationSeeder::class,
            TourismCoreSeeder::class,
        ]);

        $superAdminRole = Role::query()
            ->where('code', RoleCode::SuperAdmin->value)
            ->firstOrFail();

        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'superadmin@siparsub.test')],
            [
                'name' => env('ADMIN_NAME', 'Super Admin SIPARSUB'),
                'password' => env('ADMIN_PASSWORD', 'password'),
                'role_id' => $superAdminRole->id,
            ]
        );
    }
}


