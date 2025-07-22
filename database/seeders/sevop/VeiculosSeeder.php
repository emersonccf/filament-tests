<?php

namespace Database\Seeders\Sevop;


use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VeiculosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lê o CSV com os dados e transforma em registros
        $registros = registrosCSV('app/dados/veiculos.csv');
        // Seleciona o primeiro usuário que é o administrador para atribulo a criação e atualização dos registros semeados
        $user = User::all()->first();
        // Array para conter todos os registros a serem inseridos
        $dadosParaInserir = [];
        $timestamp = now();

        // Iterar sobre as linhas do CSV
        foreach ($registros as $record) {
                $dadosParaInserir[] = [
                    'id_modelo' => $record['id_modelo'],
                    'placa' => $record['placa'],
                    'prefixo_veiculo' => $record['prefixo_veiculo'],
                    'direcionamento' => $record['direcionamento'],
                    'local_ativacao' => $record['local_ativacao'],
                    'combustivel' => $record['combustivel'],
                    'status' => $record['status'],
                    'possui_bateria_auxiliar' => $record['possui_bateria_auxiliar'],
                    'possui_gps' => $record['possui_gps'],
                    'quilometragem' => $record['quilometragem'],
                    'data_recebimento' => parseDate(trim($record['data_recebimento'])),
                    'chassi' => $record['chassi'],
                    'renavam' => $record['renavam'],
                    'ano_fabricacao' => $record['ano_fabricacao'],
                    'ano_modelo' => $record['ano_modelo'],
                    'cor' => $record['cor'],
                    'valor_diaria' => str_replace(',', '.', $record['valor_diaria']),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                    'cadastrado_por' => $user->id,
                    'atualizado_por' => $user->id,
                ];
        }
        // Inseri todos os registros de uma vez
        DB::table('veiculos')->insert($dadosParaInserir);
    }
}
