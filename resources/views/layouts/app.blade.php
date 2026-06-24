<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Conexión Electrónica')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .navbar-azul-oscuro {
            background-color: #050d2a !important;
        }
        .search-results {
            max-height: 400px;
            overflow-y: auto;
        }
        .search-results::-webkit-scrollbar {
            width: 6px;
        }
        .search-results::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 8px;
        }
        .search-results::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 8px;
        }
        .bg-app {
            background: linear-gradient(135deg, #7dd3fc 0%, #f9a8d4 50%, #7dd3fc 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="bg-app text-gray-800">

    {{-- ===== NAVBAR PRINCIPAL ===== --}}
    <nav class="navbar-azul-oscuro shadow-md sticky top-0 z-50 border-b-4 border-pink-500">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6">
            <div class="flex justify-between items-center h-24">

                {{-- Logo, menú de categorías, Inicio y Ofertas --}}
                <div class="flex items-center shrink-0 -ml-3">
                    {{-- Menú de categorías (hamburguesa) --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center text-white hover:text-pink-300 focus:outline-none px-3 py-2 rounded-md bg-sky-600 hover:bg-sky-700 transition">
                            <i class="fas fa-bars text-xl"></i>
                            <span class="text-base sm:text-lg font-medium hidden sm:inline ml-2 text-white">Categorías</span>
                        </button>

                        <div x-show="open" x-transition:enter.duration.300ms x-cloak class="absolute left-0 mt-2 w-72 bg-sky-100 rounded-md shadow-lg py-2 z-50 border border-sky-200">
                            @php
                                $categorias = [
                                    'Semiconductores',
                                    'Componentes pasivos',
                                    'Sensores',
                                    'Arduino y desarrollo',
                                    'Alimentación y energía',
                                    'Conectores y cables',
                                    'Motores y movimiento',
                                    'Pantallas e indicadores',
                                    'Herramientas y soldadura'
                                ];
                            @endphp
                            @foreach($categorias as $cat)
                                <a href="{{ route('tienda', ['categoria' => $cat]) }}" class="block px-4 py-2.5 text-lg font-medium text-gray-700 hover:bg-sky-200 hover:text-sky-800 transition">
                                    <i class="fas fa-tag mr-2"></i> {{ $cat }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Inicio --}}
                    <a href="{{ route('inicio') }}" class="ml-3 flex items-center text-white hover:text-pink-300 text-sm font-medium transition">
                        <i class="fas fa-home text-lg mr-1"></i>
                        <span class="hidden sm:inline">Inicio</span>
                    </a>

                    {{-- Logo --}}
                    <a href="{{ route('tienda') }}" class="flex items-center shrink-0 ml-4">
                        <img src="{{ asset('imagenes/ConexionElectronica.jpeg') }}" alt="Conexión Electrónica" class="h-20 w-auto">
                        <span class="ml-2 text-lg sm:text-xl font-bold text-white hidden sm:block">Conexión Electrónica</span>
                    </a>

                    {{-- Ofertas --}}
                    <a href="{{ route('ofertas') }}" class="ml-4 text-white hover:text-pink-300 text-sm font-medium transition">
                        <i class="fas fa-tag mr-1"></i> Ofertas
                    </a>
                </div>

                {{-- Barra de búsqueda con autocompletado --}}
                <div class="flex-1 max-w-xl mx-2 sm:mx-4 relative" x-data="searchProducts()" x-init="init()">
                    <form action="{{ route('tienda') }}" method="GET" class="relative" @submit.prevent="submitSearch()">
                        <input type="text" 
                               name="buscar" 
                               placeholder="Buscar productos..." 
                               x-model="query"
                               @input.debounce.300ms="search()"
                               @keydown.escape="clearSearch()"
                               @click.away="closeResults()"
                               autocomplete="off"
                               class="w-full px-4 py-2 border border-white/30 bg-white/20 text-white placeholder-white/70 rounded-full focus:outline-none focus:ring-2 focus:ring-pink-400/50 text-sm">
                        <button type="submit" class="absolute right-3 top-2 text-white hover:text-pink-300">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>

                    <div x-show="results.length > 0 && query.length > 1" 
                         x-transition:enter.duration.300ms 
                         class="absolute left-0 right-0 mt-2 bg-white rounded-lg shadow-xl z-50 max-h-72 overflow-y-auto border border-gray-200 search-results">
                        <template x-for="producto in results" :key="producto.id">
                            <a :href="'/producto/' + producto.id" 
                               class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 transition border-b border-gray-100 last:border-0">
                                <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center overflow-hidden flex-shrink-0">
                                    <img x-show="producto.imagen" :src="'/storage/' + producto.imagen" :alt="producto.nombre" class="w-full h-full object-cover">
                                    <svg x-show="!producto.imagen" class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate" x-text="producto.nombre"></p>
                                    <p class="text-xs text-gray-500 truncate" x-text="producto.categoria || 'Sin categoría'"></p>
                                </div>
                                <span class="text-sm font-bold text-blue-600" x-text="'Bs. ' + parseFloat(producto.precio).toFixed(2)"></span>
                            </a>
                        </template>
                    </div>
                </div>

                {{-- Iconos de usuario y carrito --}}
                <div class="flex items-center space-x-2 sm:space-x-6">
                    @auth
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.away="open = false" class="flex items-center text-white hover:text-pink-300 focus:outline-none">
                                <i class="fas fa-user-circle text-2xl"></i>
                                <span class="ml-1 text-sm hidden sm:inline text-white">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs ml-1"></i>
                            </button>

                            <div x-show="open" x-transition:enter.duration.300ms x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('perfil.informacion') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i> Mi información
                                </a>
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.productos.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog mr-2"></i> Administración
                                    </a>
                                @endif
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.away="open = false" class="flex items-center text-white hover:text-pink-300 focus:outline-none">
                                <i class="fas fa-user-circle text-2xl"></i>
                                <span class="ml-1 text-sm hidden sm:inline text-white">Acceder</span>
                                <i class="fas fa-chevron-down text-xs ml-1"></i>
                            </button>

                            <div x-show="open" x-transition:enter.duration.300ms x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-in-alt mr-2"></i> Iniciar sesión
                                </a>
                                <a href="{{ route('register') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user-plus mr-2"></i> Registrarse
                                </a>
                            </div>
                        </div>
                    @endauth

                    <a href="{{ route('carrito.index') }}" class="relative text-white hover:text-pink-300 mr-2">
                        <i class="fas fa-shopping-cart text-2xl"></i>
                        @auth
                            @php
                                $cartCount = Auth::user()->carrito->sum('cantidad');
                            @endphp
                            @if($cartCount > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        @endauth
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    {{-- ===== FOOTER COMPLETO CON CAMBIOS ===== --}}
    <footer class="bg-gray-800 text-gray-300 border-t-4 border-pink-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                {{-- Columna 1: Información de la empresa --}}
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Conexión Electrónica</h3>
                    <p class="text-sm leading-relaxed">
                        Tu tienda de confianza para componentes electrónicos, robótica, 
                        semiconductores y herramientas de desarrollo. Ofrecemos productos 
                        de calidad con envíos a nivel nacional. ¡Innovación y tecnología 
                        al alcance de tu mano!
                    </p>
                </div>

                {{-- Columna 2: Enlaces rápidos --}}
                <div>
                    <h4 class="text-white font-semibold mb-3">Enlaces rápidos</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('tienda') }}" class="hover:text-white transition">Productos</a></li>
                        <li><a href="{{ route('ofertas') }}" class="hover:text-white transition">Ofertas</a></li>
                    </ul>
                </div>

                {{-- Columna 3: Contacto --}}
                <div>
                    <h4 class="text-white font-semibold mb-3">Contacto</h4>
                    <ul class="space-y-2 text-sm">
                        <li><i class="fas fa-phone mr-2"></i> +591 72382122</li>
                        <li><i class="fas fa-envelope mr-2"></i> conexionelectronica7@gmail.com</li>
                        <li><i class="fas fa-map-marker-alt mr-2"></i> Potosí - Bolivia</li>
                    </ul>
                </div>

                {{-- Columna 4: Redes sociales --}}
                <div>
                    <h4 class="text-white font-semibold mb-3">Síguenos</h4>
                    <div class="flex space-x-4 text-xl">
                        {{-- Facebook --}}
                        <a href="https://www.facebook.com/profile.php?id=61591145990948" target="_blank" class="hover:text-white transition">
                            <i class="fab fa-facebook"></i>
                        </a>
                        {{-- TikTok --}}
                        <a href="https://www.tiktok.com/@conexion.electron" target="_blank" class="hover:text-white transition">
                            <i class="fab fa-tiktok"></i>
                        </a>
                        {{-- YouTube --}}
                        <a href="https://www.youtube.com/@ConexionElectronica" target="_blank" class="hover:text-white transition">
                            <i class="fab fa-youtube"></i>
                        </a>
                        {{-- WhatsApp --}}
                        <a href="https://wa.me/qr/NG4SBJXT4FONK1" target="_blank" class="hover:text-white transition">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        {{-- Gmail --}}
                        <a href="mailto:conexionelectronica7@gmail.com" class="hover:text-white transition">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">¡Síguenos en todas nuestras redes!</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-4 text-center text-sm">
                &copy; {{ date('Y') }} Conexión Electrónica. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>