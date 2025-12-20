<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\PlantillaActa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlantillasActa extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    // Propiedades del componente
    public $selected_id;
    public $nombre;
    public $version;
    public $contenido_html;
    public $estilos_css;
    public $descripcion;
    public $fecha_vigencia_desde;
    public $fecha_vigencia_hasta;
    public $activa = false;

    public $keyWord = '';
    public $modoEdicion = false;
    public $modoCreacion = false;
    public $plantillaAEliminarId;

    // Variables disponibles para el acta (para mostrar al usuario)
    public $variablesDisponibles = [
        '{{estudiante_nombre}}' => 'Nombre completo del estudiante',
        '{{estudiante_apellidos}}' => 'Apellidos del estudiante',
        '{{estudiante_id}}' => 'ID del estudiante',
        '{{estudiante_cedula}}' => 'Cédula del estudiante',
        '{{carrera_nombre}}' => 'Nombre de la carrera',
        '{{carrera_modalidad}}' => 'Modalidad de la carrera',
        '{{periodo_codigo}}' => 'Código del período',
        '{{fecha_examen}}' => 'Fecha del examen',
        '{{hora_inicio}}' => 'Hora de inicio',
        '{{hora_fin}}' => 'Hora de fin',
        '{{presidente_nombre}}' => 'Nombre del presidente',
        '{{presidente_cedula}}' => 'Cédula del presidente',
        '{{integrante1_nombre}}' => 'Nombre del integrante 1',
        '{{integrante1_cedula}}' => 'Cédula del integrante 1',
        '{{integrante2_nombre}}' => 'Nombre del integrante 2',
        '{{integrante2_cedula}}' => 'Cédula del integrante 2',
        '{{director_nombre}}' => 'Nombre del director de carrera',
        '{{director_cedula}}' => 'Cédula del director de carrera',
        '{{nota_final}}' => 'Nota final del tribunal',
        '{{nota_final_letras}}' => 'Nota final en letras',
        '{{aprobado}}' => 'SI o NO (según si aprobó)',
        '{{fecha_actual}}' => 'Fecha actual (generación del PDF)',
    ];

    protected $rules = [
        'nombre' => 'required|string|max:100',
        'version' => 'nullable|string|max:50',
        'contenido_html' => 'required|string',
        'estilos_css' => 'nullable|string',
        'descripcion' => 'nullable|string',
        'fecha_vigencia_desde' => 'nullable|date',
        'fecha_vigencia_hasta' => 'nullable|date|after_or_equal:fecha_vigencia_desde',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre de la plantilla es obligatorio',
        'contenido_html.required' => 'El contenido HTML es obligatorio',
        'fecha_vigencia_hasta.after_or_equal' => 'La fecha de vigencia hasta debe ser posterior o igual a la fecha desde',
    ];

    public function mount()
    {
        $this->verificarAcceso();
    }

    protected function verificarAcceso()
    {
        $user = auth()->user();

        // Solo Super Admin puede gestionar plantillas
        if (!$user || !$user->hasRole('Super Admin')) {
            session()->flash('error', 'No tienes permisos para acceder a esta sección.');
            abort(403);
        }
    }

    public function render()
    {
        $keyWord = '%' . $this->keyWord . '%';

        $plantillas = PlantillaActa::with(['usuarioCreador', 'usuarioActualizador'])
            ->latest()
            ->where(function($query) use ($keyWord) {
                $query->where('nombre', 'LIKE', $keyWord)
                    ->orWhere('version', 'LIKE', $keyWord)
                    ->orWhere('descripcion', 'LIKE', $keyWord);
            })
            ->paginate(10);

        return view('livewire.plantillas-acta.view', [
            'plantillas' => $plantillas,
        ]);
    }

    public function create()
    {
        $this->resetInput();
        $this->modoCreacion = true;
        $this->modoEdicion = false;
        $this->dispatchBrowserEvent('openPlantillaModal');
    }

    public $archivoWord;

    public function importarDesdeWord()
    {
        $this->validate([
            'archivoWord' => 'required|file|mimes:docx,doc|max:10240', // Máximo 10MB
        ], [
            'archivoWord.required' => 'Debes seleccionar un archivo Word',
            'archivoWord.mimes' => 'El archivo debe ser Word (.docx o .doc)',
            'archivoWord.max' => 'El archivo no debe pesar más de 10MB',
        ]);

        try {
            // Guardar temporalmente el archivo
            $path = $this->archivoWord->store('temp');
            $fullPath = storage_path('app/' . $path);

            // Verificar si PHPWord está disponible
            if (!class_exists('\PhpOffice\PhpWord\IOFactory')) {
                session()->flash('warning', 'La librería PHPWord no está instalada. Por favor ejecuta: composer require phpoffice/phpword');
                return;
            }

            // Leer el documento Word
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($fullPath);

            // Convertir a HTML
            $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);

            // Guardar HTML temporalmente
            $htmlPath = storage_path('app/temp/word_converted.html');
            $htmlWriter->save($htmlPath);

            // Leer el HTML generado completo
            $htmlContent = file_get_contents($htmlPath);

            // Guardar una copia del HTML completo para debug
            file_put_contents(storage_path('app/temp/word_full.html'), $htmlContent);

            // Extraer TODO el contenido (incluyendo encabezados y pies)
            // No solo el body, sino todo el contenido del HTML
            if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $htmlContent, $matches)) {
                $bodyContent = $matches[1];
            } else {
                // Si no encuentra body, usar todo el contenido
                $bodyContent = $htmlContent;
            }

            // Intentar extraer encabezados y pies de página si existen
            $headerContent = '';
            $footerContent = '';

            // Buscar divs o secciones que puedan ser encabezados
            if (preg_match('/<div[^>]*class="[^"]*header[^"]*"[^>]*>(.*?)<\/div>/is', $htmlContent, $headerMatches)) {
                $headerContent = $headerMatches[1];
            }

            if (preg_match('/<div[^>]*class="[^"]*footer[^"]*"[^>]*>(.*?)<\/div>/is', $htmlContent, $footerMatches)) {
                $footerContent = $footerMatches[1];
            }

            // Combinar todo el contenido
            $fullContent = '';
            if ($headerContent) {
                $fullContent .= '<div class="header-section">' . $headerContent . '</div>';
            }
            $fullContent .= $bodyContent;
            if ($footerContent) {
                $fullContent .= '<div class="footer-section">' . $footerContent . '</div>';
            }

            // Limpiar HTML generado
            $bodyContent = $this->limpiarHTMLWord($fullContent ?: $bodyContent);

            // Asignar al componente
            $this->contenido_html = $bodyContent;
            $this->nombre = 'Plantilla importada desde Word';
            $this->version = '1.0';
            $this->descripcion = 'Plantilla importada desde archivo Word el ' . now()->format('d/m/Y H:i');

            // Limpiar archivos temporales
            unlink($fullPath);
            if (file_exists($htmlPath)) {
                unlink($htmlPath);
            }

            session()->flash('success', 'Archivo Word importado exitosamente. Revisa y edita el contenido antes de guardar.');

            $this->modoCreacion = true;
            $this->modoEdicion = false;

            // Cerrar modal de importación primero
            $this->dispatchBrowserEvent('closeImportModal');

            // Esperar y abrir modal de edición
            $this->dispatchBrowserEvent('openPlantillaModalWithContent', [
                'contenido' => $bodyContent
            ]);

        } catch (\Exception $e) {
            session()->flash('danger', 'Error al importar archivo Word: ' . $e->getMessage());
            \Log::error('Error importando Word: ' . $e->getMessage());
        }
    }

    private function limpiarHTMLWord($html)
    {
        // NO eliminar todo, solo limpiar estilos innecesarios de Word

        // Mantener estructura básica, solo limpiar atributos de Word
        $html = preg_replace('/\s*mso-[^:;]+:[^;]+;?/', '', $html); // Eliminar estilos mso-* de Word

        // NO eliminar todos los estilos ni clases, solo los más problemáticos
        // $html = preg_replace('/\s*style="[^"]*"/', '', $html); // Comentado para mantener estilos
        // $html = preg_replace('/\s*class="[^"]*"/', '', $html); // Comentado para mantener clases

        // Limpiar espacios excesivos pero mantener saltos de línea
        $html = preg_replace('/[ \t]+/', ' ', $html);

        return trim($html);
    }

    public function edit($id)
    {
        $plantilla = PlantillaActa::findOrFail($id);

        $this->selected_id = $id;
        $this->nombre = $plantilla->nombre;
        $this->version = $plantilla->version;
        $this->contenido_html = $plantilla->contenido_html;
        $this->estilos_css = $plantilla->estilos_css;
        $this->descripcion = $plantilla->descripcion;
        $this->fecha_vigencia_desde = $plantilla->fecha_vigencia_desde ? $plantilla->fecha_vigencia_desde->format('Y-m-d') : null;
        $this->fecha_vigencia_hasta = $plantilla->fecha_vigencia_hasta ? $plantilla->fecha_vigencia_hasta->format('Y-m-d') : null;
        $this->activa = $plantilla->activa;

        $this->modoEdicion = true;
        $this->modoCreacion = false;
        $this->dispatchBrowserEvent('openPlantillaModal');
    }

    public function store()
    {
        $this->validate();

        DB::transaction(function () {
            $plantilla = PlantillaActa::create([
                'nombre' => $this->nombre,
                'version' => $this->version,
                'contenido_html' => $this->contenido_html,
                'estilos_css' => $this->estilos_css,
                'descripcion' => $this->descripcion,
                'fecha_vigencia_desde' => $this->fecha_vigencia_desde,
                'fecha_vigencia_hasta' => $this->fecha_vigencia_hasta,
                'activa' => false, // Las plantillas nuevas empiezan desactivadas
                'creado_por' => Auth::id(),
                'actualizado_por' => Auth::id(),
            ]);

            session()->flash('success', 'Plantilla creada exitosamente.');
        });

        $this->resetInput();
        $this->modoCreacion = false;
        $this->dispatchBrowserEvent('closeModal');
        $this->dispatchBrowserEvent('showFlashMessage');
    }

    public function update()
    {
        $this->validate();

        $plantilla = PlantillaActa::findOrFail($this->selected_id);

        DB::transaction(function () use ($plantilla) {
            $plantilla->update([
                'nombre' => $this->nombre,
                'version' => $this->version,
                'contenido_html' => $this->contenido_html,
                'estilos_css' => $this->estilos_css,
                'descripcion' => $this->descripcion,
                'fecha_vigencia_desde' => $this->fecha_vigencia_desde,
                'fecha_vigencia_hasta' => $this->fecha_vigencia_hasta,
                'actualizado_por' => Auth::id(),
            ]);

            session()->flash('success', 'Plantilla actualizada exitosamente.');
        });

        $this->resetInput();
        $this->modoEdicion = false;
        $this->dispatchBrowserEvent('closeModal');
        $this->dispatchBrowserEvent('showFlashMessage');
    }

    public function activar($id)
    {
        DB::transaction(function () use ($id) {
            // Desactivar todas las plantillas
            PlantillaActa::query()->update(['activa' => false]);

            // Activar la plantilla seleccionada
            $plantilla = PlantillaActa::findOrFail($id);
            $plantilla->update([
                'activa' => true,
                'actualizado_por' => Auth::id(),
            ]);

            session()->flash('success', "Plantilla '{$plantilla->nombre}' activada exitosamente.");
        });

        $this->dispatchBrowserEvent('showFlashMessage');
    }

    public function desactivar($id)
    {
        DB::transaction(function () use ($id) {
            $plantilla = PlantillaActa::findOrFail($id);
            $plantilla->update([
                'activa' => false,
                'actualizado_por' => Auth::id(),
            ]);

            session()->flash('info', "Plantilla '{$plantilla->nombre}' desactivada. Se usará la plantilla por defecto.");
        });

        $this->dispatchBrowserEvent('showFlashMessage');
    }

    public function confirmDelete($id)
    {
        $this->plantillaAEliminarId = $id;
        $this->dispatchBrowserEvent('openDeleteModal');
    }

    public function delete()
    {
        if (!$this->plantillaAEliminarId) {
            return;
        }

        $plantilla = PlantillaActa::findOrFail($this->plantillaAEliminarId);

        if ($plantilla->activa) {
            session()->flash('warning', 'No puedes eliminar una plantilla activa. Desactívala primero.');
            $this->dispatchBrowserEvent('showFlashMessage');
            return;
        }

        $nombrePlantilla = $plantilla->nombre;
        $plantilla->delete();

        session()->flash('success', "Plantilla '{$nombrePlantilla}' eliminada exitosamente.");
        $this->plantillaAEliminarId = null;
        $this->dispatchBrowserEvent('closeDeleteModal');
        $this->dispatchBrowserEvent('showFlashMessage');
    }

    public function cancel()
    {
        $this->resetInput();
        $this->modoEdicion = false;
        $this->modoCreacion = false;
        $this->dispatchBrowserEvent('closeModal');
    }

    private function resetInput()
    {
        $this->selected_id = null;
        $this->nombre = null;
        $this->version = null;
        $this->contenido_html = null;
        $this->estilos_css = null;
        $this->descripcion = null;
        $this->fecha_vigencia_desde = null;
        $this->fecha_vigencia_hasta = null;
        $this->activa = false;
        $this->resetValidation();
    }

    public function duplicar($id)
    {
        $plantillaOriginal = PlantillaActa::findOrFail($id);

        DB::transaction(function () use ($plantillaOriginal) {
            $nuevaPlantilla = PlantillaActa::create([
                'nombre' => $plantillaOriginal->nombre . ' (Copia)',
                'version' => $plantillaOriginal->version,
                'contenido_html' => $plantillaOriginal->contenido_html,
                'estilos_css' => $plantillaOriginal->estilos_css,
                'descripcion' => 'Copia de: ' . $plantillaOriginal->descripcion,
                'fecha_vigencia_desde' => null,
                'fecha_vigencia_hasta' => null,
                'activa' => false,
                'creado_por' => Auth::id(),
                'actualizado_por' => Auth::id(),
            ]);

            session()->flash('success', "Plantilla duplicada exitosamente como '{$nuevaPlantilla->nombre}'.");
        });

        $this->dispatchBrowserEvent('showFlashMessage');
    }
}
