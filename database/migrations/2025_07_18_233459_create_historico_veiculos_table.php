<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TipoEventoHistorico; // Importar o Enum
use App\Enums\PrioridadeHistorico; // Importar o Enum
use App\Enums\StatusEventoHistorico; // Importar o Enum

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historico_veiculos', function (Blueprint $table) {
            $table->increments('id_historico'); // Chave primária
            $table->integer('id_veiculo')->unsigned()->comment('Chave estrangeira para veículo');
            $table->string('tipo_evento', 50)->comment('Tipo de evento');
            $table->date('data_evento')->comment('Data do evento');
            $table->time('hora_evento')->nullable()->comment('Hora do evento');
            $table->decimal('quilometragem', 10, 2)->nullable()->comment('Quilometragem no momento');
            $table->string('prioridade', 20)->comment('Nível de prioridade');
            $table->boolean('afeta_disponibilidade')->default(false)->comment('Se o evento afeta disponibilidade');
            $table->string('status_evento', 20)->comment('Status do evento');
            $table->text('descricao')->comment('Descrição detalhada do evento');
            $table->string('local_ocorrencia', 200)->nullable()->comment('Local onde ocorreu o evento');
            $table->string('prestador_servico', 100)->nullable()->comment('Nome da oficina/prestador');
            $table->date('data_prevista_conclusao')->nullable()->comment('Data prevista para conclusão');
            $table->date('data_conclusao')->nullable()->comment('Data real de conclusão');
            $table->text('observacoes')->nullable()->comment('Observações adicionais');
            $table->bigInteger('cadastrado_por')->unsigned()->comment('Usuário que cadastrou');
            $table->timestamps();
            $table->bigInteger('atualizado_por')->unsigned()->nullable()->comment('Usuário que fez última alteração');

            $table->foreign('id_veiculo')->references('id_veiculo')->on('veiculos');
            $table->foreign('cadastrado_por')->references('id')->on('users');
            $table->foreign('atualizado_por')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_veiculos');
    }
};
