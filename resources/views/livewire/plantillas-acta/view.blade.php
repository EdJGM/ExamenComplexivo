@section('title', __('Plantillas de Acta'))
<div class="container-fluid p-0">
    <!-- Banner Verde ESPE -->
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #3d8e72ff 0%, #3da66aff 100%);">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    @if (file_exists(public_path('storage/logos/LOGO-ESPE_500.png')))
                        <img src="{{ asset('storage/logos/LOGO-ESPE_500.png') }}" alt="Logo ESPE"
                             style="width: 60px; height: 60px; object-fit: contain;" class="me-3">
                    @else
                        <div class="bg-white bg-opacity-25 rounded p-2 me-3">
                            <i class="bi bi-file-earmark-text fs-3 text-white"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="h3 mb-1 fw-bold text-white" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                            PLANTILLAS DE ACTA DE TRIBUNAL
                        </h1>
                        <p class="mb-0 text-white" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">
                            Configuración y gestión de plantillas personalizadas
                        </p>
                    </div>
                </div>
                <div class="btn-group">
                    <button wire:click="create" class="btn btn-lg text-white"
                            style="border: 2px solid white; background: transparent; transition: all 0.3s ease;"
                            onmouseover="this.style.background='rgba(255,255,255,0.2)'"
                            onmouseout="this.style.background='transparent'">
                        <i class="bi bi-plus-circle me-2"></i>Nueva Plantilla
                    </button>
                    <button data-bs-toggle="modal" data-bs-target="#importWordModal" class="btn btn-lg text-white"
                            style="border: 2px solid white; background: rgba(255,255,255,0.1); transition: all 0.3s ease;"
                            onmouseover="this.style.background='rgba(255,255,255,0.3)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.1)'"
                            title="Importar plantilla desde archivo Word (.docx)">
                        <i class="bi bi-file-earmark-word me-2"></i>Importar desde Word
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('partials.alerts')

    <!-- Card Principal -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <!-- Header con Buscador -->
                <div class="card-header border-0 py-3" style="background-color: #f8f9fa;">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0 fw-bold" style="color: #2d7a5f;">
                                <i class="bi bi-list-ul me-2"></i>Listado de Plantillas
                            </h5>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input wire:model.live="keyWord" type="text"
                                       class="form-control border-start-0"
                                       placeholder="Buscar por nombre, versión..."
                                       style="box-shadow: none;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="card-body p-0">
                    @include('livewire.plantillas-acta.modals')

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>
                                        <i class="bi bi-file-text me-1"></i>Nombre
                                    </th>
                                    <th style="width: 120px;">
                                        <i class="bi bi-tag me-1"></i>Versión
                                    </th>
                                    <th style="width: 150px;" class="text-center">
                                        <i class="bi bi-toggle-on me-1"></i>Estado
                                    </th>
                                    <th style="width: 150px;">
                                        <i class="bi bi-calendar-check me-1"></i>Vigencia
                                    </th>
                                    <th style="width: 250px;" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plantillas as $plantilla)
                                    <tr style="border-bottom: 1px solid #f0f0f0;">
                                        <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $plantilla->nombre }}</strong>
                                            @if($plantilla->descripcion)
                                                <br><small class="text-muted">{{ Str::limit($plantilla->descripcion, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($plantilla->version)
                                                <span class="badge bg-secondary">v{{ $plantilla->version }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($plantilla->activa)
                                                <span class="badge bg-success px-3 py-2">
                                                    <i class="bi bi-check-circle me-1"></i>ACTIVA
                                                </span>
                                            @else
                                                <span class="badge bg-secondary px-3 py-2">
                                                    <i class="bi bi-x-circle me-1"></i>Inactiva
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($plantilla->fecha_vigencia_desde || $plantilla->fecha_vigencia_hasta)
                                                <small>
                                                    {{ $plantilla->fecha_vigencia_desde ? $plantilla->fecha_vigencia_desde->format('d/m/Y') : '?' }}
                                                    <br>
                                                    al {{ $plantilla->fecha_vigencia_hasta ? $plantilla->fecha_vigencia_hasta->format('d/m/Y') : '?' }}
                                                </small>
                                            @else
                                                <small class="text-muted">Sin límite</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                @if(!$plantilla->activa)
                                                    <button wire:click="activar({{ $plantilla->id }})"
                                                            class="btn btn-sm btn-success"
                                                            title="Activar plantilla">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                @else
                                                    <button wire:click="desactivar({{ $plantilla->id }})"
                                                            class="btn btn-sm btn-warning"
                                                            title="Desactivar plantilla">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                @endif

                                                <button wire:click="edit({{ $plantilla->id }})"
                                                        class="btn btn-sm btn-primary"
                                                        title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <button wire:click="duplicar({{ $plantilla->id }})"
                                                        class="btn btn-sm btn-info text-white"
                                                        title="Duplicar">
                                                    <i class="bi bi-files"></i>
                                                </button>

                                                @if(!$plantilla->activa)
                                                    <button wire:click="confirmDelete({{ $plantilla->id }})"
                                                            class="btn btn-sm btn-danger"
                                                            title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            No hay plantillas registradas. Crea tu primera plantilla.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Paginación -->
                @if($plantillas->hasPages())
                    <div class="card-footer bg-white border-top-0">
                        {{ $plantillas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Info de Variables Disponibles -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header" style="background: linear-gradient(135deg, #3d8e72ff 0%, #3da66aff 100%);">
                    <h6 class="mb-0 text-white">
                        <i class="bi bi-info-circle me-2"></i>Variables Disponibles para las Plantillas
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Puedes usar las siguientes variables en tus plantillas. Serán reemplazadas automáticamente al generar el PDF:
                    </p>
                    <div class="row">
                        @foreach($variablesDisponibles as $variable => $descripcion)
                            <div class="col-md-6 col-lg-4 mb-2">
                                <code class="text-primary">{{ $variable }}</code>
                                <small class="text-muted d-block">{{ $descripcion }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.addEventListener('showFlashMessage', event => {
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            let alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });

    window.addEventListener('closeModal', event => {
        let modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            let bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.hide();
            }
        });
    });

    window.addEventListener('openDeleteModal', event => {
        let modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    });

    window.addEventListener('closeDeleteModal', event => {
        let modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        if (modal) {
            modal.hide();
        }
    });
</script>
@endpush
