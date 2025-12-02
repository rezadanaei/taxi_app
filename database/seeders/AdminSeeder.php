<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['username' => 'admin'], 
            [
                'name' => 'ادمین اصلی',
                'username' => 'admin',
                'password' => Hash::make('admin1234'),
                'phone' => '09120000000',
                'type' => 'owner', 
                'status' => 'active', 
            ]
        );
    }
}
