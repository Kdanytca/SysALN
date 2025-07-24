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
        Schema::table('instituciones', function (Blueprint $table) {
            $table->foreign('idEncargadoInstitucion')->references('id')->on('usuarios')->onDelete('set null');
        });

        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreign('idInstitucion')->references('id')->on('instituciones')->onDelete('set null');
            $table->foreign('idDepartamento')->references('id')->on('departamentos')->onDelete('set null');
        });

        Schema::table('departamentos', function (Blueprint $table) {
            $table->foreign('idInstitucion')->references('id')->on('instituciones')->onDelete('cascade');
            $table->foreign('idEncargadoDepartamento')->references('id')->on('usuarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instituciones', function (Blueprint $table) {
            $table->dropForeign(['idEncargadoInstitucion']);
        });

        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['idInstitucion']);
            $table->dropForeign(['idDepartamento']);
        });

        Schema::table('departamentos', function (Blueprint $table) {
            $table->dropForeign(['idInstitucion']);
            $table->dropForeign(['idEncargadoDepartamento']);
        });
    }
};
