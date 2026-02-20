<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agrega el campo componente_rubrica_id para identificar la nota directa por componente
     */
    public function up(): void
    {
        Schema::table('miembro_calificacion', function (Blueprint $table) {
            // Campo para identificar a qué componente de rúbrica corresponde la nota directa
            // Solo se usa cuando el componente tiene asignación DIRECTOR_NOTA_CALIFICADORES
            $table->foreignId('componente_rubrica_id')
                ->nullable()
                ->after('item_plan_evaluacion_id')
                ->constrained('componentes_rubrica')
                ->onDelete('cascade');

            // Índice para mejorar consultas por componente
            $table->index(['tribunal_id', 'item_plan_evaluacion_id', 'componente_rubrica_id'], 'calificacion_componente_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('miembro_calificacion', function (Blueprint $table) {
            $table->dropIndex('calificacion_componente_index');
            $table->dropForeign(['componente_rubrica_id']);
            $table->dropColumn('componente_rubrica_id');
        });
    }
};
