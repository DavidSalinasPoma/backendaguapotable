<?php

namespace App\Http\Controllers;

use App\Http\Requests\socio\BuscarSocioRequest;
use App\Http\Requests\socio\StoreRequest;
use App\Http\Requests\socio\UpdateRequest;
use App\Models\Persona;
use App\Models\Socio;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SocioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // 1.-Persona el la funcion que esta en el Modelo de soscio
        $socio = Socio::with('persona', 'barrio')->orderBy('id', 'DESC')->paginate(5);

        $data = array(
            'code' => 200,
            'status' => 'success',
            'socio' => $socio
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
        // 1.-Recoger los usuarios por post

        $params = (object) $request->all(); // Devulve un obejto

        // 2.-Validar datos
        $validate = Validator::make($request->all(), [
            'persona_id' => 'required',
            'barrio_id' => 'required',
            'directivo' => 'required',
        ]);

        // Comprobar si los datos son validos
        if ($validate->fails()) { // en caso si los datos fallan la validacion
            // La validacion ha fallado
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Los datos enviados no son correctos',
                'socio' => $request->all(),
                'errors' => $validate->errors()
            );
        } else {
            // Si la validacion pasa correctamente
            // Crear el objeto usuario para guardar en la base de datos
            $socio = new Socio();
            $socio->persona_id = $params->persona_id;
            $socio->barrio_id = $params->barrio_id;
            $socio->directivo = $params->directivo;

            try {
                // Guardar en la base de datos

                // 5.-Crear el usuario
                $socio->save();
                $paramsArray = array(
                    'socio' => 1
                );
                // 5.- Actualizar los datos de la persona que ya es socio
                Persona::where('id', $params->persona_id)->update($paramsArray);

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El Socio se ha creado correctamente',
                    'socio' => $socio
                );
            } catch (Exception $e) {
                $data = array(
                    'status' => 'err',
                    'code' => 400,
                    'message' => 'No se pudo guardar, intente nuevamente',
                    'error' => $e
                );
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // 1.-Persona el la funcion que esta en el Modelo de soscio
        $lista = DB::table('listas')
            ->join('socios', 'listas.socio_id', '=', 'socios.id')
            ->join('aperturas', 'listas.apertura_id', '=', 'aperturas.id')
            ->join('personas', 'socios.persona_id', '=', 'personas.id')
            ->join('barrios', 'socios.barrio_id', '=', 'barrios.id')
            ->select(
                "socios.id",
                "socios.directivo",
                "personas.nombres",
                "personas.ap_paterno",
                "personas.ap_materno",
                "personas.carnet",
                "barrios.nombre",
                "aperturas.mes",
                "listas.estado",
                "listas.directivo AS directivoLista",
                "aperturas.id AS apertura",
                "listas.id AS lista"
            )
            ->where('socios.id', '=', $id)
            ->paginate(10);

        $data = array(
            'code' => 200,
            'status' => 'success',
            'socio' => $lista,
        );
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
        $socio = Socio::find($id);
        // echo $user->estado;
        // die();

        if (!empty($socio)) {

            // 1.- Validar datos recogidos por POST. pasando al getIdentity true
            $validate = Validator::make($request->all(), [

                'persona_id' => 'required',
                'barrio_id' => 'required',
                'estado' => 'required',
                'directivo' => 'required',
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

                // 4.- Quitar los campos que no quiero actualizar de la peticion.

                unset($paramsArray['created_at']);
                // unset($paramsArray['updated_at']);
                try {
                    // 5.- Actualizar los datos en la base de datos.
                    Socio::where('id', $id)->update($paramsArray);

                    // var_dump($user_update);
                    // die();
                    // 6.- Devolver el array con el resultado.
                    $data = array(
                        'status' => 'Succes',
                        'code' => 200,
                        'message' => 'El Socio se ha modificado correctamente',
                        'socio' => $socio,
                        'changes' => $paramsArray
                    );
                } catch (Exception $e) {
                    $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'No se ha modificado.',
                        // 'error' => $e
                    );
                }
            }

            return response()->json($data, $data['code']);
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Este socio no existe.',
                // 'error' => $e
            );
            return response()->json($data, $data['code']);
        }
    }

    public function deleteSocioDirectorio($id)
    {
        $paramsArray = array(
            'directivo' => 0
        );

        try {
            // Actualizar los datos en la base de datos.
            Socio::where('id', $id)->update($paramsArray);
            $data = array(
                'status' => 'Succes',
                'code' => 200,
                'message' => 'El Socio se ha modificado correctamente'
            );
        } catch (Exception $e) {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'No se ha modificado.',
                'error' => $e
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
        $socio = Socio::find($id); // Trae el usuario en formato JSON

        // echo $user->estado;
        // die();

        if (!empty($socio)) {
            $paramsArray = json_decode($socio, true); // devuelve un array
            // var_dump($paramsArray);
            // die();

            // Quitar los campos que no quiero actualizar de la peticion.
            unset($paramsArray['persona_id']);
            unset($paramsArray['created_at']);
            unset($paramsArray['updated_at']);

            // Campo stado a modificar
            $paramsArray['estado'] = 0;

            try {
                // 5.- Actualizar los datos en la base de datos.
                Socio::where('id', $id)->update($paramsArray);

                // 6.- Devolver el array con el resultado.
                $data = array(
                    'status' => 'Succes',
                    'code' => 200,
                    'message' => 'El socio ha sido dado de baja correctamente',
                    'socio' => $socio,
                    'changes' => $paramsArray
                );
            } catch (Exception $e) {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El socio no ha sido dado de baja',
                    'error' => $e

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

    // Buscar Usuario
    public function buscarSocios(BuscarSocioRequest $request)
    {
        $params = (object) $request->all(); // Devuelve un obejto
        $texto = trim($params->textos);

        $resultado = DB::table('socios')
            ->join('personas', 'socios.persona_id', '=', 'personas.id')
            ->join('barrios', 'socios.barrio_id', '=', 'barrios.id')
            // ->where('email', 'LIKE', "%$texto%")
            // ->orWhere('estado', 'LIKE', "%$texto%")
            ->select(
                "socios.id",
                "personas.carnet",
                "personas.expedito",
                "personas.nombres",
                "personas.ap_paterno",
                "personas.ap_materno",
                "socios.estado",
                "socios.directivo",
                "barrios.nombre"
            )
            ->where("personas.carnet", "=", $texto)
            ->orWhere("personas.nombres", "like", "%$texto%")
            ->orWhere("socios.id", "=", $texto)
            ->orWhere("personas.ap_paterno", "like", "%$texto%")
            ->orWhere("personas.ap_materno", "like", "%$texto%")
            ->orWhere("barrios.nombre", "like", "%$texto%")
            ->paginate(5);

        $data = array(
            'status' => 'success',
            'code' => 200,
            'socio' => $resultado
        );

        // Devuelve en json con laravel
        return response()->json($data, $data['code']);
    }

    public function showSocios($id)
    {

        try {
            $socio = Socio::with('persona', 'barrio')->find($id);
            $data = array(
                'status' => 'success',
                'code' => 200,
                'socio' => $socio
            );
        } catch (Exception $e) {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'error' => $e
            );

            // Devuelve en json con laravel
        }
        return response()->json($data, $data['code']);
    }

    public function reporteDirectorio()
    {
        // 1.-Persona el la funcion que esta en el Modelo de soscio
        $directivos = DB::table('socios')
            ->join('personas', 'socios.persona_id', '=', 'personas.id')
            ->join('barrios', 'socios.barrio_id', '=', 'barrios.id')
            // ->where('email', 'LIKE', "%$texto%")
            // ->orWhere('estado', 'LIKE', "%$texto%")
            ->select(
                "socios.id",
                "personas.carnet",
                "personas.expedito",
                "personas.nombres",
                "personas.ap_paterno AS paterno",
                "personas.ap_materno AS materno",
                "socios.estado",
                "socios.directivo",
                "barrios.nombre AS barrio"
            )
            ->where("socios.directivo", "=", 1)
            ->where("socios.estado", "=", 1)
            ->get();

        $data = array(
            'code' => 200,
            'status' => 'success',
            'directivos' => $directivos
        );
        return response()->json($data, $data['code']);
    }

    // Reportes 

    // Reportes socios por barrios
    public function socioporBarrio($id)
    {
        // 1.-Persona el la funcion que esta en el Modelo de soscio
        $socio = DB::table('socios')
            ->join('personas', 'socios.persona_id', '=', 'personas.id')
            ->join('barrios', 'socios.barrio_id', '=', 'barrios.id')
            // ->where('email', 'LIKE', "%$texto%")
            // ->orWhere('estado', 'LIKE', "%$texto%")
            ->select(
                "socios.id",
                "personas.carnet",
                "personas.expedito",
                "personas.nombres",
                "personas.ap_paterno AS paterno",
                "personas.ap_materno AS materno",
                "socios.estado",
                "socios.directivo",
                "barrios.nombre AS barrio"
            )
            ->where("barrios.id", "=", $id)
            ->orderBy('id', 'ASC')
            ->get();

        $data = array(
            'code' => 200,
            'status' => 'success',
            'socio' => $socio
        );
        return response()->json($data, $data['code']);
    }
}
