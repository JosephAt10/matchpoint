<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Order matters — seeders with FK dependencies must run after their parents.
     *
     *   1. AdminSeeder        — creates admin (no deps)
     *   2. FieldOwnerSeeder   — creates field owners (no deps)
     *   3. UserSeeder         — creates regular users (no deps)
     *   4. FieldSeeder        — creates fields + time slots (depends on FieldOwner users)
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            FieldOwnerSeeder::class,
            UserSeeder::class,
            FieldSeeder::class,
            FieldImageSeeder::class,
        ]);
    }
}
