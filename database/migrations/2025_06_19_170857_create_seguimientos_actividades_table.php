<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeguimientosActividadesTable extends Migration
{
    public function up()
    {
        Schema::create('seguimientos_actividades', function (Blueprint $table) {
            $table->id(); // idSeguimiento
            $table->foreignId('plan_estrategico_id')->constrained('plan_estrategicos')->onDelete('cascade');
            $table->foreignId('actividad_id')->constrained('actividades')->onDelete('cascade');
            $table->string('periodo'); // Ejemplo: "2025-Q2" o "Mayo 2025"
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('seguimientos_actividades');
    }
}

