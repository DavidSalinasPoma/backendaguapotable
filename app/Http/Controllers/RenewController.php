<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RenewController extends Controller
{
    // Metodo para controlar el token autentico
    public function renew(Request $request)
    {
        // $token que nos llega de la cabezera en un hedder de Angular
        $token = $request->header('Authorization');
        $separador = " ";
        $separada = explode($separador, $token);
        $token = $separada[1];
        // echo $token;
        // die();

        $data = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'Token valido',
            'token'  => $token
        );
        // Devuelve en json con laravel
        return response()->json($data, $data['code']);
    }
}
