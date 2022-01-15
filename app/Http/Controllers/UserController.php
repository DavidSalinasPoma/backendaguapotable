<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\user\BuscarUsuarioRequest;
use App\Http\Requests\User\LogoutRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\Persona;
use App\Models\User;
use App\Models\Usuario;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // $Cliente = Cliente::with('sector_pk', 'sectorLanguage_pk')
        // ->where('active', '=', 1)
        // ->get();
        $userPersona = User::orderBy('id', 'DESC')->paginate(5)->load('persona');
        $user = User::orderBy('id', 'DESC')->paginate(5);
        $total = User::count();

        // echo $userPersona;
        // die();

        $data = array(
            'code' => 200,
            'status' => 'success',
            'total' => $total,
            'paginate' => [
                "total" => $user->total(),
                "current_page" => $user->currentPage(),
                "per_page" => $user->perPage(),
                "last_page" => $user->lastPage(),
                "from" => $user->firstItem(),
                "to" => $user->lastPage(),
            ],
            'usuario' => $userPersona,
            'user' => $user

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
        $usuario = User::with('persona')->find($id);

        // Comprobamos si es un objeto eso quiere decir si exist en la base de datos.
        if (is_object($usuario)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'usuario' => $usuario
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
    public function update(UpdateRequest $request, $id)
    {

        // Validar carnet UNIQUE en una actualización
        $user = User::find($id);
        // echo $user->estado;
        // die();

        if (!empty($user)) {

            // llaves unicas
            $email = $user->email;

            // 1.- Validar datos recogidos por POST. pasando al getIdentity true
            $validate = Validator::make($request->all(), [

                // 4.-Comprobar si el carnet y el email ya existe duplicado
                // 'carnet' => 'required|unique:usuarios',
                'email' => 'required|email',
                'password' => 'required',
                'persona_id' => 'required',
                'estado' => 'required'

            ]);


            // 2.-Recoger los usuarios por post
            $params = (object) $request->all(); // Devuelve un obejto
            $paramsArray = $request->all(); // Es un array

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
                // echo $paramsArray['password'];
                // die();

                // 4.- Quitar los campos que no quiero actualizar de la peticion.
                // unset($paramsArray['id']);
                if ($paramsArray['password'] == 'Ingrese nueva contraseña') {
                    unset($paramsArray['password']);

                    // echo 'hola1';
                } else {
                    // 3.- Cifrar la PASSWORD.
                    // 3.-Cifrar la contraseña
                    $pwd = bcrypt($params->password); // se cifra la contraseña 4 veces
                    $paramsArray['password'] = $pwd;
                    // echo 'hola2';
                }
                if ($email == $paramsArray['email']) {
                    unset($paramsArray['email']);
                }
                // // unset($paramsArray['antiguo']);
                unset($paramsArray['created_at']);
                // unset($paramsArray['updated_at']);



                try {
                    // 5.- Actualizar los datos en la base de datos.
                    User::where('id', $id)->update($paramsArray);

                    // var_dump($user_update);
                    // die();
                    // 6.- Devolver el array con el resultado.
                    $data = array(
                        'status' => 'Succes',
                        'code' => 200,
                        'message' => 'El usuario se ha modificado correctamente',
                        'usuario' => $user,
                        'changes' => $paramsArray
                    );
                } catch (Exception $e) {
                    $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'El nombre de usuario ya esta en uso.',
                        'error' => $user
                    );
                }
            }

            return response()->json($data, $data['code']);
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Este usuario no existe.',
                // 'error' => $e
            );
            return response()->json($data, $data['code']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $usuario = User::find($id); // Trae el usuario en formato JSON

        // echo $user->estado;
        // die();

        if (!empty($usuario)) {
            $paramsArray = json_decode($usuario, true); // devuelve un array
            // var_dump($paramsArray);
            // die();

            // Quitar los campos que no quiero actualizar de la peticion.
            unset($paramsArray['id']);
            unset($paramsArray['persona_id']);
            unset($paramsArray['email']);
            unset($paramsArray['created_at']);
            unset($paramsArray['updated_at']);

            // Campo stado a modificar
            $paramsArray['estado'] = 0;

            try {
                // 5.- Actualizar los datos en la base de datos.
                $user_update = User::where('id', $id)->update($paramsArray);

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
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Este usuario no existe.',
                // 'error' => $e
            );
            return response()->json($data, $data['code']);
        }
    }


    // Metodo para registrar Usuarios
    public function register(RegisterRequest $request)
    {
        // 1.-Recoger los usuarios por post

        $params = (object) $request->all(); // Devulve un obejto

        // 2.-Validar datos
        $validate = Validator::make($request->all(), [
            'persona_id' => 'required',
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
            $user->persona_id = $request->persona_id;
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


            $user = User::with('persona')->where('email', $request->email)->first();

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
                'token' => $token,
                'users' => $user
            );
        }
        // Devuelve en json con laravel
        return response()->json($data, $data['code']);
    }

    // Para cerrar sesion
    public function logout(LogoutRequest $request)
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


    // Buscar Usuario
    public function buscarUsuario(BuscarUsuarioRequest $request)
    {
        $params = (object) $request->all(); // Devuelve un obejto
        $texto = trim($params->textos);

        $resultado = DB::table('users')
            ->join('personas', 'users.persona_id', '=', 'personas.id')
            // ->where('email', 'LIKE', "%$texto%")
            // ->orWhere('estado', 'LIKE', "%$texto%")
            ->select("users.id", "users.created_at", "users.email", "users.estado", "users.persona_id", "personas.nombres", "personas.ap_paterno", "personas.ap_materno")
            ->where('users.estado', 'like', "%$texto%")
            ->orWhere('users.email', 'like', "%$texto%")
            ->orWhere('personas.carnet', 'like', "%$texto%")
            ->orWhere('personas.nombres', 'like', "%$texto%")
            ->orWhere('personas.ap_paterno', 'like', "%$texto%")
            ->orWhere('personas.ap_materno', 'like', "%$texto%")
            ->paginate(5);

        $data = array(
            'status' => 'success',
            'code' => 200,
            'user' => $resultado
        );

        // Devuelve en json con laravel
        return response()->json($data, $data['code']);
    }
}
