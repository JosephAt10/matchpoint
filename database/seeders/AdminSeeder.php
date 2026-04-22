<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        //
        User::updateOrCreate(
            ['email' => 'admin@matchpoint.test'],
            [
                'name'     => 'MatchPoint Admin',
                'password' => Hash::make('Admin@123456'),
                'role'     => 'Admin',
                'status'   => 'Active',
            ]
        );

        $this->command->info('Admin account created: admin@matchpoint.test / Admin@123456');
    }
}
