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
            $table->foreignId('idEncargadoActividad')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->text('nombre_actividad');
            $table->json('objetivos');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->text('comentario');
            $table->text('unidad_encargada')->nullable();
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
