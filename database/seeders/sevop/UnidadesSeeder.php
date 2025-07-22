<?php

namespace Database\Seeders\Sevop;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lê o CSV com os dados e transforma em registros
        $registros = registrosCSV('app/dados/unidades.csv');
        // Seleciona o primeiro usuário que é o administrador para atribulo a criação e atualização dos registros semeados
        $user = User::all()->first();
        // Array para conter todos os registros a serem inseridos
        $dadosParaInserir = [];
        $timestamp = now();

        // Iterar sobre as linhas do CSV
        foreach ($registros as $record) {
            $dadosParaInserir[] = [
                'nome_unidade' => $record['nome_unidade'],
                'codigo_unidade' => $record['codigo_unidade'],
                'telefone' => $record['telefone'],
                'responsavel' => $record['responsavel'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'cadastrado_por' => $user->id,
                'atualizado_por' => $user->id,
            ];

        }
        DB::table('unidades')->insert($dadosParaInserir);
    }
}
