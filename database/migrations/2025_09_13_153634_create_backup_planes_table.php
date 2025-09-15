<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('backup_planes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idPlanOriginal')->nullable(); // permitimos null si se borra el plan
            $table->unsignedBigInteger('idDepartamento');
            $table->unsignedBigInteger('idUsuario');
            $table->string('nombre_plan_estrategico');
            $table->text('metas')->nullable();
            $table->text('ejes_estrategicos');
            $table->json('objetivos')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('indicador')->nullable();
            $table->unsignedBigInteger('creado_por');
            $table->string('nombre_departamento')->nullable();
            $table->string('nombre_responsable')->nullable();

            $table->timestamps();

            // Cambiamos el comportamiento al eliminar el plan original
            $table->foreign('idPlanOriginal')
                ->references('id')
                ->on('planes_estrategicos')
                ->nullOnDelete(); // deja el campo en null si se borra el plan original
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_planes');
    }
};
