<?php

namespace App\Helpers;

use App\Models\Tribunale;

class PlantillaActaHelper
{
    /**
     * Reemplaza las variables en el contenido HTML de la plantilla
     * con los datos reales del tribunal
     *
     * @param string $contenidoHtml
     * @param Tribunale $tribunal
     * @param array $datosAdicionales (resumenNotasCalculadas, notaFinal, etc.)
     * @return string
     */
    public static function reemplazarVariables(string $contenidoHtml, Tribunale $tribunal, array $datosAdicionales = []): string
    {
        // Datos del estudiante
        $estudiante = $tribunal->estudiante;
        $variables['{{estudiante_nombre}}'] = $estudiante->nombres ?? 'N/A';
        $variables['{{estudiante_apellidos}}'] = $estudiante->apellidos ?? 'N/A';
        $variables['{{estudiante_id}}'] = $estudiante->ID_estudiante ?? 'N/A';
        $variables['{{estudiante_cedula}}'] = $estudiante->cedula ?? 'N/A';
        $variables['{{estudiante_nombres_completos}}'] = ($estudiante->apellidos ?? '') . ' ' . ($estudiante->nombres ?? '');

        // Datos de la carrera
        $carrera = $tribunal->carrerasPeriodo->carrera ?? null;
        if ($carrera) {
            $nombreCarrera = $carrera->nombre ?? 'N/A';
            // Convertir a mayúsculas y eliminar las dos últimas palabras (igual que en la vista actual)
            $nombreCarreraMayus = mb_strtoupper($nombreCarrera, 'UTF-8');
            $partes = explode(' ', $nombreCarreraMayus);
            if (count($partes) > 2) {
                array_splice($partes, -2);
            }
            $nombreCarreraFinal = implode(' ', $partes);

            $variables['{{carrera_nombre}}'] = $nombreCarreraFinal;
            $variables['{{carrera_modalidad}}'] = $carrera->modalidad ?? 'N/A';
        } else {
            $variables['{{carrera_nombre}}'] = 'N/A';
            $variables['{{carrera_modalidad}}'] = 'N/A';
        }

        // Datos del período
        $periodo = $tribunal->carrerasPeriodo->periodo ?? null;
        $variables['{{periodo_codigo}}'] = $periodo->codigo_periodo ?? 'N/A';

        // Datos del tribunal
        $variables['{{fecha_examen}}'] = $tribunal->fecha ? \Carbon\Carbon::parse($tribunal->fecha)->format('d/m/Y') : 'N/A';
        $variables['{{hora_inicio}}'] = $tribunal->hora_inicio ? \Carbon\Carbon::parse($tribunal->hora_inicio)->format('H:i') : 'N/A';
        $variables['{{hora_fin}}'] = $tribunal->hora_fin ? \Carbon\Carbon::parse($tribunal->hora_fin)->format('H:i') : 'N/A';

        // Miembros del tribunal
        $presidente = $tribunal->miembrosTribunales->where('status', 'PRESIDENTE')->first();
        $integrante1 = $tribunal->miembrosTribunales->where('status', 'INTEGRANTE1')->first();
        $integrante2 = $tribunal->miembrosTribunales->where('status', 'INTEGRANTE2')->first();

        $variables['{{presidente_nombre}}'] = $presidente ? ($presidente->user->name ?? '') . ' ' . ($presidente->user->lastname ?? '') : 'N/A';
        $variables['{{presidente_cedula}}'] = $presidente ? str_pad($presidente->user->cedula ?? '', 10, '0', STR_PAD_LEFT) : 'N/A';

        $variables['{{integrante1_nombre}}'] = $integrante1 ? ($integrante1->user->name ?? '') . ' ' . ($integrante1->user->lastname ?? '') : 'N/A';
        $variables['{{integrante1_cedula}}'] = $integrante1 ? str_pad($integrante1->user->cedula ?? '', 10, '0', STR_PAD_LEFT) : 'N/A';

        $variables['{{integrante2_nombre}}'] = $integrante2 ? ($integrante2->user->name ?? '') . ' ' . ($integrante2->user->lastname ?? '') : 'N/A';
        $variables['{{integrante2_cedula}}'] = $integrante2 ? str_pad($integrante2->user->cedula ?? '', 10, '0', STR_PAD_LEFT) : 'N/A';

        // Director de carrera
        $director = $tribunal->carrerasPeriodo->director ?? null;
        $variables['{{director_nombre}}'] = $director ? ($director->name ?? '') . ' ' . ($director->lastname ?? '') : 'N/A';
        $variables['{{director_cedula}}'] = $director ? str_pad($director->cedula ?? '', 10, '0', STR_PAD_LEFT) : 'N/A';

        // Notas y calificaciones
        $notaFinal = $datosAdicionales['notaFinalCalculadaDelTribunal'] ?? 0;
        $variables['{{nota_final}}'] = number_format($notaFinal, 2);

        // Nota en letras
        $variables['{{nota_final_letras}}'] = self::numeroALetrasConPunto($notaFinal);

        // Aprobado
        $variables['{{aprobado}}'] = $notaFinal >= 14 ? 'SÍ' : 'NO';
        $variables['{{aprobado_x}}'] = $notaFinal >= 14 ? '__X__' : '_____';
        $variables['{{no_aprobado_x}}'] = $notaFinal < 14 ? '__X__' : '_____';

        // Fecha actual
        $variables['{{fecha_actual}}'] = \Carbon\Carbon::now()->format('d/m/Y');

        // Logo (base64)
        $logoPath = public_path('storage/logos/LOGO-ESPE_lg.png');
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $variables['{{logo_base64}}'] = 'data:image/png;base64,' . base64_encode($logoData);
        } else {
            $variables['{{logo_base64}}'] = '';
        }

