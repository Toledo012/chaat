<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\FormatoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\DeptoViewController;
use App\Http\Controllers\TicketController;

use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| DASHBOARDS (protegidos por ROL)
|--------------------------------------------------------------------------
*/
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->middleware(['auth', 'rol:Administrador'])
    ->name('admin.dashboard');

Route::get('/user/dashboard', [UserController::class, 'dashboard'])
    ->middleware(['auth', 'rol:Usuario'])
    ->name('user.dashboard');


Route::get('/departamento/dashboard', [DeptoViewController::class, 'dashboard'])
    ->middleware(['auth', 'rol:Departamento'])
    ->name('departamento.dashboard');

/*
|--------------------------------------------------------------------------
| USER: CAMBIAR PASSWORD (solo Usuario)
|--------------------------------------------------------------------------
*/
Route::put('/user/update-password', [UserController::class, 'updatePassword'])
    ->middleware(['auth', 'rol:Usuario'])
    ->name('user.update-password');


/*
|--------------------------------------------------------------------------
| ADMIN: USUARIOS + AUDITORÍA
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'rol:Administrador'])
    ->group(function () {

        // Usuarios
        Route::get('/users', [AdminController::class, 'usersIndex'])
            ->middleware('perm:any,gestion_usuarios,crear_usuarios,editar_usuarios,eliminar_usuarios,cambiar_roles,activar_cuentas')
            ->name('users.index');

        Route::post('/users', [AdminController::class, 'storeUser'])
            ->middleware('perm:crear_usuarios')
            ->name('users.store');

        Route::put('/users/{id}/update-role', [AdminController::class, 'updateUserRole'])
            ->middleware('perm:cambiar_roles')
            ->name('users.update-role');

        Route::put('/users/{id}/update-permissions', [AdminController::class, 'updateUserPermissions'])
            ->middleware('perm:cambiar_roles')
            ->name('users.update-permissions');

        Route::get('/users/{id}/permissions', [AdminController::class, 'redirectUserPermissions'])
            ->middleware('perm:cambiar_roles')
            ->name('users.permissions');

        Route::put('/users/{id}/permissions', [AdminController::class, 'updateUserPermissions'])
            ->middleware('perm:cambiar_roles')
            ->name('users.permissions.update');

        Route::put('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])
            ->middleware('perm:activar_cuentas')
            ->name('users.toggle-status');

        Route::put('/users/{id}', [AdminController::class, 'updateUser'])
            ->middleware('perm:editar_usuarios')
            ->name('users.update');

        Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])
            ->middleware('perm:eliminar_usuarios')
            ->name('users.destroy');

        // Auditoría
        Route::get('/movimientos', [MovimientoController::class, 'index'])
            ->middleware('perm:any,gestion_usuarios')
            ->name('movimientos.index');
    });


/*
|--------------------------------------------------------------------------
| ADMIN: FORMATOS
|--------------------------------------------------------------------------
*/
Route::prefix('admin/formatos')
    ->name('admin.formatos.')
    ->middleware(['auth', 'rol:Administrador', 'perm:gestion_formatos'])
    ->group(function () {

        // Listado y creación
        Route::get('/', [FormatoController::class, 'index'])->name('index');
        Route::get('/create', [FormatoController::class, 'create'])->name('create');

        // Formularios A–D
        Route::get('/a', [FormatoController::class, 'formatoA'])->name('a');
        Route::post('/a', [FormatoController::class, 'storeA'])->name('a.store');

        Route::get('/b', [FormatoController::class, 'formatoB'])->name('b');
        Route::post('/b', [FormatoController::class, 'storeB'])->name('b.store');

        Route::get('/c', [FormatoController::class, 'formatoC'])->name('c');
        Route::post('/c', [FormatoController::class, 'storeC'])->name('c.store');

        Route::get('/d', [FormatoController::class, 'formatoD'])->name('d');
        Route::post('/d', [FormatoController::class, 'storeD'])->name('d.store');

        // Previsualizaciones y PDFs
        Route::get('/a/{id}/preview', [FormatoController::class, 'previewA'])->name('a.preview');
        Route::get('/a/{id}/pdf', [FormatoController::class, 'generarPDFA'])->name('a.pdf');

        Route::get('/b/{id}/preview', [FormatoController::class, 'previewB'])->name('b.preview');
        Route::get('/b/{id}/pdf', [FormatoController::class, 'generarPDFB'])->name('b.pdf');

        Route::get('/c/{id}/preview', [FormatoController::class, 'previewC'])->name('c.preview');
        Route::get('/c/{id}/pdf', [FormatoController::class, 'generarPDFC'])->name('c.pdf');

        Route::get('/d/{id}/preview', [FormatoController::class, 'previewD'])->name('d.preview');
        Route::get('/d/{id}/pdf', [FormatoController::class, 'generarPDFD'])->name('d.pdf');

        // Reporte general
        Route::get('/reporte/general', [FormatoController::class, 'reporteGeneral'])->name('reporte.general');

        // Editar formatos
        Route::get('/editar/{tipo}/{id}', [FormatoController::class, 'edit'])->name('edit');
        Route::post('/editar/{tipo}/{id}', [FormatoController::class, 'update'])->name('update');
    });


