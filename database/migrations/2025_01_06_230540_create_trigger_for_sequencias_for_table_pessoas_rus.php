<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('
            CREATE TRIGGER antes_inserir_tabela_pessoas
            BEFORE INSERT ON pessoas
            FOR EACH ROW
            BEGIN
                DECLARE proximo_valor INT;

                SELECT valor + 1 INTO proximo_valor FROM sequencias WHERE nome = "RUS" FOR UPDATE;

                UPDATE sequencias SET valor = proximo_valor WHERE nome = "RUS";

                SET NEW.rus_id = proximo_valor;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS antes_inserir_tabela_pessoas');
    }
};
