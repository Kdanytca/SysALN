<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planes_estrategicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('idDepartamento')->constrained('departamentos')->onDelete('cascade');
            $table->foreignId('idUsuario')->constrained('usuarios')->onDelete('cascade');

            $table->string('nombre_plan_estrategico', 255);

            // CORREGIDO: Ahora es JSON para soportar texto largo sin problemas
            $table->text('ejes_estrategicos');

            $table->text('objetivos')->nullable();

            $table->date('fecha_inicio');
            $table->date('fecha_fin');

            // CORREGIDO: Hacer nullable ya que en tu store siempre se guarda vacío
            $table->string('indicador', 45)->nullable();

            $table->foreignId('creado_por')->constrained('usuarios')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planes_estrategicos');
    }
};
