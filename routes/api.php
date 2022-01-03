<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/*************RUTAS PARA USUARIOS********/
// Utilizando rutas automatica usuario 
// Route::resource('/api/usuario', UsuarioController::class);
// // Ruta personalizada usuario
// Route::post('/api/register', [UserController::class, 'register']);
// Route::post('/api/login', [UsuarioController::class, 'login']);

// // Ruta de prueba
// Route::get('/api/prueba', [UserController::class, 'prueba']);
