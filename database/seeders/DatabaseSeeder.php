<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat SuperAdmin jika belum ada
        if (!User::where('email', 'superadmin@kopingaji.com')->exists()) {
            User::create([
                'name'        => 'Super Admin',
                'email'       => 'superadmin@kopingaji.com',
                'password'    => Hash::make('superadmin123'),
                'role'        => 'SUPERADMIN',
                'whatsapp'    => '6281234567890',
                'is_verified' => true,
            ]);
        }

        // Buat Admin contoh jika belum ada
        if (!User::where('email', 'admin@kopingaji.com')->exists()) {
            User::create([
                'name'        => 'Admin Kopi',
                'email'       => 'admin@kopingaji.com',
                'password'    => Hash::make('admin12345'),
                'role'        => 'ADMIN',
                'whatsapp'    => '6281234567891',
                'is_verified' => true,
            ]);
        }

        $this->command->info('✅ SuperAdmin dan Admin berhasil dibuat!');
        $this->command->info('📧 SuperAdmin: superadmin@kopingaji.com | Password: superadmin123');
        $this->command->info('📧 Admin: admin@kopingaji.com | Password: admin12345');
    }
}
