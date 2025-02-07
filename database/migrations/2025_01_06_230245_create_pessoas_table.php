<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->uuid('uuid_id')->unique();
            $table->string('matricula', 20)->unique()->nullable();
            $table->bigInteger('registro_unico')->unsigned()->unique()->nullable();
            $table->string('foto', 2048)->nullable();
            $table->string('nome', 150)->index();
            $table->boolean('ativo')->default(false);
            $table->string('sexo', 1)->nullable(); //enum
            $table->date('data_nascimento')->nullable();
            $table->string('tipo_sanguineo',3)->nullable(); //enum
            $table->unsignedSmallInteger('estado_civil')->nullable(); //enum
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

        // Inicializa o valor da sequência do RUS
        $valorInicial = DB::table('pessoas')->max('rus_id') ?? 9999;

        // Cria a nova sequência chamada de RUS e define o seu valor inicial
        DB::table('sequencias')->insert([
            'nome' => 'RUS',
            'valor' => $valorInicial,
        ]);

        // TODO: Chamar uma seed aqui para popular a tabela pessoa com os dados existentes

        // Adiciona a trigger para gerar UUID automaticamente
        DB::unprepared('
            CREATE TRIGGER before_insert_pessoas
            BEFORE INSERT ON pessoas
            FOR EACH ROW
            BEGIN
                DECLARE proximo_valor INT;
                DECLARE maior_rus INT;

                -- Encontrar o maior valor de RUS na tabela pessoas
                SELECT COALESCE(MAX(rus_id), 9999) INTO maior_rus FROM pessoas;

                -- Atualizar o valor na tabela sequencias
                UPDATE sequencias SET valor = maior_rus WHERE nome = "RUS";

                -- Gerar RUS (Registro Único) se estiver nulo ou vazio ou for 0
                IF NEW.rus_id IS NULL OR NEW.rus_id = "" OR NEW.rus_id = 0 THEN
                    SELECT valor + 1 INTO proximo_valor FROM sequencias WHERE nome = "RUS" FOR UPDATE;
                    UPDATE sequencias SET valor = proximo_valor WHERE nome = "RUS";
                    SET NEW.rus_id = proximo_valor;
                END IF;

                 -- Gerar UUID se estiver nulo ou vazio
                IF NEW.uuid_id IS NULL OR NEW.uuid_id = "" THEN
                    SET NEW.uuid_id = UUID();
                END IF;

                -- Definir created_at se estiver nulo ou vazio
                IF NEW.created_at IS NULL OR NEW.created_at = "" THEN
                    SET NEW.created_at = CURRENT_TIMESTAMP;
                END IF;

                -- Definir updated_at se estiver nulo ou vazio
                IF NEW.updated_at IS NULL OR NEW.updated_at = "" THEN
                    SET NEW.updated_at = CURRENT_TIMESTAMP;
                END IF;
            END
        ');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS before_insert_pessoas');
        Schema::dropIfExists('pessoas');
    }
};
