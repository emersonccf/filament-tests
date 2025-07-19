<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alocacao_veiculos', function (Blueprint $table) {
            $table->increments('id_alocacao'); // Chave primária
            $table->integer('id_veiculo')->unsigned()->comment('Chave estrangeira para veículo');
            $table->integer('id_unidade')->unsigned()->comment('Chave estrangeira para unidade');
            $table->date('data_inicio')->comment('Data de início da alocação');
            $table->date('data_fim')->nullable()->comment('Data de fim da alocação');
            $table->text('observacoes')->nullable()->comment('Observações sobre a alocação');
            $table->integer('cadastrado_por')->unsigned()->comment('Usuário que cadastrou');
            $table->timestamps();
            $table->integer('atualizado_por')->unsigned()->nullable()->comment('Usuário que fez última alteração');

            $table->foreign('id_veiculo')->references('id_veiculo')->on('veiculos');
            $table->foreign('id_unidade')->references('id_unidade')->on('unidades');
            $table->foreign('cadastrado_por')->references('id')->on('users');
            $table->foreign('atualizado_por')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alocacao_veiculos');
    }
};

