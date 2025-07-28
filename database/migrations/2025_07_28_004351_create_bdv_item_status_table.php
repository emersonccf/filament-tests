<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TipoRegistroStatusEnum; // Importar o Enum

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bdv_item_status', function (Blueprint $table) {
            $table->id('id_item_status');
            $table->unsignedBigInteger('id_registro_motorista')->comment('Chave estrangeira para o registro de motorista');
            $table->string('tipo_registro', 10)->default(TipoRegistroStatusEnum::SAIDA->value)->comment('Indica se o status é da Saída ou da Chegada do veículo');

            // Itens aplicáveis a AMBOS (2 e 4 rodas):
            $table->boolean('crlv')->default(false);
            $table->boolean('lacre_placa')->default(false);
            $table->boolean('oleo_freio')->default(false);
            $table->boolean('oleo_motor')->default(false);
            $table->boolean('pneus_estado')->default(false);
            $table->boolean('retrovisor_direito_esquerdo')->default(false);
            $table->boolean('buzina')->default(false);
            $table->boolean('luzes_farol_alto_baixo_estacionamento')->default(false);
            $table->boolean('luzes_pisca_re_freios')->default(false);
            $table->boolean('chaparia_pintura')->default(false);
            $table->boolean('giroflex')->default(false);
            $table->boolean('sirene')->default(false);

            // Itens aplicáveis SOMENTE a veículos de 2 rodas:
            $table->boolean('velocimetro')->default(false);
            $table->boolean('bancos_estado')->default(false);
            $table->boolean('bateria_agua')->default(false);
            $table->boolean('paralamas_dianteiro_traseiro')->default(false);
            $table->boolean('descarga_completa')->default(false);
            $table->boolean('etiqueta_revisao')->default(false);
            $table->boolean('tampas_laterais')->default(false);
            $table->boolean('protetor_perna')->default(false);
            $table->boolean('fechadura_chave')->default(false);
            $table->boolean('carenagem_tanque')->default(false);
            $table->boolean('carenagem_farol')->default(false);
            $table->boolean('tanque_estrutura')->default(false)->comment('Refere-se à estrutura física do tanque');
            $table->boolean('caixa_lado_esq_lado_dir')->default(false);
            $table->boolean('punhos_manete')->default(false);

            // Itens aplicáveis SOMENTE a veículos de 4 rodas:
            $table->boolean('macaco')->default(false);
            $table->boolean('chave_roda')->default(false);
            $table->boolean('triangulo')->default(false);
            $table->boolean('estepe')->default(false);
            $table->boolean('extintor')->default(false);
            $table->boolean('agua_radiador')->default(false);
            $table->boolean('calotas')->default(false);
            $table->boolean('retrovisor_interno')->default(false);
            $table->boolean('macanetas_fechaduras')->default(false);
            $table->boolean('limpadores')->default(false);
            $table->boolean('luzes_internas')->default(false);
            $table->boolean('cinto_seguranca')->default(false);
            $table->boolean('radio_am_fm')->default(false);
            $table->boolean('estofamento')->default(false);

            $table->bigInteger('cadastrado_por')->unsigned()->comment('Usuário que cadastrou');
            $table->timestamps();
            $table->bigInteger('atualizado_por')->unsigned()->nullable()->comment('Usuário que fez última alteração');


            // Chave estrangeira
            $table->foreign('id_registro_motorista')->references('id_registro_motorista')->on('bdv_registro_motorista')->onDelete('restrict');
            $table->foreign('cadastrado_por')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('atualizado_por')->references('id')->on('users')->onDelete('restrict');

            // Garante um registro de Saida e um de Chegada por motorista
            $table->unique(['id_registro_motorista', 'tipo_registro']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bdv_item_status');
    }
};
