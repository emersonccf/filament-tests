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
            'password' => bcrypt('123'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'is_admin' => true,
            'is_active' => true,
            'belongs_sector' => true,
        ]);

        User::factory()->create([
            'name' => 'Usuário Teste Adm',
            'cpf' => '15432366002',
            'email' => 'usuario@teste.com',
            'password' => bcrypt('123'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'is_admin' => true,
            'is_active' => true,
            'belongs_sector' => true,
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
