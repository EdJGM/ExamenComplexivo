{{-- Modal Crear/Editar Plantilla --}}
<div wire:ignore.self class="modal fade" id="plantillaModal" tabindex="-1" aria-labelledby="plantillaModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #3d8e72ff 0%, #3da66aff 100%);">
                <h5 class="modal-title text-white" id="plantillaModalLabel">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    @if(isset($modoCreacion) && $modoCreacion)
                        Nueva Plantilla de Acta
                    @else
                        Editar Plantilla de Acta
                    @endif
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" wire:click="cancel"></button>
            </div>

            <div class="modal-body">
                <form>
                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">
                                <i class="bi bi-tag-fill me-1"></i>Nombre de la Plantilla <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                   id="nombre" wire:model="nombre" placeholder="Ej: Acta 2025">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Versión -->
                        <div class="col-md-6 mb-3">
                            <label for="version" class="form-label">
                                <i class="bi bi-hash me-1"></i>Versión
                            </label>
                            <input type="text" class="form-control @error('version') is-invalid @enderror"
                                   id="version" wire:model="version" placeholder="Ej: 1.0">
                            @error('version')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">
                            <i class="bi bi-card-text me-1"></i>Descripción
                        </label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                  id="descripcion" wire:model="descripcion" rows="2"
                                  placeholder="Descripción breve de los cambios o características"></textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Fecha Vigencia Desde -->
                        <div class="col-md-6 mb-3">
                            <label for="fecha_vigencia_desde" class="form-label">
                                <i class="bi bi-calendar-check me-1"></i>Vigencia Desde
                            </label>
                            <input type="date" class="form-control @error('fecha_vigencia_desde') is-invalid @enderror"
                                   id="fecha_vigencia_desde" wire:model="fecha_vigencia_desde">
                            @error('fecha_vigencia_desde')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Dejar vacío para sin límite</small>
                        </div>

                        <!-- Fecha Vigencia Hasta -->
                        <div class="col-md-6 mb-3">
                            <label for="fecha_vigencia_hasta" class="form-label">
                                <i class="bi bi-calendar-x me-1"></i>Vigencia Hasta
                            </label>
                            <input type="date" class="form-control @error('fecha_vigencia_hasta') is-invalid @enderror"
                                   id="fecha_vigencia_hasta" wire:model="fecha_vigencia_hasta">
                            @error('fecha_vigencia_hasta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Dejar vacío para sin límite</small>
                        </div>
                    </div>

                    <!-- Contenido HTML con TinyMCE -->
                    <div class="mb-3">
                        <label for="contenido_html" class="form-label">
                            <i class="bi bi-code-slash me-1"></i>Contenido HTML <span class="text-danger">*</span>
                        </label>
                        <!-- Input hidden para sincronizar con Livewire -->
                        <input type="hidden" wire:model="contenido_html" id="contenido_html_hidden">
                        <div wire:ignore>
                            <textarea id="contenido_html_editor" class="form-control">{{ $contenido_html ?? '' }}</textarea>
                        </div>
                        @error('contenido_html')
                            <div class="text-danger mt-1"><small>{{ $message }}</small></div>
                        @enderror
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            @verbatim
                            Puedes usar variables como <code>{{estudiante_nombre}}</code>, <code>{{nota_final}}</code>, etc.
                            @endverbatim
                        </small>
                    </div>

                    <!-- Estilos CSS (Opcional) -->
                    <div class="mb-3">
                        <label for="estilos_css" class="form-label">
                            <i class="bi bi-palette me-1"></i>Estilos CSS Adicionales (Opcional)
                        </label>
                        <textarea class="form-control @error('estilos_css') is-invalid @enderror"
                                  id="estilos_css" wire:model="estilos_css" rows="4" style="font-family: monospace;"
                                  placeholder="Ej: .custom-class { color: red; }"></textarea>
                        @error('estilos_css')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Los estilos se agregarán dentro de una etiqueta &lt;style&gt;</small>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="cancel">
                    <i class="bi bi-x-circle me-1"></i>Cancelar
                </button>
                @if(isset($modoCreacion) && $modoCreacion)
                    <button type="button" class="btn btn-success" wire:click="store">
                        <i class="bi bi-save me-1"></i>Guardar Plantilla
                    </button>
                @else
                    <button type="button" class="btn btn-primary" wire:click="update">
                        <i class="bi bi-save me-1"></i>Actualizar Plantilla
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Importar desde Word --}}
<div wire:ignore.self class="modal fade" id="importWordModal" tabindex="-1" aria-labelledby="importWordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #3d8e72ff 0%, #3da66aff 100%);">
                <h5 class="modal-title text-white" id="importWordModalLabel">
                    <i class="bi bi-file-earmark-word me-2"></i>Importar desde Word
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Instrucciones:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Selecciona un archivo Word (.docx o .doc)</li>
                        <li>El archivo se convertirá automáticamente a HTML</li>
                        <li>Luego podrás editarlo en TinyMCE antes de guardar</li>
                        <li>Tamaño máximo: 10MB</li>
                    </ul>
                </div>

                <div class="mb-3">
                    <label for="archivoWord" class="form-label">
                        <i class="bi bi-file-earmark-arrow-up me-1"></i>Seleccionar archivo Word
                    </label>
                    <input type="file" class="form-control @error('archivoWord') is-invalid @enderror"
                           wire:model="archivoWord" accept=".doc,.docx">
                    @error('archivoWord')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if($archivoWord)
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        Archivo seleccionado: <strong>{{ $archivoWord->getClientOriginalName() }}</strong>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" wire:click="importarDesdeWord" @if(!$archivoWord) disabled @endif>
                    <i class="bi bi-upload me-1"></i>Importar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Confirmación Eliminar --}}
