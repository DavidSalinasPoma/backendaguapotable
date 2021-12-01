<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Helpers\JwtAuth;
use App\Policies\PersonaPolicy;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PersonaController extends Controller
{

    // Metodo constructor
    public function __construct()
    {
        // Utiliza la autenticacion en toda la clase excepto en los metodos de index y show.
        $this->middleware('api.authM', ['except' => ['index', 'show', 'pruebas']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $persona = Persona::all(); // Saca con el usuario relacionado de la base de datos
        $data = array(
            'code' => 200,
            'status' => 'success',
            'persona' => $persona
        );
        return response()->json($data, $data['code']);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        // 1.- RECIBIR DATOS
        // Recibimos los datos de angular en una variable
        $json = $request->input('json', null);

        // Convertimos los datos en objeto y array
        $params = json_decode($json); // objeto
        $paramsArray = json_decode($json, true); // Array
        // var_dump($paramsArray);
        // die();

        // Validamos si esta vacio
        if (!empty($params) && !empty($paramsArray)) {

            // 2.-VALIDAR DATOS
            $validate = Validator::make($paramsArray, [
                'carnet' => 'required|unique:personas',
                'expedito' => 'required',
                'nombres' => 'required',
                'ap_paterno' => 'required',
                'ap_materno' => 'required',
                'sexo' => 'required',
                'direccion' => 'required',
                'nacimiento' => 'required',
                'estado_civil' => 'required',
            ]);

            // 5.- SI LA VALIDACION FUE CORRECTA
            // Comprobar si los datos son validos
            if ($validate->fails()) { // en caso si los datos fallan la validacion
                // La validacion ha fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El registro no se ha creado',
                    'errors' => $validate->errors()
                );
            } else {

                // CONSEGUIR EL USUARIO IDENTIFICADO->El que hace el registro.
                // $jwtAuth = new JwtAuth();
                // $token = $request->header('token-usuario', null);
                // $user = $jwtAuth->checkToken($token, true); // Devuelve el token decodificado en un objeto.

                // Si la validacion pasa correctamente  
                // Crear el objeto usuario para guardar en la base de datos
                $persona = new Persona();
                $persona->carnet = $paramsArray['carnet'];
                $persona->expedito = $paramsArray['expedito'];
                $persona->nombres = $paramsArray['nombres'];
                $persona->ap_paterno = $paramsArray['ap_paterno'];
                $persona->ap_materno = $paramsArray['ap_materno'];
                $persona->sexo = $paramsArray['sexo'];
                $persona->direccion = $paramsArray['direccion'];
                $persona->nacimiento = $paramsArray['nacimiento'];
                $persona->estado_civil = $paramsArray['estado_civil'];

                // $promocion->usuarios_id = $user->sub;


                // 7.-GUARDAR EN LA BASE DE DATOS
                $persona->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Se ha registrado correctamente.',
                    'persona' => $persona
                );
            }
        } else {
            $data = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos.'
            );
        }
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
        $persona = Persona::find($id);

        // Comprobamos si es un objeto eso quiere decir si exist en la base de datos.
        if (is_object($persona)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'persona' => $persona
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'La persona no existe'
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


        // Validar carnet UNIQUE en una actualización
        $persona = Persona::find($id);
        // dd(Gate::allows('update', $persona));
        echo 'Hola David';

        $this->authorize('update', $persona);

        $carnet = $persona->carnet;


        // 1- Recoger los datos por POST.
        $json = $request->input('json', null);
        // Decodificamos el json que nos llega 
        $params = json_decode($json); // Devulve un obejto
        $paramsArray = json_decode($json, true); // devuelve un array


        if (!empty($paramsArray)) {


            // Actualizar Usuario.
            // 2.- Validar datos recogidos por POST. pasando al getIdentity true
            $validate = Validator::make($paramsArray, [
                // Validar lo que se va actualizar
                'carnet' => 'required',
                'expedito' => 'required',
                'nombres' => 'required',
                'ap_paterno' => 'required',
                'ap_materno' => 'required',
                'sexo' => 'required',
                'direccion' => 'required',
                'nacimiento' => 'required',
                'estado_civil' => 'required',
                'estado' => 'required',

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
                // echo $carnet;
                // echo $paramsArray['carnet'];
                // die();
                if ($carnet == $paramsArray['carnet']) {
                    unset($paramsArray['carnet']);
                }

                // 4.- Quitar los campos que no quiero actualizar de la peticion.
                // unset($paramsArray['id']);
                // unset($paramsArray['password']);
                // // unset($paramsArray['antiguo']);
                unset($paramsArray['created_at']);
                // unset($paramsArray['updated_at']);

                try {
                    // 5.- Actualizar los datos en la base de datos.
                    Persona::where('id', $id)->update($paramsArray);

                    // 6.- Devolver el array con el resultado.
                    $data = array(
                        'status' => 'Succes',
                        'code' => 200,
                        'message' => 'La persona se ha modificado correctamente',
                        'persona' => $persona,
                        'changes' => $paramsArray
                    );
                } catch (Exception $e) {
                    $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'No se hizo la modificación, Este registro con numero de carnet ya existe',
                        'error' => $e
                    );
                }
            }
        } else {
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'No hay datos para modificar.',
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
        $persona = Persona::find($id); // Trae el usuario en formato JSON

        $paramsArray = array(
            'estado' => 0
        );

        try {
            // 5.- Actualizar los datos en la base de datos.
            Persona::where('id', $id)->update($paramsArray);

            // 6.- Devolver el array con el resultado.
            $data = array(
                'status' => 'Succes',
                'code' => 200,
                'message' => 'La persona ha sido dado de baja correctamente',
                'persona' => $persona,
                'changes' => $paramsArray
            );
        } catch (Exception $e) {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'La persona no ha sido dado de baja',
                'error' => $e

            );
        }

        return response()->json($data, $data['code']);
    }

    // Pruebas de este controlador
    public function pruebas(Request $request)
    {
        return "Acción de pruebas de PERSONA-CONTROLLER";
    }
}
