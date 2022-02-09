<?php

namespace App\Http\Controllers;

use App\Http\Requests\factura\BuscarFacturaRequest;
use App\Http\Requests\Factura\StoreRequest;
use App\Http\Requests\Factura\UpdateRequest;
use App\Models\Detalle;
use App\Models\Evento;
use App\Models\Factura;
use App\Models\Lista;
use App\Models\Productos;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // 1.-Persona el la funcion que esta en el Modelo de soscio
        $lista = DB::table('facturas')
            ->join('consumos', 'facturas.consumo_id', '=', 'consumos.id')
            ->join('aperturas', 'consumos.apertura_id', '=', 'aperturas.id')
            ->join('socios', 'consumos.socio_id', '=', 'socios.id')
            ->join('barrios', 'socios.barrio_id', '=', 'barrios.id')
            ->join('personas', 'socios.persona_id', '=', 'personas.id')
            ->select(
                "facturas.id AS idFactura",
                "facturas.estado_pago",
                "socios.id AS idSocio",
                "personas.nombres",
                "personas.ap_paterno AS paterno",
                "personas.ap_materno AS materno",
                "personas.carnet",
                "barrios.nombre AS barrio",
                "consumos.mes",
                "consumos.anio",
                "consumos.lecturaAnterior",
                "consumos.LecturaActual",
                "consumos.consumo",
                "consumos.precio AS precioConsumo",
            )
            ->paginate(5);

        $data = array(
            'code' => 200,
            'status' => 'success',
            'factura' => $lista,
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
            'consumo_id' => 'required',
        ]);

        // Comprobar si los datos son validos
        if ($validate->fails()) { // en caso si los datos fallan la validacion
            // La validacion ha fallado
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Los datos enviados no son correctos!',
                'barrio' => $request->all(),
                'errors' => $validate->errors()
            );
        } else {
            // Si la validacion pasa correctamente
            // Crear el objeto usuario para guardar en la base de datos
            $factura = new Factura();
            $factura->consumo_id = $params->consumo_id;

            try {
                // Guardar en la base de datos

                // 5.-Crear el usuario
                $factura->save();

                // Aqui guardando datos de factura detalle

                $producto = Productos::all();

                foreach ($producto as $key => $item) {
                    if ($item->estado == 1) {
                        $detalle = new Detalle();
                        $detalle->factura_id = $factura['id'];
                        $detalle->producto_id = $item->id;
                        $detalle->precio = $item->precio;
                        $detalle->save();

                        $sinLectura = Lista::where('listas.estado', '=', 0)->count();
                        if ($sinLectura == 0) {
                            $cantidad = 0;

                            if ($item->nombre == 'evento' && $item->cantidad > 0) {
                                $cantidad = $item->cantidad - 1;
                                $paramsArray = array(
                                    'cantidad' => $item->cantidad - 1
                                );
                                Productos::where('id', $item->id)->update($paramsArray);
                            }

                            if ($item->nombre == 'evento' && $cantidad == 0) {
                                $arrayParams = array(
                                    'estado' => 0
                                );
                                Productos::where('id', $item->id)->update($arrayParams);
                                Evento::where('id', $item->num_producto)->update($arrayParams);
                            }
                        }
                    }
                }


                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'La factura se ha creado correctamente',
                    'factura'  => $factura
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // 1.-Persona el la funcion que esta en el Modelo de soscio
        $detalle = DB::table('detalles')
            ->join('facturas', 'detalles.factura_id', '=', 'facturas.id')
            ->join('productos', 'detalles.producto_id', '=', 'productos.id')
            ->join('consumos', 'facturas.consumo_id', '=', 'consumos.id')
            ->join('socios', 'consumos.socio_id', '=', 'socios.id')
            ->join('aperturas', 'consumos.apertura_id', '=', 'aperturas.id')
            ->join('personas', 'socios.persona_id', '=', 'personas.id')
            ->join('barrios', 'socios.barrio_id', '=', 'barrios.id')
            ->select(
                "detalles.precio AS precioDetalle",
                "detalles.factura_id AS idFactura",
                "productos.producto",
                "productos.nombre AS categoriaProducto",
                "facturas.retraso",
                "consumos.mes AS periodo",
                "consumos.anio",
                "consumos.lecturaAnterior",
                "consumos.lecturaActual",
                "consumos.consumo",
                "consumos.precio AS precioConsumo",
                "socios.id AS idSocio",
                "personas.carnet",
                "personas.nombres",
                "personas.ap_paterno AS paterno",
                "personas.ap_materno AS materno",
                "personas.direccion",
                "barrios.nombre AS barrio"
            )
            ->where("detalles.factura_id", "=", $id)
            ->get();

        $data = array(
            'code' => 200,
            'status' => 'success',
            'detalle' => $detalle,
        );
        return response()->json($data, $data['code']);
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
    public function update(UpdateRequest $request, $id)
    {
        // 2.-Recoger los usuarios por post
        $params = (object) $request->all(); // Devuelve un obejto
        $paramsArray = $request->all(); // Es un array

        try {
            // 5.- Actualizar los datos en la base de datos.
            Factura::where('id', $id)->update($paramsArray);
            $changes = Factura::find($id);
            // var_dump($user_update);
            // die();
            // 6.- Devolver el array con el resultado.
            $data = array(
                'status' => 'Succes',
                'code' => 200,
                'message' => 'La factura se ha modificado correctamente',
                'changesFactura' => $changes
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
        //
    }

    // Buscar Usuario
    public function buscarFacturas(BuscarFacturaRequest $request)
    {
        $params = (object) $request->all(); // Devuelve un obejto
        $texto = trim($params->textos);


        $resultado = DB::table('facturas')
            ->join('consumos', 'facturas.consumo_id', '=', 'consumos.id')
            ->join('aperturas', 'consumos.apertura_id', '=', 'aperturas.id')
            ->join('socios', 'consumos.socio_id', '=', 'socios.id')
            ->join('barrios', 'socios.barrio_id', '=', 'barrios.id')
            ->join('personas', 'socios.persona_id', '=', 'personas.id')
            ->select(
                "facturas.id AS idFactura",
                "facturas.estado_pago",
                "facturas.retraso",
                "socios.id AS idSocio",
                "personas.nombres",
                "personas.ap_paterno AS paterno",
                "personas.ap_materno AS materno",
                "personas.carnet",
                "barrios.nombre AS barrio",
                "consumos.mes",
                "consumos.anio",
                "consumos.lecturaAnterior",
                "consumos.LecturaActual",
                "consumos.consumo",
                "consumos.precio AS precioConsumo",
                "consumos.estado"
            )
            ->where('personas.carnet', '=', $texto)
            ->orWhere('socios.id', '=', $texto)
            ->paginate(5);

        $data = array(
            'status' => 'success',
            'code' => 200,
            'factura' => $resultado,

        );

        // Devuelve en json con laravel
        return response()->json($data, $data['code']);
    }

    // Cuando solo exite el retraso
    public function retrasoFactura($id)
    {
        // 1.-Persona el la funcion que esta en el Modelo de soscio
        $factura = DB::table('facturas')
            ->join('consumos', 'facturas.consumo_id', '=', 'consumos.id')
            ->join('socios', 'consumos.socio_id', '=', 'socios.id')
            ->join('aperturas', 'consumos.apertura_id', '=', 'aperturas.id')
            ->join('personas', 'socios.persona_id', '=', 'personas.id')
            ->join('barrios', 'socios.barrio_id', '=', 'barrios.id')
            ->select(
                // "*"
                "facturas.id AS idFactura",
                "facturas.retraso",
                "facturas.total_pagado",
                "facturas.fecha_emision",
                "facturas.estado_pago",
                "consumos.mes AS periodo",
                "consumos.anio",
                "consumos.lecturaAnterior",
                "consumos.lecturaActual",
                "consumos.consumo",
                "consumos.precio AS precioConsumo",
                "socios.id AS idSocio",
                "personas.carnet",
                "personas.nombres",
                "personas.ap_paterno AS paterno",
                "personas.ap_materno AS materno",
                "personas.direccion",
                "barrios.nombre AS barrio"
            )
            ->where("facturas.id", "=", $id)
            ->get();

        $data = array(
            'code' => 200,
            'status' => 'success',
            'factura' => $factura,
        );
        return response()->json($data, $data['code']);
    }
}
