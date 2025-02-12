<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Administrador',
            'cpf' => '69010146006',
            'email' => 'admin@admin.com',
            'password' => bcrypt('123'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'is_admin' => true,
            'is_active' => true,
            'belongs_sector' => true,
        ]);
    }
}
