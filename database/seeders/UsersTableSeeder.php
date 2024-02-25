<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run()
    {
        // Tạo tài khoản admin
        User::create([
            'name' => 'Admin',
            'email' => 'root@gmail.com',
            'password' => bcrypt('12341234'),
            'phone' => 'your_phone_number',
            'role' => 'root',
            'status' => 'active',
        ]);

        // Tạo tài khoản customer care
        User::create([
            'name' => 'Customer Care',
            'email' => 'customer_care@gmail.com',
            'password' => bcrypt('12341234'),
            'phone' => 'your_phone_number',
            'role' => 'customer_care',
            'status' => 'active',
        ]);
    }
}
