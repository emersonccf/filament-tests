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
        Schema::create('bdv_main', function (Blueprint $table) {
            $table->id('id_bdv'); // Laravel 11 prefere 'id()' para auto-incremento primário, mas renomeamos para id_bdv
            $table->unsignedInteger('id_veiculo')->comment('Chave estrangeira para o veículo ao qual o BDV se refere');
            $table->date('data_referencia')->comment('Data principal do BDV (dia em que o veículo foi utilizado)');
            $table->text('observacoes_gerais')->nullable()->comment('Observações gerais para este BDV');


            $table->bigInteger('cadastrado_por')->unsigned()->comment('Usuário que cadastrou');
            $table->timestamps();
            $table->bigInteger('atualizado_por')->unsigned()->nullable()->comment('Usuário que fez última alteração');

            // Chaves estrangeiras
            $table->foreign('id_veiculo')->references('id_veiculo')->on('veiculos')->onDelete('restrict');
            $table->foreign('cadastrado_por')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('atualizado_por')->references('id')->on('users')->onDelete('restrict');


            // Garante um BDV único por veículo por data
            $table->unique(['id_veiculo', 'data_referencia']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bdv_main');
    }
};
