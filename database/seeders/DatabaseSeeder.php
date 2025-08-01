<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\sefit\PessoasSeeder;
use Database\Seeders\sevop\AlocacaoVeiculosUnidadesSeeder;
use Database\Seeders\sevop\MarcasSeeder;
use Database\Seeders\sevop\ModelosSeeder;
use Database\Seeders\sevop\UnidadesSeeder;
use Database\Seeders\sevop\VeiculosSeeder;
use Illuminate\Database\Seeder;
use App\Models\HistoricoVeiculo;

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
            'password' => bcrypt('Admin@123'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'is_admin' => true,
            'is_active' => true,
            'belongs_sector' => true,
        ]);

        User::factory()->create([
            'name' => 'Sonia Carvalho dos Santos',
            'cpf' => '31804594504',
            'email' => 'soniacs@teste.com',
            'password' => bcrypt('S@nia123'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'is_admin' => false,
            'is_active' => true,
            'belongs_sector' => false,
        ]);

        User::factory()->create([
            'name' => 'Kauane Mariano Gonzaga da Silva',
            'cpf' => '08407881503',
            'email' => 'kauanemgs@teste.com',
            'password' => bcrypt('K@uane123'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'is_admin' => false,
            'is_active' => true,
            'belongs_sector' => false,
        ]);

        User::factory()->create([
            'name' => 'Usuário de Teste',
            'cpf' => '15432366002',
            'email' => 'usuario@teste.com',
            'password' => bcrypt('Usu@rio123'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'is_admin' => false,
            'is_active' => true,
            'belongs_sector' => false,
        ]);

        $this->call([PessoasSeeder::class]);
        $this->call([MarcasSeeder::class]);
        $this->call([ModelosSeeder::class]);
        $this->call([VeiculosSeeder::class]);
        $this->call([UnidadesSeeder::class]);
        $this->call([AlocacaoVeiculosUnidadesSeeder::class]);
        // Agora, crie os históricos de veículos
//        HistoricoVeiculo::factory(1000)->create(); // Cria 1000 registros de histórico de veículos
    }
}
