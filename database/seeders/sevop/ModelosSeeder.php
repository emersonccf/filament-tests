<?php

namespace Database\Seeders\Sevop;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ModelosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lê o CSV com os dados e transforma em registros
        $registros = registrosCSV('app/dados/modelos.csv');
        // Seleciona o primeiro usuário que é o administrador para atribulo a criação e atualização dos registros semeados
        $user = User::all()->first();
        // Array para conter todos os registros a serem inseridos
        $dadosParaInserir = [];
        $timestamp = now();

        // Iterar sobre as linhas do CSV
        foreach ($registros as $record) {
            $dadosParaInserir[] = [
                'id_marca' => $record['id_marca'],
                'nome_modelo' => $record['nome_modelo'],
                'categoria' => $record['categoria'],
                'numero_portas' => $record['numero_portas'],
                'capacidade_passageiros' => $record['capacidade_passageiros'],
                'numero_rodas' => $record['numero_rodas'],
                'cilindrada' => $record['cilindrada'],
                'peso_bruto' => $record['peso_bruto'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'cadastrado_por' => $user->id,
                'atualizado_por' => $user->id,
            ];

        }
        DB::table('modelos')->insert($dadosParaInserir);
    }
}
