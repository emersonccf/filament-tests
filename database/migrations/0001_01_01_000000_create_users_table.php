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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid_id')->unique();
            $table->string('name');
            $table->string('cpf', 19)->unique();
            $table->string('email')->unique();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('change_password')->default(false);
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_active')->default(false);
            $table->boolean('belongs_sector')->default(false);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
//            $table->timestamp('created_at')->useCurrent();
//            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // Adiciona a trigger para gerar UUID automaticamente
        DB::unprepared('
            CREATE TRIGGER before_insert_users
            BEFORE INSERT ON users
            FOR EACH ROW
            BEGIN
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

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
//            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS before_insert_users');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
