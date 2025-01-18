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
        Schema::create('pessoas', function (Blueprint $table) {
            $table->bigInteger('rus_id')->unsigned()->primary();
            $table->string('matricula', 20)->unique()->nullable();
            $table->bigInteger('registro_unico')->unsigned()->unique()->nullable();
            $table->string('foto', 255)->nullable();
            $table->string('nome', 150)->index();
            $table->string('sexo', 1)->nullable(); //enum
            $table->date('data_nascimento')->nullable();
            $table->string('tipo_sanguineo',3)->nullable(); //enum
            $table->integer('estado_civil')->nullable(); //enum
            $table->boolean('possui_filhos')->default(false);
            $table->string('cpf', 15)->unique();
            $table->string('rg', 20)->unique()->nullable();
            $table->string('rg_orgao_emissor',20)->nullable();
            $table->string('whats_app',15)->unique()->nullable();
            $table->string('tel_01',15)->nullable();
            $table->string('tel_02',15)->nullable();
            $table->string('email',150)->unique()->nullable();
            //$table->foreign('id_endereco')->references('id')->on('enderecos')->cascadeOnDelete()->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
//            $table->timestamp('created_at')->useCurrent();
//            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoas');
    }
};
