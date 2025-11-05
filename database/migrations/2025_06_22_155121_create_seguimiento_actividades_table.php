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
        Schema::create('seguimiento_actividades', function (Blueprint $table) {
            $table->id();
            $table->date('periodo_consultar');
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['pendiente', 'en progreso', 'finalizado'])->default('pendiente');
            $table->string('documento')->nullable(); // Ruta al archivo (si lo quieres)
            $table->json('evidencia')->nullable()->onDelete('cascade');
            $table->foreignId('idActividades')->constrained('actividades')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguimiento_actividades');
    }
};
