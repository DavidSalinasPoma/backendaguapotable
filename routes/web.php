<?php

use App\Http\Controllers\AperturaController;
use App\Http\Controllers\BarrioController;
use App\Http\Controllers\ConsumoController;
use App\Http\Controllers\DetalleController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ListaController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\PruebasController;
use App\Http\Controllers\RenewController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\SocioController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


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





// Ruta personalizada usuario
Route::post('/api/register', [UserController::class, 'register']);
Route::post('/api/login', [UserController::class, 'login']);

Route::group(['middleware' => ['cors']], function () {
    //Rutas a las que se permitirá acceso
    /*************VERIFICAR TOKEN CON EL GUARD DE ANGULAR********/
    // Utilizando rutas automatica Barrio 
    Route::post('/api/login/renew', [RenewController::class, 'renew']);
});

// Grupo de rutas que necesitan el token
Route::group(['middleware' => ['auth:sanctum']], function () {



    // /*************RUTAS PARA USUARIOS********/
    // Utilizando rutas automatica usuario 
    Route::resource('/api/user', UserController::class);

    // Buscar Usuario
    Route::post('/api/buscar/usuario', [UserController::class, 'buscarUsuario']);


    /*************RUTAS PARA PERSONAS********/
    // Utilizando rutas automatica usuario 
    Route::resource('/api/persona', PersonaController::class);
    // Buscar Personas
    Route::post('/api/buscar/personas', [PersonaController::class, 'buscarPersonas']);
    // Buscar Personas Usuarios
    Route::get('/api/buscar/personauser', [PersonaController::class, 'buscarPersonaUsuario']);

    /*************RUTAS PARA SOCIOS********/
    // Utilizando rutas automatica socio
    Route::resource('/api/socio', SocioController::class);
    // Buscar Socios
    Route::post('/api/buscar/socios', [SocioController::class, 'buscarSocios']);
    Route::get('/api/show/showsocios/{id}', [SocioController::class, 'showSocios']);

    /*************RUTAS PARA EMPLEADOS********/
    // Utilizando rutas automatica empleado 
    Route::resource('/api/empleado', EmpleadoController::class);

    /*************RUTAS PARA EVENTOS********/
    // Utilizando rutas automatica evento 
    Route::resource('/api/evento', EventoController::class);
    // Buscar Personas
    Route::post('/api/buscar/eventos', [EventoController::class, 'buscarEvento']);

    /*************RUTAS PARA Servicios********/
    // Utilizando rutas automatica servicio 
    Route::resource('/api/servicio', ServicioController::class);
    // Buscar Personas
    Route::post('/api/buscar/servicios', [ServicioController::class, 'buscarServicio']);

    /*************RUTAS PARA Barrio********/
    // Utilizando rutas automatica Barrio 
    Route::resource('/api/barrio', BarrioController::class);
    // Buscar barrios
    Route::post('/api/buscar/barrios', [BarrioController::class, 'buscarBarrios']);

    /*************RUTAS PARA APERTURAS********/
    // Utilizando rutas automatica aperturas 
    Route::resource('/api/apertura', AperturaController::class);

    /*************RUTAS PARA LISTAR SOCIOS********/
    // Utilizando rutas automatica aperturas 
    Route::resource('/api/lista', ListaController::class);
    // Buscar listas
    Route::post('/api/buscar/listas', [ListaController::class, 'buscarSocios']);
    Route::get('/api/buscar/validarLista', [ListaController::class, 'validarLista']);

    /*************RUTAS PARA CONSUMO********/
    // Utilizando rutas automatica aperturas 
    Route::resource('/api/consumo', ConsumoController::class);
    // Buscar Consumo
    Route::post('/api/desplegar/consumos', [ConsumoController::class, 'showConsumos']);
    Route::post('/api/desplegar/precios', [ConsumoController::class, 'preciosConsumos']);

    /*************RUTAS PARA PRODUCTOS********/
    // Utilizando rutas automatica productos
    Route::resource('/api/producto', ProductosController::class);
    Route::put('/api/delete/productos/{id}', [ProductosController::class, 'eliminarProducto']);

    /*************RUTAS PARA FACTURAS********/
    // Utilizando rutas automatica Factura 
    Route::resource('/api/factura', FacturaController::class);
    // Buscar Facturas
    Route::post('/api/ubicar/facturas', [FacturaController::class, 'buscarFacturas']);
    Route::get('/api/ubicar/pruebas', [FacturaController::class, 'pruebasFacturas']);
    Route::get('/api/show/retrasofactura/{id}', [FacturaController::class, 'retrasoFactura']);

    /*************RUTAS PARA DETALLES********/
    // Utilizando rutas automatica Detalle 
    Route::resource('/api/detalle', DetalleController::class);


    // Ruta para cerrar sesion o eliminar un token
    Route::post('/api/logout', [UserController::class, 'logout']);
});
