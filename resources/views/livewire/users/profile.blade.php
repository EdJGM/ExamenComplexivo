<div class="card">
    @include('partials.alerts')
    <div class="card-body">
        <div class="card">
            <div class="card-header bg-info">
                @if (Auth::id() === $user->id)
                    <h1>Mi Perfil</h1>
                @else
                    <h1>Perfil de {{ $name }}</h1>
                @endif
            </div>
            <div class="card-body">
                <div class="col mx-auto">
                    <form>
                        <div class="col mx-auto">
                            <div class="card-body p-0">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        wire:model="name" value="{{ $name }}" placeholder="{{ $name }}">
                                    @error('name')
                                        <span class="error invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        wire:model="email" value="{{ $email }}"
                                        placeholder="{{ $email }}">
                                    @error('email')
                                        <span class="error invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>

                                @can('gestionar usuarios')
                                    <div class="mb-3">
                                        <label for="departamento_id" class="form-label">Departamento (Opcional)</label>
                                        <select wire:model="departamento_id" class="form-select @error('departamento_id') is-invalid @enderror" id="departamento_id">
                                            <option value="">Sin departamento asignado</option>
                                            @foreach($departamentosDisponibles as $depto)
                                                <option value="{{ $depto->id }}">{{ $depto->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Asignar departamento solo para docentes</small>
                                        @error('departamento_id')
                                            <span class="error invalid-feedback" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endcan

                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        wire:model="password" placeholder="***************">
                                    @error('password')
                                        <span class="error invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password-confirm" class="form-label">Confirmar Contraseña</label>
                                    <input id="password-confirm" type="password" class="form-control"
                                        wire:model="password_confirmation" placeholder="***************">
                                </div>
                                <p class="card-text"><small class="text-muted">Modificado por última vez:
                                        {{ $user->updated_at }}</small></p>
                            </div>
                            <button type="button" wire:click.prevent="update()"
                                class="btn btn-info float-end">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @can('gestionar roles y permisos')
            <div class="card mx-auto mt-3 p-3">
                <h3>Roles</h3>
                <form action="{{ route('users.updateRoles', encrypt($user->id)) }}" id="update_product_info"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <ul class="list-group text-start">
                        @php
                            $superAdminRole = $roles->where('name', 'Super Admin')->first();
                            $directorApoyo = $roles->whereIn('name', ['Director de Carrera', 'Docente de Apoyo']);
                            $docenteRole = $roles->where('name', 'Docente')->first();
                            $userHasDirectorOrApoyo = $user->hasRole('Director de Carrera') || $user->hasRole('Docente de Apoyo');
                        @endphp

                        {{-- 1. Super Admin (primero) --}}
                        @if($superAdminRole)
                            <li class="list-group-item list-group-item-action list-group-item-info">
                                <input @if ($user->hasrole('Super Admin')) checked @endif name="Super Admin"
                                    value="Super Admin" class="form-check-input me-1" type="checkbox"
                                    id="rol{{ $superAdminRole->id }}">

                                <label class="form-check-label stretched-link" for="rol{{ $superAdminRole->id }}">
                                    Super Admin
                                </label>
                            </li>
                        @endif

                        {{-- 2. Director de Carrera / Docente de Apoyo (segundo) --}}
                        @if($directorApoyo->count() > 0)
                            <li class="list-group-item list-group-item-action list-group-item-info">
                                @foreach($directorApoyo as $rol)
                                    <input @if ($user->hasrole($rol->name)) checked @endif name="{{ $rol->name }}"
                                        value="{{ $rol->name }}" class="form-check-input me-1" type="checkbox"
                                        id="rol{{ $rol->id }}" style="display: none;">
                                @endforeach

                                <input @if ($userHasDirectorOrApoyo) checked @endif
                                    name="Director de Carrera" value="Director de Carrera"
                                    class="form-check-input me-1" type="checkbox" id="rolDirectorApoyo"
                                    onchange="document.querySelectorAll('[name=\'Director de Carrera\'], [name=\'Docente de Apoyo\']').forEach(cb => cb.checked = this.checked)">

                                <label class="form-check-label stretched-link" for="rolDirectorApoyo">
                                    Director de Carrera / Docente de Apoyo
                                </label>
                            </li>
                        @endif

                        {{-- 3. Docente (tercero) --}}
                        @if($docenteRole)
                            <li class="list-group-item list-group-item-action list-group-item-info">
                                <input @if ($user->hasrole('Docente')) checked @endif name="Docente"
                                    value="Docente" class="form-check-input me-1" type="checkbox"
                                    id="rol{{ $docenteRole->id }}">

                                <label class="form-check-label stretched-link" for="rol{{ $docenteRole->id }}">
                                    Docente
                                </label>
                            </li>
                        @endif
                    </ul>
                    <button type="submit" class="btn btn-info mt-3 float-end">Guardar</button>
                </form>
            </div>
        @endcan
        {{-- Los usuarios normales NO ven la sección de roles en su propio perfil --}}


    </div>
</div>
