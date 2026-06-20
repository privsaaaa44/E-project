<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@cinevo.local'],
            [
                'name' => 'Cinevo Admin',
                'phone' => '03000000000',
                'password' => Hash::make('Admin12345'),
                'role' => 'admin',
                'status' => 'Active',
            ]
        );
    }
}
