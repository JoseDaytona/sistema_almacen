<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Solo Usuarios No Autenticados
Route::group(['middleware' => 'guest'], function () {
    Route::post('auth', 'AuthController@login')->name('auth');
});

Route::get('unauthorized', 'AuthController@unauthorized')->name('unauthorized');

//Solo Usuarios Autenticados
Route::group(['middleware' => 'auth'], function () {
    
    //Solo Administradores
    Route::group(['middleware' => 'auth'], function () {
        //Route::resource('login', 'LoginController');
    });

    Route::resource('cliente', 'ClienteController');

    Route::resource('articulo', 'ArticuloController');

    Route::resource('colocacion', 'ColocacionController');

    Route::resource('empleado', 'EmpleadoController');

    Route::resource('pedido', 'PedidoController');

    Route::resource('usuario', 'UsuarioController');

});
