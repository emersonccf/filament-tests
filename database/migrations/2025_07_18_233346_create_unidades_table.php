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
        Schema::create('unidades', function (Blueprint $table) {
            $table->increments('id_unidade'); // Chave primária
            $table->string('nome_unidade', 100)->unique()->comment('Nome da unidade');
            $table->string('codigo_unidade', 20)->unique()->comment('Código identificador da unidade');
            $table->string('telefone', 15)->nullable()->comment('Telefone da unidade');
            $table->string('responsavel', 100)->nullable()->comment('Nome do responsável');
            $table->integer('cadastrado_por')->unsigned()->comment('Usuário que cadastrou');
            $table->timestamps();
            $table->integer('atualizado_por')->unsigned()->nullable()->comment('Usuário que fez última alteração');

            $table->foreign('cadastrado_por')->references('id')->on('users');
            $table->foreign('atualizado_por')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidades');
    }
};

