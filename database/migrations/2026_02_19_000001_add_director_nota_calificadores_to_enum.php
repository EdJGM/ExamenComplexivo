<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agrega la opción DIRECTOR_NOTA_CALIFICADORES al enum calificado_por
     */
    public function up(): void
    {
        // Modificar el enum para agregar la nueva opción
        DB::statement("ALTER TABLE asignacion_calificador_comp_plans MODIFY COLUMN calificado_por ENUM(
            'MIEMBROS_TRIBUNAL',
            'CALIFICADORES_GENERALES',
            'DIRECTOR_CARRERA',
            'DOCENTE_APOYO',
            'DIRECTOR_NOTA_CALIFICADORES'
        ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Primero actualizar los registros que tengan el valor nuevo
        DB::table('asignacion_calificador_comp_plans')
            ->where('calificado_por', 'DIRECTOR_NOTA_CALIFICADORES')
            ->update(['calificado_por' => 'DIRECTOR_CARRERA']);

        // Revertir el enum a su estado original
        DB::statement("ALTER TABLE asignacion_calificador_comp_plans MODIFY COLUMN calificado_por ENUM(
            'MIEMBROS_TRIBUNAL',
            'CALIFICADORES_GENERALES',
            'DIRECTOR_CARRERA',
            'DOCENTE_APOYO'
        ) NOT NULL");
    }
};
