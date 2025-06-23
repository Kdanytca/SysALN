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
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idMetas')->constrained('metas')->onDelete('cascade');
            $table->foreignId('idUsuario')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('nombre_actividad', 255);
            $table->string('objetivos', 255);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('resultados_esperados', 255);
            $table->string('unidad_encargada', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
