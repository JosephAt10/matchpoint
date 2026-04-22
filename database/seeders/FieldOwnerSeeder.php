<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FieldOwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $owners = [
            [
                'name'   => 'James',
                'email'  => 'james@matchpoint.test',
                'status' => 'Active',
            ],
            [
                'name'   => 'Mutias',
                'email'  => 'mutias@matchpoint.test',
                'status' => 'Active',
            ],
            [
                'name'   => 'Pending Owner',
                'email'  => 'pending.owner@matchpoint.test',
                'status' => 'PendingApproval',
            ],
        ];

        foreach ($owners as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('Owner@123456'),
                    'role'     => 'FieldOwner',
                    'status'   => $data['status'],
                ]
            );
        }

        $this->command->info('Field owner accounts created (password: Owner@123456)');
    }
}
