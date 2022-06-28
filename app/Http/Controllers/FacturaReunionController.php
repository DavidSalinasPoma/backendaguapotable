<?php

namespace App\Http\Controllers;

use App\Models\DetalleReunion;
use App\Models\FacturaReunion;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacturaReunionController extends Controller
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
        // 1.-Recoger los usuarios por post
        $params = (object) $request->all(); // Devuelve un obejto
        $paramsArray = $request->all(); // Es un array

        // 4.- Quitar los campos que no quiero actualizar de la peticion.
        unset($paramsArray['id']);
        unset($paramsArray['reunion_id']);
        unset($paramsArray['socio_id']);

        // data para modificar reuniones_detalle
        $detalleReunion = array(
            'opcion' => $params->opcion
        );

        try {
            // Transaccion con eloquent
            DB::transaction(function () use ($id, $paramsArray, $params, $detalleReunion) {
                // 2.- Actualizar los datos en la base de datos.
                FacturaReunion::where('id', $id)->update($paramsArray);

                DetalleReunion::where([
                    ['reunion_id', $params->reunion_id],
                    ['socio_id', $params->socio_id],
                ])->update($detalleReunion);
            }, 2);
            // Fin transacción

            // 3.- Devolver el array con el resultado.
            $data = array(
                'status' => 'Succes',
                'code' => 200,
                'message' => 'La opción reunión se modificó correctamente',
                'changes' => $paramsArray
            );
        } catch (Exception $e) {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'No se hizo la modificación',
                'error' => $e
            );
        }
        return response()->json($data, $data['code']);
    }

    // Respuesta a datos segun la data
    public function indexFacturaReunion(Request $request)
    {
        // 2.-Recoger los usuarios por post
        $params = (object) $request->all(); // Devuelve un obejto
        $paramsArray = $request->all(); // Es un array

        // 1.-Persona el la funcion que esta en el Modelo de soscio
        $FacturaReunion = DB::table('factura_reunion')
            ->join('reuniones', 'factura_reunion.reunion_id', '=', 'reuniones.id')
            ->join('facturas', 'factura_reunion.factura_id', '=', 'facturas.id')
            ->join('consumos', 'facturas.consumo_id', '=', 'consumos.id')
            ->join('socios', 'consumos.socio_id', '=', 'socios.id')
            ->join('personas', 'socios.persona_id', '=', 'personas.id')
            ->where('factura_reunion.fecha_reunion', '=', $params->fecha)
            ->select(
                'reuniones.multa',
                'facturas.id AS idFactura',
                'factura_reunion.id AS idFactReunion',
                'factura_reunion.reunion_id AS idReunion',
                'factura_reunion.opcion',
                'factura_reunion.precio',
                'facturas.estado_pago',
                'socios.id AS idSocio',
                "personas.nombres",
                "personas.ap_paterno",
                "personas.ap_materno",
                "personas.carnet",
                "personas.expedito",
            )
            ->orderBy('socios.id', 'DESC')
            ->get();

        $data = array(
            'code' => 200,
            'status' => 'success',
            'facturaReunion' => $FacturaReunion
        );
        return response()->json($data, $data['code']);
    }
}
