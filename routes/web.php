<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\FormatoController;
use Illuminate\Support\Facades\Route;


// ==========================
// LOGIN Y DASHBOARD
// ==========================
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->middleware('auth')->name('admin.dashboard');
Route::get('/user/dashboard', [UserController::class, 'dashboard'])->middleware('auth')->name('user.dashboard');

// ==========================
// ADMINISTRACIÓN DE USUARIOS
// ==========================
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/users', [AdminController::class, 'usersIndex'])->middleware('perm:any,gestion_usuarios,crear_usuarios,editar_usuarios,eliminar_usuarios,cambiar_roles,activar_cuentas')->name('users.index');
    Route::post('/users', [AdminController::class, 'storeUser'])->middleware('perm:crear_usuarios')->name('users.store');
    Route::put('/users/{id}/update-role', [AdminController::class, 'updateUserRole'])->middleware('perm:cambiar_roles')->name('users.update-role');
    Route::put('/users/{id}/update-permissions', [AdminController::class, 'updateUserPermissions'])->middleware('perm:cambiar_roles')->name('users.update-permissions');
    Route::get('/users/{id}/permissions', [AdminController::class, 'redirectUserPermissions'])->middleware('perm:cambiar_roles')->name('users.permissions');
    Route::put('/users/{id}/permissions', [AdminController::class, 'updateUserPermissions'])->middleware('perm:cambiar_roles')->name('users.permissions.update');
    Route::put('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->middleware('perm:activar_cuentas')->name('users.toggle-status');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->middleware('perm:editar_usuarios')->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->middleware('perm:eliminar_usuarios')->name('users.destroy');

    // Auditoría
    Route::get('/movimientos', [MovimientoController::class, 'index'])->middleware('perm:any,gestion_usuarios')->name('movimientos.index');
});


// ==========================
// 📂 FORMATOS ADMIN
// ==========================
Route::prefix('admin/formatos')->name('admin.formatos.')->group(function () {

    // 🔹 Vistas principales
    Route::get('/', [FormatoController::class, 'index'])->name('index');
    Route::get('/create', [FormatoController::class, 'create'])->name('create');

    // Formatos A–D
    Route::get('/a', [FormatoController::class, 'formatoA'])->name('a');
    Route::post('/a', [FormatoController::class, 'storeA'])->name('a.store');

    Route::get('/b', [FormatoController::class, 'formatoB'])->name('b');
    Route::post('/b', [FormatoController::class, 'storeB'])->name('b.store');

    Route::get('/c', [FormatoController::class, 'formatoC'])->name('c');
    Route::post('/c', [FormatoController::class, 'storeC'])->name('c.store');

    Route::get('/d', [FormatoController::class, 'formatoD'])->name('d');
    Route::post('/d', [FormatoController::class, 'storeD'])->name('d.store');

    

    // 🔹 FORMATO A
    Route::get('/a/{id}/preview', [FormatoController::class, 'previewA'])->name('a.preview');
    Route::get('/a/{id}/pdf', [FormatoController::class, 'generarPDFA'])->name('a.pdf');

    // 🔹 FORMATO B
    Route::get('/b/{id}/preview', [FormatoController::class, 'previewB'])->name('b.preview');
    Route::get('/b/{id}/pdf', [FormatoController::class, 'generarPDFB'])->name('b.pdf');

    // 🔹 FORMATO C
    Route::get('/c/{id}/preview', [FormatoController::class, 'previewC'])->name('c.preview');
    Route::get('/c/{id}/pdf', [FormatoController::class, 'generarPDFC'])->name('c.pdf');

    // 🔹 FORMATO D
    Route::get('/d/{id}/preview', [FormatoController::class, 'previewD'])->name('d.preview');
    Route::get('/d/{id}/pdf', [FormatoController::class, 'generarPDFD'])->name('d.pdf');

// 📊 REPORTE GENERAL DE FORMATOS
Route::get('/reporte/general', [FormatoController::class, 'reporteGeneral'])
    ->name('reporte.general');
});