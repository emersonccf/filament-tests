<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TipoTurnoEnum;
use App\Enums\NivelCombustivelEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bdv_registro_motorista', function (Blueprint $table) {
            $table->id('id_registro_motorista');
            $table->unsignedBigInteger('id_bdv')->comment('Chave estrangeira para o BDV principal');
            $table->unsignedBigInteger('id_condutor')->comment('Chave estrangeira para o condutor do veículo neste turno (Pessoa)');
            $table->string('tipo_turno', 20)->default(TipoTurnoEnum::MATUTINO->value)->comment('Turno de atuação do motorista (Matutino, Vespertino, Noturno, Diurno)');

            // Informações de Saída
            $table->dateTime('momento_saida')->comment('Data e hora da saída do veículo com este motorista');
            $table->decimal('km_saida', 10)->comment('Quilometragem do veículo na saída');
            $table->string('nivel_combustivel_saida', 10)->default(NivelCombustivelEnum::VAZIO->value)->comment('Nível de combustível na saída');
            $table->text('observacoes_saida')->nullable()->comment('Observações do motorista ou encarregado na saída');
            $table->unsignedBigInteger('id_encarregado_saida')->comment('Pessoa encarregada que conferiu a saída do veículo');

            // Informações de Chegada (podem ser nulas se o veículo ainda não retornou)
            $table->dateTime('momento_chegada')->nullable()->comment('Data e hora da chegada do veículo com este motorista');
            $table->decimal('km_chegada', 10)->nullable()->comment('Quilometragem do veículo na chegada');
            $table->string('nivel_combustivel_chegada', 10)->nullable()->comment('Nível de combustível na chegada');
            $table->text('observacoes_chegada')->nullable()->comment('Observações do motorista ou encarregado na chegada');
            $table->unsignedBigInteger('id_encarregado_chegada')->nullable()->comment('Pessoa encarregada que conferiu a chegada do veículo');

            $table->bigInteger('cadastrado_por')->unsigned()->comment('Usuário que cadastrou');
            $table->timestamps();
            $table->bigInteger('atualizado_por')->unsigned()->nullable()->comment('Usuário que fez última alteração');


            // Chaves estrangeiras
            $table->foreign('id_bdv')->references('id_bdv')->on('bdv_main')->onDelete('restrict');
            $table->foreign('id_condutor')->references('rus_id')->on('pessoas')->onDelete('restrict');
            $table->foreign('id_encarregado_saida')->references('rus_id')->on('pessoas')->onDelete('restrict');
            $table->foreign('id_encarregado_chegada')->references('rus_id')->on('pessoas')->onDelete('restrict');
            $table->foreign('cadastrado_por')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('atualizado_por')->references('id')->on('users')->onDelete('restrict');

            // Garante um registro único por BDV, condutor e turno
            $table->unique(['id_bdv', 'id_condutor', 'tipo_turno'], 'unique_bdv_condutor_turno');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bdv_registro_motorista');
    }
};
