<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('respuesta_usuarios', function (Blueprint $table) {
            $table->boolean('correcta')->after('respuesta_usuario')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('respuesta_usuarios', function (Blueprint $table) {
            $table->dropColumn('correcta');
        });
    }
};