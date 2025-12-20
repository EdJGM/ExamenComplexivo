<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plantillas_acta', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100); // Nombre identificador de la plantilla (ej: "Acta 2025")
            $table->string('version', 50)->nullable(); // Versión de la plantilla (ej: "1.0", "2.0")
            $table->longText('contenido_html'); // Contenido HTML de la plantilla con variables {{variable}}
            $table->text('estilos_css')->nullable(); // Estilos CSS adicionales
            $table->boolean('activa')->default(false); // Solo una plantilla puede estar activa
            $table->date('fecha_vigencia_desde')->nullable(); // Fecha desde la cual es válida
            $table->date('fecha_vigencia_hasta')->nullable(); // Fecha hasta la cual es válida
            $table->text('descripcion')->nullable(); // Descripción de los cambios o características
            $table->unsignedBigInteger('creado_por')->nullable(); // Usuario que creó la plantilla
            $table->unsignedBigInteger('actualizado_por')->nullable(); // Usuario que actualizó la plantilla
            $table->timestamps();

            // Índices
            $table->index('activa');
            $table->index('fecha_vigencia_desde');
            $table->index('fecha_vigencia_hasta');

            // Relaciones
            $table->foreign('creado_por')->references('id')->on('users')->onDelete('set null');
            $table->foreign('actualizado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plantillas_acta');
    }
};
