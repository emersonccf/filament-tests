<?php

namespace Database\Seeders\Sevop;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarcasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lê o CSV com os dados e transforma em registros
        $registros = registrosCSV('app/dados/marcas.csv');
        // Seleciona o primeiro usuário que é o administrador para atribulo a criação e atualização dos registros semeados
        $user = User::all()->first();
        // Array para conter todos os registros a serem inseridos
        $dadosParaInserir = [];
        $timestamp = now();

        // Iterar sobre as linhas do CSV
        foreach ($registros as $record) {
            $dadosParaInserir[] = [
                'nome_marca' => $record['nome_marca'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'cadastrado_por' => $user->id,
                'atualizado_por' => $user->id,
            ];

        }
        DB::table('marcas')->insert($dadosParaInserir);
    }
}
