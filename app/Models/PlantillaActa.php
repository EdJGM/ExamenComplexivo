<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaActa extends Model
{
    use HasFactory;

    protected $table = 'plantillas_acta';

    protected $fillable = [
        'nombre',
        'version',
        'contenido_html',
        'estilos_css',
        'activa',
        'fecha_vigencia_desde',
        'fecha_vigencia_hasta',
        'descripcion',
        'creado_por',
        'actualizado_por',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'fecha_vigencia_desde' => 'date',
        'fecha_vigencia_hasta' => 'date',
    ];

    /**
     * Relación con el usuario que creó la plantilla
     */
    public function usuarioCreador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /**
     * Relación con el usuario que actualizó la plantilla
     */
    public function usuarioActualizador()
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    /**
     * Scope para obtener solo plantillas activas
     */
    public function scopeActiva($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Scope para obtener plantillas vigentes según fecha
     */
    public function scopeVigente($query, $fecha = null)
    {
        $fecha = $fecha ?? now();

        return $query->where(function ($q) use ($fecha) {
            $q->where(function ($q2) use ($fecha) {
                $q2->whereDate('fecha_vigencia_desde', '<=', $fecha)
                   ->whereDate('fecha_vigencia_hasta', '>=', $fecha);
            })->orWhere(function ($q2) {
                $q2->whereNull('fecha_vigencia_desde')
                   ->whereNull('fecha_vigencia_hasta');
            });
        });
    }

    /**
     * Obtener la plantilla activa actual
     */
    public static function obtenerPlantillaActiva()
    {
        return self::where('activa', true)->first();
    }

    /**
     * Activar esta plantilla y desactivar las demás
     */
    public function activar()
    {
        // Desactivar todas las demás plantillas
        self::where('id', '!=', $this->id)->update(['activa' => false]);

        // Activar esta plantilla
        $this->update(['activa' => true]);
    }
}
