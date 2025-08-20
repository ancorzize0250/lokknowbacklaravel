<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pregunta');
            $table->char('opcion', 1); // 'a', 'b', 'c', 'd'
            $table->string('descripcion_opcion');
            $table->timestamps();

            $table->foreign('id_pregunta')->references('id')->on('preguntas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opciones');
    }
};