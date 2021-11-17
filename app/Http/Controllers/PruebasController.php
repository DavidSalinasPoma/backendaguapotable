<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Usuario;

use function GuzzleHttp\Promise\all;

class PruebasController extends Controller
{
    public function index()
    {
        $titulo = 'Animales';

        $animales = ['perro', 'gato', 'Tigre'];

        return view('pruebas.index', array(
            'titulo' => $titulo,
            'animales' => $animales,
        ));
    }

    public function testOrm()
    {
        $usuarios = Usuario::all();
        // echo "<pre>{ var_dump($usuarios) }</pre>";
        foreach ($usuarios as $usuario) {
            echo "<h1>" . $usuario->username . "</h1>";
            echo "<span style='color:gray'>{$usuario->persona->nombres} - {$usuario->persona->ap_paterno}</span>";
            echo "<p>" . $usuario->password . "</p>";
            echo "<hr>";
        }
        die();
    }
}
