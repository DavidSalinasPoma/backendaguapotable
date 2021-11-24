<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;

use App\Models\Usuario;

class JwtAuth
{

    // Pasos para crear un token
    // 1.- Buscar si existe el usuario con sus credenciales(name_user y contraseña) en la base de datos
    // 2.- Comprobar si son correctas si el(name_user y contraseña son correctas).
    // 3.- Generar el token con los datos del usuario identificado.(si el nome_usuer y contraseña) son correctas.
    // 4.- Devolver los datos decodificados o el token, en funcion de un parametro.

    // Parametros o propiedades de la clase
    public $key;

    // Metodo Constructor
    public function __construct()
    {
        $this->key = 'Esta es una clave super secreta 123'; // es una clave randon
    }
    // geters and seters(encapsulamiento)
    public function setKey($key)
    {
        $this->key = $key;
    }
    public function getKey()
    {
        return $this->key;
    }

    // Metodos de comportamiento(Primero crear un provider para utilizar en laravel)
    public function singup()
    {
        return 'Metodo JWTAUTH';
    }
}
