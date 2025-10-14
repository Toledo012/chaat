<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovimientoController;
use Illuminate\Support\Facades\Route;

// Login
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login']);

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboards
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->middleware('auth')->name('admin.dashboard');
Route::get('/user/dashboard', [UserController::class, 'dashboard'])->middleware('auth')->name('user.dashboard');

// Gestión de usuarios CON PERMISOS
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    // Ver lista de usuarios
    Route::get('/users', [AdminController::class, 'usersIndex'])->middleware('perm:any,gestion_usuarios,crear_usuarios,editar_usuarios,eliminar_usuarios,cambiar_roles,activar_cuentas')->name('users.index');

    // Crear usuario nuevo
    Route::post('/users', [AdminController::class, 'storeUser'])->middleware('perm:crear_usuarios')->name('users.store');

    // Cambiar rol
    Route::put('/users/{id}/update-role', [AdminController::class, 'updateUserRole'])->middleware('perm:cambiar_roles')->name('users.update-role');

    // Actualizar permisos - âœ… NUEVA RUTA
    Route::put('/users/{id}/update-permissions', [AdminController::class, 'updateUserPermissions'])->middleware('perm:cambiar_roles')->name('users.update-permissions');
    Route::get('/users/{id}/permissions', [AdminController::class, 'redirectUserPermissions'])->middleware('perm:cambiar_roles')->name('users.permissions');
    Route::put('/users/{id}/permissions', [AdminController::class, 'updateUserPermissions'])->middleware('perm:cambiar_roles')->name('users.permissions.update');
    Route::get('/usuarios/{id}/permisos', [AdminController::class, 'redirectUserPermissions'])->middleware('perm:cambiar_roles')->name('usuarios.permisos');
    Route::put('/usuarios/{id}/permisos', [AdminController::class, 'updateUserPermissions'])->middleware('perm:cambiar_roles')->name('usuarios.permisos.update');

    // Activar/desactivar cuenta
    Route::put('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->middleware('perm:activar_cuentas')->name('users.toggle-status');

    // Editar usuario
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->middleware('perm:editar_usuarios')->name('users.update');

    // Crear cuenta para usuario existente
    Route::post('/users/{id}/create-account', [AdminController::class, 'createUserAccount'])->middleware('perm:crear_usuarios')->name('users.create-account');

    // Eliminar usuario
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->middleware('perm:eliminar_usuarios')->name('users.destroy');

    // Auditoría de movimientos
    Route::get('/movimientos', [MovimientoController::class, 'index'])->middleware('perm:any,gestion_usuarios')->name('movimientos.index');
});

