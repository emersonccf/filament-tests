<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use League\Csv\Reader;
use Carbon\Carbon;

class PessoasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Caminho para o arquivo CSV
        $csvPath = storage_path('app/dados/dados-syscad-sefit.csv');

        // Criar um leitor CSV
        $csv = Reader::createFromPath($csvPath, 'r');
        $csv->setDelimiter(';'); // Define o delimitador como ponto e vírgula
        $csv->setHeaderOffset(0); // A primeira linha contém os cabeçalhos

        // Iterar sobre as linhas do CSV
        foreach ($csv as $record) {
            $this->insertPessoa($record);
        }
    }

    /**
     * Insere um registro de pessoa no banco de dados.
     *
     * @param array $record
     */
    private function insertPessoa(array $record): void
    {
        DB::table('pessoas')->insert([
            'rus_id' => $record['rus_id'],
            'uuid_id' => !empty(trim($record['uuid_id'])) ? $record['uuid_id'] : Str::uuid(),
            'matricula' => trim($record['matricula']) ?: null,
            'registro_unico' => trim($record['registro_unico']) ?: null,
            'foto' => trim($record['foto']) ?: null,
            'nome' => trim($record['nome']),
            'ativo' => !encontraPalavras(['APOSENTADO','FALECIDO','EXONERAÇÃO','EXT'], trim($record['observacoes'])),
            'sexo' => trim($record['sexo']) ?: null,
            'data_nascimento' => $this->parseDate(trim($record['data_nascimento'])),
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
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Converte a string de data para o formato correto.
     *
     * @param string|null $date
     * @return string|null
     */
    private function parseDate(?string $date): ?string
    {
        if (!$date) return null;
        try {
            return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
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