<div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    <i class="bi bi-question-circle me-2"></i>
                    ¿Estás seguro de que deseas eliminar esta plantilla?
                </p>
                <p class="text-muted mb-0 mt-2">
                    <small>Esta acción no se puede deshacer.</small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" wire:click="delete">
                    <i class="bi bi-trash me-1"></i>Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- TinyMCE desde jsDelivr CDN (Gratis, sin API key, sin límites) -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    let tinyMCEInitialized = false;

    // Función para inicializar TinyMCE
    function initTinyMCE() {
        // Destruir instancia previa si existe
        if (tinymce.get('contenido_html_editor')) {
            tinymce.get('contenido_html_editor').remove();
        }

        // Inicializar TinyMCE
        tinymce.init({
            selector: '#contenido_html_editor',
            height: 500,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic underline | ' +
                     'alignleft aligncenter alignright alignjustify | ' +
                     'bullist numlist outdent indent | table | code | fullscreen | help',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 12px; }',

            setup: function (editor) {
                editor.on('init', function () {
                    tinyMCEInitialized = true;
                });

                // Actualizar input hidden cuando cambie el contenido
                editor.on('change keyup', function () {
                    const content = editor.getContent();
                    const hiddenInput = document.getElementById('contenido_html_hidden');
                    if (hiddenInput) {
                        hiddenInput.value = content;
                        // Disparar evento input para que Livewire detecte el cambio
                        hiddenInput.dispatchEvent(new Event('input'));
                    }
                });

                // También actualizar al perder foco
                editor.on('blur', function () {
                    const content = editor.getContent();
                    const hiddenInput = document.getElementById('contenido_html_hidden');
                    if (hiddenInput) {
                        hiddenInput.value = content;
                        hiddenInput.dispatchEvent(new Event('input'));
                    }
                });
            }
        });
    }

    // Función para limpiar TinyMCE
    function cleanupTinyMCE() {
        if (tinymce.get('contenido_html_editor')) {
            tinymce.get('contenido_html_editor').remove();
        }
        tinyMCEInitialized = false;
    }

    // Evento para abrir el modal
    window.addEventListener('openPlantillaModal', event => {
        let modal = new bootstrap.Modal(document.getElementById('plantillaModal'));
        modal.show();
    });

    // Evento para abrir modal con contenido (desde importación Word)
    window.addEventListener('openPlantillaModalWithContent', event => {
        const contenido = event.detail.contenido;

        // Actualizar textarea con el contenido
        const textarea = document.getElementById('contenido_html_editor');
        if (textarea) {
            textarea.value = contenido;
        }

        // Abrir el modal
        setTimeout(() => {
            let modal = new bootstrap.Modal(document.getElementById('plantillaModal'));
            modal.show();
        }, 300);
    });

    // Cuando se muestre el modal, inicializar TinyMCE
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('plantillaModal');

        if (modalElement) {
            modalElement.addEventListener('shown.bs.modal', function () {
                setTimeout(() => {
                    initTinyMCE();
                }, 200);
            });

            modalElement.addEventListener('hidden.bs.modal', function () {
                cleanupTinyMCE();
            });
        }
    });

    // Escuchar evento de cierre del modal desde Livewire
    window.addEventListener('closeModal', event => {
        let modalElement = document.getElementById('plantillaModal');
        let modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    });

    // Cerrar modal de importación
    window.addEventListener('closeImportModal', event => {
        let modalElement = document.getElementById('importWordModal');
        let modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    });
</script>
@endpush
