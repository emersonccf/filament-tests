<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\HistoricoVeiculo;
use App\Models\Veiculo;       // Importe o modelo Veiculo
use App\Models\User;         // Importe o modelo User (para cadastrado_por, atualizado_por)
use App\Enums\TipoEventoHistorico; // Importe seus Enums
use App\Enums\PrioridadeHistorico;
use App\Enums\StatusEventoHistorico;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HistoricoVeiculo>
 */
class HistoricoVeiculoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HistoricoVeiculo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // 1. Obter um ID de Veículo existente aleatoriamente
        // Certifique-se de que a tabela 'veiculos' não está vazia antes de rodar
        $veiculo = Veiculo::inRandomOrder()->first();
        if (!$veiculo) {
            // Caso não existam veículos, crie um ou ajuste sua lógica de seeder
            // Por simplicidade, vamos lançar um erro ou retornar um valor padrão para evitar falha
            // Em um seeder real, você garantiria que Veiculos fossem criados primeiro
            throw new \Exception('Não há veículos na base de dados. Por favor, popule a tabela de veículos primeiro.');
        }

        // 2. Obter um ID de Usuário Administrador
        $user = User::all()->first();
        if (!$user) {
            throw new \Exception('Não há usuários na base de dados. Por favor, popule a tabela de usuários primeiro.');
        }

        // Gera uma data de evento dentro do último ano
        $dataEvento = $this->faker->dateTimeBetween('-1 year', 'now');

        // Gera uma data prevista de conclusão (opcional, com 70% de chance de ser preenchida)
        // Se preenchida, será após a data do evento
        $dataPrevistaConclusao = $this->faker->optional(0.7)->dateTimeBetween($dataEvento, '+6 months');

        // Gera uma data real de conclusão (opcional, com 50% de chance de ser preenchida)
        // Se preenchida, será após a data do evento e antes ou igual à data prevista (se existir)
        $dataConclusao = null;
        if ($this->faker->boolean(50)) { // 50% de chance de ter uma data de conclusão
            $dataConclusao = $this->faker->dateTimeBetween($dataEvento, $dataPrevistaConclusao ?? '+1 year');
        }

        return [
            'id_veiculo' => $veiculo->id_veiculo,
            'tipo_evento' => $this->faker->randomElement(TipoEventoHistorico::cases())->value,
            'data_evento' => $dataEvento->format('Y-m-d'),
            'hora_evento' => $this->faker->optional()->time('H:i:s'), // Pode ser nulo
            'quilometragem' => $this->faker->optional(0.8)->randomFloat(2, 1000, 100000), // 80% de chance de ser preenchido, com 2 casas decimais, entre 1000 e 100000
            'prioridade' => $this->faker->randomElement(PrioridadeHistorico::cases())->value,
            'afeta_disponibilidade' => $this->faker->boolean(), // true ou false aleatoriamente
            'status_evento' => $this->faker->randomElement(StatusEventoHistorico::cases())->value,
            'descricao' => $this->faker->paragraph(3), // Parágrafo com 3 frases
            'local_ocorrencia' => $this->faker->optional(0.7)->address(), // 70% de chance de ser preenchido com um endereço
            'prestador_servico' => $this->faker->optional(0.6)->company(), // 60% de chance de ser preenchido com um nome de empresa
            'data_prevista_conclusao' => $dataPrevistaConclusao ? $dataPrevistaConclusao->format('Y-m-d') : null,
            'data_conclusao' => $dataConclusao ? $dataConclusao->format('Y-m-d') : null,
            'observacoes' => $this->faker->optional(0.5)->sentence(), // 50% de chance de ter uma observação com uma frase
            'cadastrado_por' => $user->id,
            // Opcional: atualizado_por pode ser o mesmo usuário que cadastrou ou nulo
            'atualizado_por' => $this->faker->optional(0.8)->passthrough($user->id), // 80% de chance de ser o mesmo usuário, caso contrário nulo
        ];
    }
}
