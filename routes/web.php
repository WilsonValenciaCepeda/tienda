<?php

use App\Http\Controllers\CarritoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Página de inicio
Route::get('/', function () {
    return view('welcome');
})->name('inicio');

// Tienda (catálogo)
Route::get('/tienda', [ProductoController::class, 'index'])->name('tienda');
Route::get('/producto/{producto}', [ProductoController::class, 'show'])->name('producto.show');

// Ofertas
Route::get('/ofertas', [ProductoController::class, 'ofertas'])->name('ofertas');

// API de búsqueda (autocompletado)
Route::get('/api/search', [ProductoController::class, 'search'])->name('api.search');

// Dashboard (redirige a tienda)
Route::get('/dashboard', function () {
    return redirect()->route('tienda');
})->middleware(['auth'])->name('dashboard');

// Perfil de usuario
Route::middleware(['auth'])->group(function () {
    Route::get('/mi-informacion', function () {
        return view('perfil.informacion');
    })->name('perfil.informacion');

    Route::get('/mi-informacion/editar', function () {
        return view('perfil.editar');
    })->name('perfil.editar');

    Route::put('/mi-informacion/editar', [ProfileController::class, 'update'])->name('perfil.editar.update');

    Route::get('/mi-informacion/pago', function () {
        return view('perfil.pago');
    })->name('perfil.pago');

    Route::get('/mi-informacion/soporte', function () {
        return view('perfil.soporte');
    })->name('perfil.soporte');
});

// Rutas protegidas
Route::middleware(['auth'])->group(function () {

    // Carrito
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/agregar', [CarritoController::class, 'agregar'])->name('carrito.agregar');
    Route::patch('/carrito/actualizar/{carrito}', [CarritoController::class, 'actualizar'])->name('carrito.actualizar');
    Route::delete('/carrito/eliminar/{carrito}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');
    Route::delete('/carrito/vaciar', [CarritoController::class, 'vaciar'])->name('carrito.vaciar');

    // Compra individual de un producto del carrito
    Route::post('/carrito/comprar/{carrito}', [CarritoController::class, 'comprarIndividual'])->name('carrito.comprar.individual');

    // Pedidos
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::post('/checkout', [PedidoController::class, 'checkout'])->name('checkout');

    // Administración (solo admin)
    Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
        Route::get('/productos', [ProductoController::class, 'adminIndex'])->name('productos.index');
        Route::get('/productos/crear', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/productos/{producto}/editar', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

        Route::get('/pedidos', [PedidoController::class, 'adminIndex'])->name('pedidos.index');
        Route::patch('/pedidos/{pedido}/estado', [PedidoController::class, 'actualizarEstado'])->name('pedidos.estado');
    });
});

require __DIR__ . '/auth.php';