        // Reemplazar todas las variables
        $resultado = str_replace(array_keys($variables), array_values($variables), $contenidoHtml);

        return $resultado;
    }

    /**
     * Convierte un número a letras con formato "NÚMERO LETRAS"
     * (Misma función que está en la vista acta-tribunal.blade.php)
     *
     * @param float $numero
     * @return string
     */
    private static function numeroALetrasConPunto($numero): string
    {
        $numeros = [
            0 => 'CERO', 1 => 'UNO', 2 => 'DOS', 3 => 'TRES', 4 => 'CUATRO',
            5 => 'CINCO', 6 => 'SEIS', 7 => 'SIETE', 8 => 'OCHO', 9 => 'NUEVE',
            10 => 'DIEZ', 11 => 'ONCE', 12 => 'DOCE', 13 => 'TRECE', 14 => 'CATORCE',
            15 => 'QUINCE', 16 => 'DIECISÉIS', 17 => 'DIECISIETE', 18 => 'DIECIOCHO',
            19 => 'DIECINUEVE', 20 => 'VEINTE', 21 => 'VEINTIUNO', 22 => 'VEINTIDÓS',
            23 => 'VEINTITRÉS', 24 => 'VEINTICUATRO', 25 => 'VEINTICINCO', 26 => 'VEINTISÉIS',
            27 => 'VEINTISIETE', 28 => 'VEINTIOCHO', 29 => 'VEINTINUEVE', 30 => 'TREINTA',
            31 => 'TREINTA Y UNO', 32 => 'TREINTA Y DOS', 33 => 'TREINTA Y TRES',
            34 => 'TREINTA Y CUATRO', 35 => 'TREINTA Y CINCO', 36 => 'TREINTA Y SEIS',
            37 => 'TREINTA Y SIETE', 38 => 'TREINTA Y OCHO', 39 => 'TREINTA Y NUEVE',
            40 => 'CUARENTA', 41 => 'CUARENTA Y UNO', 42 => 'CUARENTA Y DOS',
            43 => 'CUARENTA Y TRES', 44 => 'CUARENTA Y CUATRO', 45 => 'CUARENTA Y CINCO',
            46 => 'CUARENTA Y SEIS', 47 => 'CUARENTA Y SIETE', 48 => 'CUARENTA Y OCHO',
            49 => 'CUARENTA Y NUEVE', 50 => 'CINCUENTA', 51 => 'CINCUENTA Y UNO',
            52 => 'CINCUENTA Y DOS', 53 => 'CINCUENTA Y TRES', 54 => 'CINCUENTA Y CUATRO',
            55 => 'CINCUENTA Y CINCO', 56 => 'CINCUENTA Y SEIS', 57 => 'CINCUENTA Y SIETE',
            58 => 'CINCUENTA Y OCHO', 59 => 'CINCUENTA Y NUEVE', 60 => 'SESENTA',
            61 => 'SESENTA Y UNO', 62 => 'SESENTA Y DOS', 63 => 'SESENTA Y TRES',
            64 => 'SESENTA Y CUATRO', 65 => 'SESENTA Y CINCO', 66 => 'SESENTA Y SEIS',
            67 => 'SESENTA Y SIETE', 68 => 'SESENTA Y OCHO', 69 => 'SESENTA Y NUEVE',
            70 => 'SETENTA', 71 => 'SETENTA Y UNO', 72 => 'SETENTA Y DOS',
            73 => 'SETENTA Y TRES', 74 => 'SETENTA Y CUATRO', 75 => 'SETENTA Y CINCO',
            76 => 'SETENTA Y SEIS', 77 => 'SETENTA Y SIETE', 78 => 'SETENTA Y OCHO',
            79 => 'SETENTA Y NUEVE', 80 => 'OCHENTA', 81 => 'OCHENTA Y UNO',
            82 => 'OCHENTA Y DOS', 83 => 'OCHENTA Y TRES', 84 => 'OCHENTA Y CUATRO',
            85 => 'OCHENTA Y CINCO', 86 => 'OCHENTA Y SEIS', 87 => 'OCHENTA Y SIETE',
            88 => 'OCHENTA Y OCHO', 89 => 'OCHENTA Y NUEVE', 90 => 'NOVENTA',
            91 => 'NOVENTA Y UNO', 92 => 'NOVENTA Y DOS', 93 => 'NOVENTA Y TRES',
            94 => 'NOVENTA Y CUATRO', 95 => 'NOVENTA Y CINCO', 96 => 'NOVENTA Y SEIS',
            97 => 'NOVENTA Y SIETE', 98 => 'NOVENTA Y OCHO', 99 => 'NOVENTA Y NUEVE',
        ];

        $parteEntera = floor($numero);
        $parteDecimal = round(($numero - $parteEntera) * 100);

        $textoEntera = $numeros[$parteEntera] ?? 'ERROR';
        $textoDecimal = $numeros[$parteDecimal] ?? 'ERROR';

        return number_format($numero, 2) . '&nbsp;&nbsp;' . $textoEntera . ' PUNTO ' . $textoDecimal;
    }
}
