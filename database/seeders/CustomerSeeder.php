<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        Customer::create([
            'first_name' => 'Muhammad',
            'last_name' => 'Fakih',
            'email' => 'fakih@gmail.com',
            'password' => Hash::make('fakih123'),  // Pastikan menggunakan hashing untuk password
            'phone' => '1234567890',
            'photo' => null, // Opsional, jika Anda ingin menyertakan foto
        ]);

        // Tambahkan lebih banyak data sesuai kebutuhan
    }
}
