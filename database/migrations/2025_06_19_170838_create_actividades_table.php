<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadesTable extends Migration
{
    public function up()
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->id(); // idActividades
            $table->foreignId('plan_estrategico_id')->constrained('plan_estrategicos')->onDelete('cascade');
            $table->string('unidad')->nullable(); // Unidad responsable o área
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete(); // Usuario responsable
            $table->text('objetivos')->nullable();
            $table->json('fechas')->nullable(); // Fechas relevantes
            $table->text('resultados_esperados')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('actividades');
    }
}

