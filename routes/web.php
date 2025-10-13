<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Login
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login']);

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboards
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');

// Gestión de usuarios CON PERMISOS
Route::prefix('admin')->name('admin.')->group(function () {
    // Ver lista de usuarios
    Route::get('/users', [AdminController::class, 'usersIndex'])->name('users.index');

    // Crear usuario nuevo
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');

    // Cambiar rol
    Route::put('/users/{id}/update-role', [AdminController::class, 'updateUserRole'])->name('users.update-role');

    // Actualizar permisos - ✅ NUEVA RUTA
    Route::put('/users/{id}/update-permissions', [AdminController::class, 'updateUserPermissions'])->name('users.update-permissions');

    // Activar/desactivar cuenta
    Route::put('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');

    // Editar usuario
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');

    // Crear cuenta para usuario existente
    Route::post('/users/{id}/create-account', [AdminController::class, 'createUserAccount'])->name('users.create-account');

    // Eliminar usuario
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');
});