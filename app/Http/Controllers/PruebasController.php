<?php

namespace App\Http\Controllers;

class PruebasController extends Controllers
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
}
