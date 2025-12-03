<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @hasSection('title')
            @yield('title') |
        @endif {{ config('app.name', 'Laravel') }}
    </title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    @vite(['resources/js/app.js'])
    <!-- Choices.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="icon" href="{{ Storage::url('logos/ITIN_LOGO_SMALL.png') }}" type="image/x-icon">
    <!-- Scripts -->
    @livewireStyles
    <style>
        .contentImagePrincipalContainer { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; }
        .contentImagePrincipalContainer img { width: 100%; height: 100%; object-fit: cover; filter: brightness(0.92) contrast(1.05); }
        .fade-in { animation: fadeIn 0.6s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px);} to { opacity: 1; transform: translateY(0);} }
    </style>
</head>

<body>
    <div id="app" style="background:#ffffff;">
        <nav class="navbar sticky-top navbar-expand-md navbar-dark shadow-sm mb-4" style="height:70px; background-image: linear-gradient(rgba(0,0,0,.7), rgba(0,0,0,.7)), url('{{ Storage::url('fondos/ESPE.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container" style="height:70px;">
                <!-- Logo y Brand -->
            <div class="navbar-brand d-flex align-items-center text-white">
                    {{-- <img src="{{ Storage::url('logos/LOGO-ESPE_500.png') }}" alt="ESPE Logo"
                        style="width:150px;object-fit:contain;margin-right:12px;"> --}}
                    <div class="d-none d-md-block">
                        <div class="fw-bold text-white" style="font-size: 1.1rem;">Sistema Examen Complexivo</div>
                        <small class="text-light opacity-75">Universidad de las Fuerzas Armadas ESPE</small>
                    </div>
                </div>

                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <i class="bi bi-list text-white fs-4"></i>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link text-white d-flex align-items-center" href="{{ route('login') }}">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>
                                        {{ __('Iniciar Sesión') }}
                                    </a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link text-white d-flex align-items-center" href="{{ route('register') }}">
                                        <i class="bi bi-person-plus me-2"></i>
                                        {{ __('Registrarse') }}
                                    </a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                          <a id="navbarDropdown" class="nav-link dropdown-toggle text-white d-flex align-items-center"
                                   href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="bi bi-person-circle me-2 fs-5"></i>
                                    <span class="fw-semibold">{{ Auth::user()->name }}</span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="navbarDropdown">
                                    <div class="dropdown-header px-3 py-2">
                                        <small class="text-muted">Sesión activa</small>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-2 text-danger"></i>
                                        {{ __('Cerrar Sesión') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <div class="contentImagePrincipalContainer">
            <img class="" src="{{ Storage::url('fondos/002-Cotopaxi.jpg') }}" alt="Fondo Ecuador">
        </div>
        <main class="p-0 m-0 fade-in">
            {{-- Contenido Principal --}}
            <div class="container py-4">
                @yield('content')
            </div>
        </main>
    </div>
    @livewireScripts
    <!-- Choices.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>
    <script type="module">
        const addModal = new bootstrap.Modal('#createDataModal');
        const editModal = new bootstrap.Modal('#updateDataModal');
        window.addEventListener('closeModal', () => {
            addModal.hide();
            editModal.hide();
        })
    </script>
</body>

</html>