/*
|--------------------------------------------------------------------------
| ADMIN: MATERIALES
|--------------------------------------------------------------------------
*/
Route::prefix('admin/materiales')
    ->name('admin.materiales.')
    ->middleware(['auth', 'rol:Administrador', 'perm:gestion_formatos'])
    ->group(function () {

        Route::get('/', [MaterialController::class, 'index'])->name('index');
        Route::get('/create', [MaterialController::class, 'create'])->name('create');
        Route::post('/create', [MaterialController::class, 'store'])->name('store');

        Route::get('/{id}/edit', [MaterialController::class, 'edit'])->name('edit');
        Route::put('/{id}/edit', [MaterialController::class, 'update'])->name('update');

        Route::delete('/{id}', [MaterialController::class, 'destroy'])->name('destroy');

        Route::delete('/eliminar-multiples', [MaterialController::class, 'destroyMultiple'])
            ->name('destroy.multiple');
    });


/*
|--------------------------------------------------------------------------
| ADMIN: DEPARTAMENTOS (catálogo)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth', 'rol:Administrador'])
    ->group(function () {

        Route::resource('departamentos', DepartamentoController::class)
            ->names('admin.departamentos')
            ->except(['show', 'destroy']);
    });


    // ==========================
//  TICKETS - ADMIN
// ==========================
Route::prefix('admin/tickets')
    ->name('admin.tickets.')
    ->middleware(['auth', 'rol:Administrador'])
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminTicketController::class, 'index'])->name('index');
        Route::post('/{ticket}/asignar', [\App\Http\Controllers\AdminTicketController::class, 'asignar'])->name('asignar');

        Route::get('/{ticket}/completar', [\App\Http\Controllers\AdminTicketController::class, 'completar'])
    ->name('completar');

        Route::post('/{ticket}/cancelar', [\App\Http\Controllers\AdminTicketController::class, 'cancelar'])->name('cancelar');

                Route::post('/', [\App\Http\Controllers\AdminTicketController::class, 'store'])->name('store');


    });


// ==========================
// TICKETS - USUARIO (TECNICO)
// ==========================
Route::prefix('user/tickets')
    ->name('user.tickets.')
    ->middleware(['auth', 'rol:Usuario'])
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\UserTicketController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\UserTicketController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\UserTicketController::class, 'store'])->name('store');

        Route::post('/{ticket}/tomar', [\App\Http\Controllers\UserTicketController::class, 'tomar'])->name('tomar');
        Route::get('/{ticket}/completar', [\App\Http\Controllers\UserTicketController::class, 'completar'])->name('completar');
    });


// ==========================
// TICKETS - DEPARTAMENTO
// ==========================
Route::prefix('departamento/tickets')
    ->name('departamento.tickets.')
    ->middleware(['auth', 'rol:Departamento'])
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\DeptTicketController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\DeptTicketController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\DeptTicketController::class, 'store'])->name('store');
        Route::post('/{ticket}/cancelar', [\App\Http\Controllers\DeptTicketController::class, 'cancelar'])->name('cancelar');



    });



Route::get('/test-mail', function () {
    Mail::raw('Hola, este es un correo de prueba desde Laravel SEMAHN', function ($msg) {
        $msg->to('TU_CORREO_DESTINO@gmail.com')
            ->subject('Prueba de correo - SEMAHN Tickets');
    });

    return 'Correo enviado (si no llegó, revisa logs).';
});



