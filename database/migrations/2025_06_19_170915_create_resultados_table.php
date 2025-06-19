<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultadosTable extends Migration
{
    public function up()
    {
        Schema::create('resultados', function (Blueprint $table) {
            $table->id(); // idResultados
            $table->date('fecha');
            $table->foreignId('actividad_id')->constrained('actividades')->onDelete('cascade');
            $table->text('descripcion')->nullable();
            $table->text('logros')->nullable();
            $table->unsignedTinyInteger('porcentaje')->nullable(); // Porcentaje de avance o cumplimiento
            $table->text('comentarios')->nullable();
            $table->foreignId('plan_estrategico_id')->constrained('plan_estrategicos')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('resultados');
    }
}

