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
        Schema::create('marcas', function (Blueprint $table) {
            $table->increments('id_marca'); // Chave primária
            $table->string('nome_marca', 50)->unique()->comment('Nome da marca');
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
        Schema::dropIfExists('marcas');
    }
};
