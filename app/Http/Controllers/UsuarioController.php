<?php

namespace App\Http\Controllers;

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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
                'username' => 'required',
                'password' => 'required',
            ]);

            // Comprobar si los datos son validos
            if ($validate->fails()) { // en caso si los datos fallan la validacion
                // La validacion ha fallado
                $data = array(
                    'status' => 'Error',
                    'code' => 400,
                    'message' => 'El usuario no se ha creado',
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
                        'message' => 'Ya existe un registro con el Nro. de carnet o email.'
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
        return 'Accion de Login de un usuario';
    }
}
