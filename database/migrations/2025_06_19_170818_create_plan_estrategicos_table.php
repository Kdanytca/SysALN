<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanEstrategicosTable extends Migration
{
    public function up()
    {
        Schema::create('plan_estrategicos', function (Blueprint $table) {
            $table->id(); // idPlanEstrategico
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('institucion_id')->constrained('instituciones')->onDelete('cascade');
            $table->string('nombre');
            $table->text('metas')->nullable();
            $table->json('fechas')->nullable(); // Puedes usar json para fechas variadas
            $table->json('indicadores')->nullable();
            $table->json('responsables')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('plan_estrategicos');
    }
}

