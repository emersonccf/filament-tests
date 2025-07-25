<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\CategoriaVeiculo; // Importar o Enum

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('modelos', function (Blueprint $table) {
            $table->increments('id_modelo'); // Chave primária
            $table->integer('id_marca')->unsigned()->comment('Chave estrangeira para marca');
            $table->string('nome_modelo', 50)->comment('Nome do modelo');
            // Usando string para ENUM no banco, e o Enum no Model para tipagem segura
            $table->decimal('quilometragem_revisao', 10, 2)->default(10000)->comment('Quilometragem definida para periodicidade de revisão');
            $table->string('categoria', 50)->default(CategoriaVeiculo::OUTROS->value)->comment('Categoria do veículo');
            $table->tinyInteger('numero_portas')->default(0)->comment('Número de portas');
            $table->tinyInteger('capacidade_passageiros')->default(2)->comment('Capacidade de passageiros');
            $table->tinyInteger('numero_rodas')->default(4)->comment('Número de rodas');
            $table->string('cilindrada', 10)->nullable()->comment('Cilindrada do motor');
            $table->decimal('peso_bruto', 8, 2)->nullable()->comment('Peso bruto total (kg)');
            $table->bigInteger('cadastrado_por')->unsigned()->comment('Usuário que cadastrou');
            $table->timestamps();
            $table->bigInteger('atualizado_por')->unsigned()->nullable()->comment('Usuário que fez última alteração');

            $table->foreign('id_marca')->references('id_marca')->on('marcas');
            $table->foreign('cadastrado_por')->references('id')->on('users');
            $table->foreign('atualizado_por')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modelos');
    }
};

