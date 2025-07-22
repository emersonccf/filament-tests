<?php

namespace Database\Seeders\Sefit;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PessoasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lê o CSV com os dados e transforma em registros
        $registros = registrosCSV('app/dados/dados-syscad-sefit.csv');
        // Seleciona o primeiro usuário que é o administrador para atribulo a criação e atualização dos registros semeados
        $user = User::all()->first();
        // Array para conter todos os registros a serem inseridos
        $dadosParaInserir = [];
        $timestamp = now();

        // Iterar sobre as linhas do CSV
        foreach ($registros as $record) {
            $dadosParaInserir[] = [
                'rus_id' => $record['rus_id'],
                'uuid_id' => !empty(trim($record['uuid_id'])) ? $record['uuid_id'] : Str::uuid(),
                'matricula' => trim($record['matricula']) ?: null,
                'registro_unico' => trim($record['registro_unico']) ?: null,
                'foto' => trim($record['foto']) ?: null,
                'nome' => trim($record['nome']),
                'ativo' => !encontraPalavras(['APOSENTADO','FALECIDO','EXONERAÇÃO','EXT'], trim($record['observacoes'])),
                'sexo' => trim($record['sexo']) ?: null,
                'data_nascimento' => parseDate(trim($record['data_nascimento'])),
                'tipo_sanguineo' => trim($record['tipo_sanguineo']) ?: null,
                'estado_civil' => trim($record['estado_civil']) ?: null,
                'possui_filhos' => (trim($record['possui_filhos']) ?: '0') == '1', //true ou false
                'cpf' => $this->returnCPF(trim($record['cpf'])),
                'rg' => trim($record['rg']) ?: null,
                'rg_orgao_emissor' => trim($record['rg_orgao_emissor']) ?: null,
                'whats_app' => trim($record['whats_app']) ?: null,
                'tel_01' => trim($record['tel_01']) ?: null,
                'tel_02' => trim($record['tel_02']) ?: null,
                'email' => trim($record['email']) ?: null,
                'observacoes' => trim($record['observacoes']) ?: null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];

        }
        DB::table('pessoas')->insert($dadosParaInserir);
    }

    /**
     * Converte o estado civil para o formato numérico.
     *
     * @param string|null $estadoCivil
     * @return int|null
     */
    private function parseEstadoCivil(?string $estadoCivil): ?int
    {
        // Implemente a lógica de conversão conforme necessário
        // Este é apenas um exemplo
        $mapeamento = [
            'Solteiro' => 1,
            'Casado' => 2,
            'Divorciado' => 3,
            'Viúvo' => 4,
        ];

        return $mapeamento[$estadoCivil] ?? null;
    }

    /**
     * Retorna um CPF fake caso a pessoa não tenha um cpf válido informado.
     *
     * @param string $cpf
     * @return string
     */
    private function returnCPF(string $cpf): string
    {
        if($cpf == 'CPF-FAKE')
            return gerarCPF();
        else
            return $cpf;
    }

}
