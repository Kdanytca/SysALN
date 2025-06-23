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
        Schema::create('resultados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idPlanEstrategico')->constrained('planes_estrategicos')->onDelete('cascade');
            $table->integer('fecha_consulta');
            $table->string('descripcion', 255);
            $table->string('porcentaje_seguimiento', 255);
            $table->string('indicador', 255);
            $table->string('comentarios', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultados');
    }
};
