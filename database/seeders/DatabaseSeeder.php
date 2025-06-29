<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         // --- PERUBAHAN DI SINI ---
        // Matikan pengecekan foreign key constraint sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Kosongkan tabel users (dan products, orders jika Anda punya seeder untuk itu dan ingin truncate juga)
        User::truncate();
        // Pastikan Anda juga mengosongkan tabel orders dan products SEBELUM users
        // karena orders dan products memiliki foreign key ke users.
        // Urutan TRUNCATE harus kebalikan dari urutan CREATE TABLE.
        // Jika orders punya foreign key ke products, maka products di-truncate setelah orders.

        // Jika Anda ingin membersihkan semua tabel setiap kali seed:
        // Hapus data dari tabel yang memiliki foreign key terlebih dahulu
        \App\Models\Order::truncate();
        \App\Models\Product::truncate();
        \App\Models\User::truncate(); // Ini akan menjadi aman sekarang

        // Aktifkan kembali pengecekan foreign key constraint
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        // --- AKHIR PERUBAHAN ---

        // Buat user Admin
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'), // Password: admin123
        ]);

        // Buat user Customer
        User::factory()->create([
            'name' => 'Customer User',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('customer123'), // Password: customer123
        ]);
        
        $this->command->info('Admin and Customer users seeded successfully!');
    }
}
