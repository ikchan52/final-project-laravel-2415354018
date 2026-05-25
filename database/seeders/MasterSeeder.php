<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // <--- WAJIB ADA INI
use App\Models\Service; // <--- TAMBAHKAN INI JUGA KALAU PAKAI MODEL SERVICE
use Illuminate\Support\Facades\Hash; // <--- WAJIB ADA UNTUK HASH::MAKE

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        User::create([
            'name' => 'Krishna Admin',
            'email' => 'admin@erp.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        // Create Staff (Sesuai Figma)
        User::create([
            'name' => 'Admin Satu',
            'email' => 'staff1@erp.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        // Tambahkan Layanan Dummy
        $service = Service::create([
            'name' => 'Cloud Hosting Pro',
            'price' => 250000,
            'status' => 'active'
        ]);

        // Gunakan perintah ini untuk eksekusi total:
        // php artisan migrate:fresh --seed --seeder=MasterSeeder
    }
}
