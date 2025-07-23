<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\DirecionamentoVeiculo; // Importar o Enum
use App\Enums\LocalAtivacaoVeiculo; // Importar o Enum
use App\Enums\CombustivelVeiculo; // Importar o Enum
use App\Enums\StatusVeiculo; // Importar o Enum

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('veiculos', function (Blueprint $table) {
            $table->increments('id_veiculo'); // Chave primária
            $table->string('placa', 8)->unique()->comment('Placa do veículo (NULL para bicicletas)');
            $table->integer('id_modelo')->unsigned()->comment('Chave estrangeira para modelo');
            $table->string('prefixo_veiculo', 10)->index()->comment('Prefixo da viatura para ativação');
            $table->string('direcionamento', 20)->default(DirecionamentoVeiculo::NORMAL->value)->comment('Destinação do veículo em operações');
            $table->string('local_ativacao', 50)->default(LocalAtivacaoVeiculo::GTRAN->value)->comment('Localidade onde o veículo é ativado e/ou desativado');
            $table->string('combustivel', 20)->default(CombustivelVeiculo::FLEX->value)->comment('Tipo de combustível');
            $table->string('status', 20)->default(StatusVeiculo::ATIVO->value)->comment('Status operacional');
            $table->boolean('possui_bateria_auxiliar')->default(false)->comment('Informa de o veículo possui bateria auxiliar');
            $table->boolean('possui_gps')->default(false)->comment('Informa de o veículo possui GPS');
            $table->decimal('quilometragem', 10, 2)->default(0)->comment('Quilometragem atual');
            $table->date('data_recebimento')->nullable()->comment('Data de recebimento do veículo');
            $table->date('data_devolucao')->nullable()->comment('Data de recebimento do veículo, fim de contrato');
            $table->string('chassi', 17)->unique()->nullable()->comment('Número do chassi (VIN) (NULL para bicicletas)');
            $table->string('renavam', 11)->unique()->nullable()->comment('RENAVAM (NULL para bicicletas)');
            $table->year('ano_fabricacao')->nullable()->comment('Ano de fabricação');
            $table->year('ano_modelo')->nullable()->comment('Ano do modelo');
            $table->string('cor', 30)->nullable()->comment('Cor do veículo');
            $table->decimal('valor_diaria', 12, 2)->nullable()->comment('Valor da diária do veículo');
            $table->bigInteger('cadastrado_por')->unsigned()->comment('Usuário que cadastrou');
            $table->timestamps(); // Substitui data_cadastro e data_atualizacao
            $table->bigInteger('atualizado_por')->unsigned()->nullable()->comment('Usuário que fez última alteração');

            $table->foreign('id_modelo')->references('id_modelo')->on('modelos');
            $table->foreign('cadastrado_por')->references('id')->on('users');
            $table->foreign('atualizado_por')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
};

