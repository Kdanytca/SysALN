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
        Schema::create('metas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idPlanEstrategico')->constrained('planes_estrategicos')->onDelete('cascade');
            $table->foreignId('idEncargadoMeta')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('nombre_meta', 255);
            $table->json('ejes_estrategicos')->nullable();
            $table->string('nombre_actividades', 255)->nullable();
            $table->text('resultados_esperados');
            $table->text('indicador_resultados')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->text('comentario');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metas');
    }
};
