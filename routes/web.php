<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\FormatoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminTicketController;


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

Route::put('/user/update-password', [UserController::class, 'updatePassword'])
    ->name('user.update-password');



// ==========================
// 📂 FORMATOS ADMIN
// ==========================
Route::prefix('admin/formatos')
    ->name('admin.formatos.')
    ->middleware(['auth', 'perm:gestion_formatos'])
    ->group(function () {

    // 🔹 Listado y creación
    Route::get('/', [FormatoController::class, 'index'])->name('index');
    Route::get('/create', [FormatoController::class, 'create'])->name('create');

    // 🔹 Formularios A–D
    Route::get('/a', [FormatoController::class, 'formatoA'])->name('a');
    Route::post('/a', [FormatoController::class, 'storeA'])->name('a.store');

    Route::get('/b', [FormatoController::class, 'formatoB'])->name('b');
    Route::post('/b', [FormatoController::class, 'storeB'])->name('b.store');

    Route::get('/c', [FormatoController::class, 'formatoC'])->name('c');
    Route::post('/c', [FormatoController::class, 'storeC'])->name('c.store');

    Route::get('/d', [FormatoController::class, 'formatoD'])->name('d');
    Route::post('/d', [FormatoController::class, 'storeD'])->name('d.store');

 

    // ==========================

    // Previsualizaciones y PDFs
    Route::get('/a/{id}/preview', [FormatoController::class, 'previewA'])->name('a.preview');
    Route::get('/a/{id}/pdf', [FormatoController::class, 'generarPDFA'])->name('a.pdf');

    Route::get('/b/{id}/preview', [FormatoController::class, 'previewB'])->name('b.preview');
    Route::get('/b/{id}/pdf', [FormatoController::class, 'generarPDFB'])->name('b.pdf');

    Route::get('/c/{id}/preview', [FormatoController::class, 'previewC'])->name('c.preview');
    Route::get('/c/{id}/pdf', [FormatoController::class, 'generarPDFC'])->name('c.pdf');

    Route::get('/d/{id}/preview', [FormatoController::class, 'previewD'])->name('d.preview');
    Route::get('/d/{id}/pdf', [FormatoController::class, 'generarPDFD'])->name('d.pdf');

    // 📊 Reporte general
    Route::get('/reporte/general', [FormatoController::class, 'reporteGeneral'])->name('reporte.general');

   // ==========================
    //editar formatos
        Route::get('/editar/{tipo}/{id}', [FormatoController::class, 'edit'])
            ->name('edit');

        Route::post('/editar/{tipo}/{id}', [FormatoController::class, 'update'])
            ->name('update');   



});

Route::prefix('admin/materiales')
    ->name('admin.materiales.')
    ->middleware(['auth', 'perm:gestion_formatos'])
    ->group(function () {

    Route::get('/', [MaterialController ::class, 'index'])->name('index');
    Route::get('/create', [MaterialController::class, 'create'])->name('create');
    Route::post('/create', [MaterialController::class, 'store'])->name('store');

    Route::get('/{id}/edit', [MaterialController::class, 'edit'])->name('edit');
    Route::put('/{id}/edit', [MaterialController::class, 'update'])->name('update');

    Route::delete('/{id}', [MaterialController::class, 'destroy'])->name('destroy');
Route::delete('/eliminar-multiples', [MaterialController::class, 'destroyMultiple'])
    ->name('destroy.multiple');


});


// ==========================
// DEPARTAMENTOS  ROL
Route::get('/depto/dashboard', [UserController::class, 'deptoDashboard'])
    ->middleware('auth')
    ->name('depto.dashboard');


// ==========================
// DEPARTAMENTOS SECCIÓN 

    Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::resource('departamentos', DepartamentoController::class)
        ->names('admin.departamentos')

        
        ->except(['show','destroy']);


});


// ==========================
// TICKETS
// ==========================
Route::middleware(['auth'])->prefix('tickets')->name('tickets.')->group(function () {
    Route::get('/', [TicketController::class, 'index'])->middleware('perm:ver_tickets')->name('index');
    Route::post('/', [TicketController::class, 'store'])->middleware('perm:crear_tickets')->name('store');
});



Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    Route::get('/tickets', [AdminTicketController::class, 'index'])
        ->middleware('perm:ver_tickets')
        ->name('tickets.index');

    // Técnico toma ticket (solo si lo creó, tu lógica ya lo valida)
    Route::post('/tickets/{id_ticket}/take', [AdminTicketController::class, 'take'])
        ->middleware('perm:tomar_tickets')
        ->name('tickets.take');

    //  Admin asigna/reasigna (solo admin)
    Route::post('/tickets/{id_ticket}/assign', [AdminTicketController::class, 'assign'])
        ->middleware('perm:gestion_usuarios')
        ->name('tickets.assign');

    // Admin cambia estado general (solo admin)
    Route::post('/tickets/{id_ticket}/status', [AdminTicketController::class, 'setStatus'])
        ->middleware('perm:gestion_usuarios')
        ->name('tickets.status');

    // Cerrar ticket: técnico asignado O admin (tu lógica ya lo valida)
    Route::post('/tickets/{id_ticket}/close', [AdminTicketController::class, 'close'])
        ->middleware('perm:cerrar_tickets')
        ->name('tickets.close');

    // Elegir formato desde ticket: técnico asignado o admin
    // (como NO habrá generar_formatos, usamos gestion_formatos)
    Route::post('/tickets/{id_ticket}/set-formato', [AdminTicketController::class, 'setFormato'])
        ->middleware('perm:gestion_formatos')
        ->name('tickets.set-formato');

    // Redirigir a formulario A/B/C/D
    Route::get('/tickets/{id_ticket}/generar-formato', [AdminTicketController::class, 'generarFormato'])
        ->middleware('perm:gestion_formatos')
        ->name('tickets.generar-formato');
});

