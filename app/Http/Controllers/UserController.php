<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
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


    // Metodo para registrar Usuarios
    public function register(RegisterRequest $request)
    {
        // 1.-Recoger los usuarios por post

        $params = (object) $request->all(); // Devulve un obejto



        // 2.-Validar datos
        $validate = Validator::make($request->all(), [
            // 'persona_id' => 'required',
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);

        // Comprobar si los datos son validos
        if ($validate->fails()) { // en caso si los datos fallan la validacion
            // La validacion ha fallado
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Los datos enviados no son correctos',
                'user' => $request->all(),
                'errors' => $validate->errors()
            );
        } else {
            // Si la validacion pasa correctamente

            // 3.-Cifrar la contraseña
            $pwd = bcrypt($params->password); // se cifra la contraseña 4 veces

            // Crear el objeto usuario para guardar en la base de datos
            $user = new User();
            // $user->persona_id = $paramsArray['persona_id'];
            // $user->username = $paramsArray['username'];
            // $user->password = $pwd;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = $pwd;


            try {
                // Guardar en la base de datos

                // 5.-Crear el usuario
                $user->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente',
                    'usuario' => $user
                );
            } catch (Exception $e) {
                $data = array(
                    'status' => 'Error',
                    'code' => 404,
                    'message' => $e
                );
            }
        }


        // Devuelve en json con laravel
        return response()->json($data, $data['code']);
    }

    // Metodo para login de usuarios
    public function login(LoginRequest $request)
    {

        // 1.- Validar los datos recibidos por POST.
        $validate = Validator::make($request->all(), [
            // 4.-Comprobar si el usuario ya existe duplicado
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Comprobar si los datos son validos
        if ($validate->fails()) { // en caso si los datos fallan la validacion
            // La validacion ha fallado
            $data = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'El usuario no se ha podido identificar Faltan datos',
                'errors' => $validate->errors()
            );
        } else {


            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {

                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Las credenciales son incorrectas.',
                );
                return response()->json($data, $data['code']);
            }
            // echo $user;
            // echo 'Hola t';
            // die();
            $token = $user->createToken($request->email)->plainTextToken;
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Credenciales correctas',
                'token' => $token
            );
        }
        // Devuelve en json con laravel
        return response()->json($data, $data['code']);
    }

    // Para cerrar sesion
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $data = array(
            'status' => 'success',
            'code' => 200,
            'message' => 'Token Eliminado Correctamente',
        );
        // Devuelve en json con laravel
        return response()->json($data, $data['code']);
    }
}
