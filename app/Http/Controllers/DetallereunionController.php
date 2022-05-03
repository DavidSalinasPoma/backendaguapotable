<?php

namespace App\Http\Controllers;

use App\Http\Requests\detalleReunion\UpdateListaDetalleRequest;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\detalleReunion\UpdateRequest;
use App\Models\Detallereunion;
use App\Models\Reunion;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetallereunionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
        // 1.-Persona el la funcion que esta en el Modelo de soscio
        $detalle = DB::table('reunion_detalles')
            ->join('socios', 'reunion_detalles.socio_id', '=', 'socios.id')
            ->join('reuniones', 'reunion_detalles.reunion_id', '=', 'reuniones.id')
            ->join('personas', 'socios.persona_id', '=', 'personas.id')
            ->select(
                "socios.id",
                "reunion_detalles.id AS detalle_id",
                "reunion_detalles.opcion",
                "personas.nombres",
                "personas.ap_paterno AS paterno",
                "personas.ap_materno AS materno",
                "personas.carnet",
                "personas.expedito",
                "reuniones.reunion",
                "reuniones.multa",
                "reuniones.fecha",
            )
            ->where('reunion_detalles.reunion_id', '=', $id)
            ->where('reunion_detalles.estado', '=', 0)
            ->orderBy('id', 'ASC')
            ->paginate(1);

        $data = array(
            'code' => 200,
            'status' => 'success',
            'detalle' => $detalle
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


        // 1.- Validar datos recogidos por POST. pasando al getIdentity true
        $validate = Validator::make($request->all(), [

            'opcion' => 'required',
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

            // 4.- Quitar los campos que no quiero actualizar de la peticion.
            // unset($paramsArray['created_at']);
            // unset($paramsArray['updated_at']);

            try {
                // 5.- Actualizar los datos en la base de datos.
                DB::table('reunion_detalles')
                    ->where('id', $id)
                    ->update($paramsArray);

                // var_dump($user_update);
                // die();
                // 6.- Devolver el array con el resultado.
                $data = array(
                    'status' => 'Succes',
                    'code' => 200,
                    'message' => 'El detalle de reunion se ha modificado correctamente',
                    'changes' => $paramsArray
                );
            } catch (Exception $e) {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'No se ha modificado.',
                    'error' => $e
                );
            }
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
        //
    }

    public function indexListaDetalle($id)
    {
        // 1.-Persona el la funcion que esta en el Modelo de soscio
        $detalle = DB::table('reunion_detalles')
            ->join('socios', 'reunion_detalles.socio_id', '=', 'socios.id')
            ->join('reuniones', 'reunion_detalles.reunion_id', '=', 'reuniones.id')
            ->join('personas', 'socios.persona_id', '=', 'personas.id')
            ->select(
                "socios.id",
                "socios.estado",
                "reunion_detalles.id AS detalle_id",
                "reunion_detalles.opcion",
                "reunion_detalles.estado",
                "personas.nombres",
                "personas.ap_paterno AS paterno",
                "personas.ap_materno AS materno",
                "personas.carnet",
                "personas.expedito",
                "reuniones.reunion",
                "reuniones.multa",
                "reuniones.fecha",
            )
            ->where('reunion_detalles.reunion_id', '=', $id)
            ->where('reunion_detalles.estado', '=', 1)
            ->where('socios.estado', '=', 1)
            ->orderBy('id', 'DESC')
            ->get();

        $data = array(
            'code' => 200,
            'status' => 'success',
            'detalle' => $detalle
        );
        return response()->json($data, $data['code']);
    }

    // Actualizar el estado de la lista reunion
    public function updateListaDetalle(UpdateListaDetalleRequest $request, $id)
    {
        // 2.-Recoger los usuarios por post
        $params = (object) $request->all(); // Devuelve un obejto
        $paramsArray = $request->all(); // Es un array

        // 4.- Quitar los campos que no quiero actualizar de la peticion.
        unset($paramsArray['created_at']);
        // unset($paramsArray['updated_at']);

        try {
            // 5.- Actualizar los datos en la base de datos.
            Reunion::where('id', $id)->update($paramsArray);
            // var_dump($user_update);
            // die();
            // 6.- Devolver el array con el resultado.
            $data = array(
                'status' => 'Succes',
                'code' => 200,
                'message' => 'El estado de reunion se ha modificado correctamente',
                'changes' => $paramsArray
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
}
