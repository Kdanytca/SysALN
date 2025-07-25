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
        Schema::create('planes_estrategicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('idDepartamento')->constrained('departamentos')->onDelete('cascade');

            // Responsable del plan (usuario del departamento)
            $table->foreignId('idUsuario')->constrained('usuarios')->onDelete('cascade');

            $table->string('nombre_plan_estrategico', 255);
            $table->string('ejes_estrategicos', 255);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('indicador', 45);

            // Usuario que creó el plan (admin Breeze)
            $table->foreignId('creado_por')->constrained('usuarios')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planes_estrategicos');
    }
};

