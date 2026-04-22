<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $users = [
            ['name' => 'Pirates Dee',   'email' => 'pirates@matchpoint.test'],
            ['name' => 'Kuol Palmer',   'email' => 'kuol@matchpoint.test'],
            ['name' => 'Gabby Dor',  'email' => 'gabby@matchpoint.test'],
            ['name' => 'Deng Caom', 'email' => 'deng@matchpoint.test'],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('User@123456'),
                    'role'     => 'User',
                    'status'   => 'Active',
                ]
            );
        }

        $this->command->info('Sample user accounts created (password: User@123456)');
    }
}
