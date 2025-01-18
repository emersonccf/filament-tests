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
        Schema::create('sequencias', function (Blueprint $table) {
            $table->string('nome', 50)->primary();
            $table->integer('valor');
            $table->timestamps();
//            $table->timestamp('created_at')->useCurrent();
//            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // Inicializa o valor da sequência do RUS
        $valorInicial = DB::table('pessoas')->max('rus_id') ?? 9999;

        DB::table('sequencias')->insert([
            'nome' => 'RUS',
            'valor' => $valorInicial,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sequencias');
    }
};
