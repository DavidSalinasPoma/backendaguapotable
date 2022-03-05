<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Helpers\JwtAuth;
use App\Http\Requests\Persona\BuscarPersonaRequest;
use App\Http\Requests\Persona\StoreRequest;
use App\Http\Requests\Persona\UpdateRequest;
use App\Policies\PersonaPolicy;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PersonaController extends Controller
{

    // Metodo constructor
    public function __construct()
    {
        // Utiliza la autenticacion en toda la clase excepto en los metodos de index y show.
        // $this->middleware('api.authM', ['except' => ['index', 'show', 'pruebas']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $persona = Persona::where('socio', '=', '0')
            ->orderBy('id', 'DESC')->paginate(5);

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

    public function store(StoreRequest $request)
    {

        // 1.-VALIDAR DATOS
        $validate = Validator::make($request->all(), [
            'carnet' => 'required|unique:personas',
            'expedito' => 'required',
            'nombres' => 'required',
            'ap_paterno' => 'required',
            'sexo' => 'required',
            'direccion' => 'required',
            'email' => 'required|email',
            'celular' => 'required',
            'celular_familiar' => 'required',
            'nacimiento' => 'required',
            'estado_civil' => 'required',
        ]);

        // 2.-Recoger los usuarios por post
        $params = (object) $request->all(); // Devuelve un obejto

        // 3.- SI LA VALIDACION FUE CORRECTA
        // Comprobar si los datos son validos
        if ($validate->fails()) { // en caso si los datos fallan la validacion
            // La validacion ha fallado
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Los datos enviados no son correctos.',
                'errors' => $validate->errors()
            );
        } else {


            $persona = new Persona();
            $persona->carnet = $params->carnet;
            $persona->expedito = $params->expedito;
            $persona->nombres = $params->nombres;
            $persona->ap_paterno = $params->ap_paterno;
            $persona->ap_materno = $params->ap_materno;
            $persona->sexo = $params->sexo;
            $persona->direccion = $params->direccion;
            $persona->email = $params->email;
            $persona->celular = $params->celular;
            $persona->celular_familiar = $params->celular_familiar;
            $persona->nacimiento = $params->nacimiento;
            $persona->estado_civil = $params->estado_civil;

            // $promocion->usuarios_id = $user->sub;

            try {
                // 7.-GUARDAR EN LA BASE DE DATOS
                $persona->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Se ha registrado correctamente.',
                    'persona' => $persona
                );
            } catch (Exception $e) {
                $data = array(
                    'status' => 'err',
                    'code' => 400,
                    'message' => 'La persona no se pudo registrar intente de nuevo.',
                    'error' => $e
                );
            }
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
    public function update(UpdateRequest $request, $id)
    {


        // Validar carnet UNIQUE en una actualización
        $persona = Persona::find($id);


        if (!empty($persona)) {

            // llaves unicas
            $carnet = $persona->carnet;

            // Actualizar Usuario.
            // 1.- Validar datos recogidos por POST. pasando al getIdentity true
            $validate = Validator::make($request->all(), [
                // Validar lo que se va actualizar
                'carnet' => 'required',
                'expedito' => 'required',
                'nombres' => 'required',
                'ap_paterno' => 'required',
                'sexo' => 'required',
                'direccion' => 'required',
                'email' => 'required|email',
                'celular' => 'required',
                'celular_familiar' => 'required',
                'nacimiento' => 'required',
                'estado_civil' => 'required',
                'estado' => 'required',

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


            return response()->json($data, $data['code']);
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Esta persona no existe.',
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

    // Buscar Usuario
    public function buscarPersonas(BuscarPersonaRequest $request)
    {
        $params = (object) $request->all(); // Devuelve un obejto
        $texto = trim($params->textos);

        $resultado = DB::table('personas')
            ->select("personas.id", "personas.nombres", "personas.carnet", "personas.ap_paterno", "personas.ap_materno", "personas.celular", "personas.estado", "personas.socio")
            ->where('personas.id', 'like', "%$texto%")
            ->orWhere('personas.carnet', 'like', "%$texto%")
            ->orWhere('personas.nombres', 'like', "%$texto%")
            ->orWhere('personas.ap_paterno', 'like', "%$texto%")
            ->orWhere('personas.ap_materno', 'like', "%$texto%")
            ->orWhere('personas.celular', 'like', "%$texto%")
            ->paginate(5);

        $data = array(
            'status' => 'success',
            'code' => 200,
            'persona' => $resultado
        );

        // Devuelve en json con laravel
        return response()->json($data, $data['code']);
    }

    public function buscarPersonaUsuario()
    {

        $persona = Persona::where('estado', '=', '1')
            ->orderBy('id', 'DESC')->paginate(5);

        $data = array(
            'code' => 200,
            'status' => 'success',
            'persona' => $persona
        );
        return response()->json($data, $data['code']);
    }

    public function indexPersonas()
    {

        $persona = Persona::orderBy('id', 'DESC')->paginate(5);

        $data = array(
            'code' => 200,
            'status' => 'success',
            'persona' => $persona
        );
        return response()->json($data, $data['code']);
    }
}
