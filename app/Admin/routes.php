<?php

use App\Admin\Controllers\ComunicadoController;
use App\Admin\Controllers\EmpleadoController;
use App\Admin\Controllers\EmpleadoMesController;
use App\Admin\Controllers\EmpleadoRHController;
use App\Admin\Controllers\EmpleadoRHExcelController;
use App\Admin\Controllers\EventoController;
use App\Admin\Controllers\NoticiaController;
use App\Admin\Controllers\PermisoController;
use App\Admin\Controllers\ReservacionController;
use App\Admin\Controllers\TicketController;
use App\Admin\Controllers\UsuarioRHExcelController;
use App\Admin\Controllers\VacacionController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use OpenAdmin\Admin\Facades\Admin;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
    'as' => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('noticias', NoticiaController::class);
    $router->resource('comunicados', ComunicadoController::class);
    $router->resource('empleado-mes', EmpleadoMesController::class);
    $router->resource('eventos', EventoController::class);
    $router->resource('reservacions', ReservacionController::class);
    $router->resource('vacacions', VacacionController::class);
    $router->resource('tickets', TicketController::class);
    $router->resource('empleado-r-hs', EmpleadoRHController::class);
    $router->resource('empleado-r-h-excels', EmpleadoRHExcelController::class);
    $router->resource('usuario-r-h-excels', UsuarioRHExcelController::class);
    $router->resource('permisos', PermisoController::class);
    $router->resource('vacacions', VacacionController::class);
});
