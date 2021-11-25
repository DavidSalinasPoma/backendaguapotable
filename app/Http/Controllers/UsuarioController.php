<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\Usuario;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Usuario::all(); // Saca con el usuario relacionado de la base de datos
        $data = array(
            'code' => 200,
            'status' => 'success',
            'usuario' => $user
        );
        return response()->json($data, $data['code']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $usuario = Usuario::find($id);

        // Comprobamos si es un objeto eso quiere decir si exist en la base de datos.
        if (is_object($usuario)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'carrera' => $usuario
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El usuario no existe'
            );
        }
        return response()->json($data, $data['code']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $jwtauth = new JwtAuth();

        // $token que nos llega de la cabezera en un hedder de Angular
        $token = $request->header('token-usuario');
        // echo $token;
        // die();

        // 1.- Comprobar si el Usuario esta identificado.
        $checkToken = $jwtauth->checkToken($token); // True si el token es correcto 
        // echo $checkToken;
        // die();

        // 2.- Recoger los datos por POST.
        $json = $request->input('json', null);
        $paramsArray = json_decode($json, true); // devuelve un array

        if ($checkToken == true && !empty($paramsArray)) {
            // Actualizar Usuario.

            // Sacar el usuario identificado
            $userIdentificado = $jwtauth->checkToken($token, true);
            // var_dump($userIdentificado);
            // die();

            // 3.- Validar datos recogidos por POST. pasando al getIdentity true
            $validate = Validator::make($paramsArray, [

                // 4.-Comprobar si el carnet y el email ya existe duplicado
                // 'carnet' => 'required|unique:usuarios',
                'username' => 'required|unique:usuarios',
                'password' => 'required',
                'persona_id' => 'required'

            ]);
            // // Comprobar si los datos son validos
            if ($validate->fails()) { // en caso si los datos fallan la validacion
                // La validacion ha fallado
                $data = array(
                    'status' => 'Error',
                    'code' => 400,
                    'message' => 'Datos incorrectos no se puede actualizar',
                    'errors' => $validate->errors()
                );
            } else {

                // 4.- Quitar los campos que no quiero actualizar de la peticion.
                // unset($paramsArray['id']);
                // unset($paramsArray['password']);
                // // unset($paramsArray['antiguo']);
                unset($paramsArray['created_at']);
                // unset($paramsArray['updated_at']);

                // 3.- Cifrar la PASSWORD.
                // $paramsArray['password'] = hash('sha256', $paramsArray['password']); // para verificar que las contraseña a consultar sean iguales.
                try {
                    // 5.- Actualizar los datos en la base de datos.
                    $user_update = Usuario::where('id', $id)->update($paramsArray);

                    // var_dump($user_update);
                    // die();
                    // 6.- Devolver el array con el resultado.
                    $data = array(
                        'status' => 'Succes',
                        'code' => 200,
                        'message' => 'El usuario se ha modificado correctamente',
                        'usuario' => $userIdentificado,
                        'changes' => $paramsArray
                    );
                } catch (Exception $e) {
                    $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'El nombre de usuario ya esta en uso.',
                        // 'error' => $e
                    );
                }
            }
        } else {
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'El usuario no se esta identificado correctamente',
            );
        }

        return response()->json($data, $data['code']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $usuario = Usuario::find($id); // Trae el usuario en formato JSON
        $paramsArray = json_decode($usuario, true); // devuelve un array
        // var_dump($paramsArray);
        // die();

        // Quitar los campos que no quiero actualizar de la peticion.
        unset($paramsArray['id']);
        unset($paramsArray['persona_id']);
        unset($paramsArray['username']);
        unset($paramsArray['created_at']);
        unset($paramsArray['updated_at']);

        // Campo stado a modificar
        $paramsArray['estado'] = 0;

        try {
            // 5.- Actualizar los datos en la base de datos.
            $user_update = Usuario::where('id', $id)->update($paramsArray);

            // 6.- Devolver el array con el resultado.
            $data = array(
                'status' => 'Succes',
                'code' => 200,
                'message' => 'El usuario ha sido dado de baja correctamente',
                'usuario' => $usuario,
                'changes' => $paramsArray
            );
        } catch (Exception $e) {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'El usuario no ha sido dado de baja',

            );
        }

        return response()->json($data, $data['code']);
    }

    // Pruebas de este controlador
    public function pruebas(Request $request)
    {
        return "Acción de pruebas de USUARIO-CONTROLLER";
    }

    // Metodo para registrar Usuarios
    public function register(Request $request)
    {
        // 1.-Recoger los usuarios por post
        $json = $request->input('json', null); // en caso que no llegara nada recibe  NULL
        // probar que llega los datos
        // var_dump($json);
        // Para que no pida ninguna vista y que corte ahi el programa
        // die();

        // Decodificamos el json que nos llega 
        $params = json_decode($json); // Devulve un obejto
        // echo var_dump($params); // Devuelve un dato
        // die();

        $paramsArray = json_decode($json, true); // nos devuelve un array
        // var_dump($paramsArray);
        // die();


        // Validar si esta vacio 
        if (!empty($params) && !empty($paramsArray)) {

            // Limpiar datos de espacios en blanco al principio y el final
            $paramsArray = array_map('trim', $paramsArray);

            // 2.-Validar datos
            $validate = Validator::make($paramsArray, [
                'persona_id' => 'required',
                'username' => 'required|unique:usuarios',
                'password' => 'required',
            ]);

            // Comprobar si los datos son validos
            if ($validate->fails()) { // en caso si los datos fallan la validacion
                // La validacion ha fallado
                $data = array(
                    'status' => 'Error',
                    'code' => 400,
                    'message' => 'Los datos enviados no son correctos',
                    'user' => $paramsArray,
                    'errors' => $validate->errors()
                );
            } else {
                // Si la validacion pasa correctamente

                // 3.-Cifrar la contraseña
                $pwd = hash('sha256', $params->password); // se cifra la contraseña 4 veces

                // Crear el objeto usuario para guardar en la base de datos
                $usuarios = new Usuario();
                $usuarios->persona_id = $paramsArray['persona_id'];
                $usuarios->username = $paramsArray['username'];
                $usuarios->password = $pwd;

                try {
                    // Guardar en la base de datos

                    // 5.-Crear el usuario
                    $usuarios->save();
                    $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'El usuario se ha creado correctamente',
                        'usuario' => $usuarios
                    );
                } catch (Exception $e) {
                    $data = array(
                        'status' => 'Error',
                        'code' => 404,
                        'message' => $e
                    );
                }
            }
        } else {
            $data = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos.'
            );
        }

        // Devuelve en json con laravel
        return response()->json($data, $data['code']);
    }

    // Metodo para login de usuarios
    public function login(Request $request)
    {
        $jwtauth = new JwtAuth();

        // 1.- Recibir datos por POST.
        $json = $request->input('json', null);
        $params = json_decode($json); // devuelve en un obejto
        $paramsArray = json_decode($json, true); // Devuelve en un array para hacer validaciones.
        // var_dump($paramsArray);
        // die();

        // 2.- Validar los datos recibidos por POST.
        $validate = Validator::make($paramsArray, [
            // 4.-Comprobar si el usuario ya existe duplicado
            'username' => 'required',
            'password' => 'required',
        ]);
        // Comprobar si los datos son validos
        if ($validate->fails()) { // en caso si los datos fallan la validacion
            // La validacion ha fallado
            $singup = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'El usuario no se ha podido identificar Faltan datos',
                'errors' => $validate->errors()
            );
        } else {
            // 3.- Cifrar la PASSWORD.
            $pwd = hash('sha256', $params->password); // para verificar que las contraseña a consultar sean iguales.
            // echo $pwd;
            // die();

            // 4.- Devolver token(codificado) o datos(en un objeto decodificado).
            // Este token sera el que recibiremos con el cliente y pasaremos a cada una de las peticines
            // http que realizemos a ciertos metodos de nuestra api, el API lo recibira y procesara el token
            // comprobara si es correcto. y si lo es me dejara entrar y si no lo es no lo hara.
            $singup = $jwtauth->singup($params->username, $pwd); // Por defecto token codificado.

            if (!empty($params->getToken)) { // si existe y no esta vacio y no es NULL.
                $singup = $jwtauth->singup($params->username, $pwd, true); // Token decodificado en un objeto.
            }
        }
        return response()->json($singup, 200);
    }
}
