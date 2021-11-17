<?php

use App\Http\Controllers\PersonaController;
use App\Http\Controllers\PruebasController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Rutas de prueba
Route::get('/', function () {
    return view('welcome');
});

Route::get('/pruebas/{nombre?}', function ($nombre = null) {

    $texto = '<h2>Hola mundo desde una Ruta</h2>';
    $texto .= 'Nombre: ' . $nombre;

    return  view('pruebas', array(
        'texto' => $texto
    ));
});

Route::get('/animales', [PruebasController::class, 'index']);

Route::get('/test-orm', [PruebasController::class, 'testOrm']);


// Rutas de API
/* Metodos Http comunes
GET: Conseguir datos o recursos
POST: Guardar datos o recursos o hacer logica desde un formulario
PUT: Actualizar recursos o datos.
DELETE: Para eliminar datos o recursos.
*/
// rutas de prueba
Route::get('/usuario/pruebas', [UsuarioController::class, 'pruebas']);
Route::get('/persona/pruebas', [PersonaController::class, 'pruebas']);


// Rutas del controlador de Usuarios
Route::post('/api/register', [UsuarioController::class, 'register']);
Route::post('/api/login', [UsuarioController::class, 'login']);
