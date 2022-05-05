<?php

namespace App\Http\Controllers;

use App\Http\Requests\factura\BuscarFacturaRequest;
use App\Http\Requests\Factura\StoreRequest;
use App\Http\Requests\Factura\UpdateRequest;
use App\Models\Detalle;
use App\Models\DetalleReunion;
use App\Models\Evento;
use App\Models\Factura;
use App\Models\FacturaReunion;
use App\Models\Lista;
use App\Models\Productos;
use App\Models\Reunion;
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
            'precio' => 'required',
            'directivo' => 'required',
            'socio_id' => 'required',
        ]);

        // Comprobar si los datos son validos
        if ($validate->fails()) { // en caso si los datos fallan la validacion
            // La validacion ha fallado
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Los datos enviados no son correctos En factura!',
                'barrio' => $request->all(),
                'errors' => $validate->errors()
            );
        } else {
            // Si la validacion pasa correctamente
            // Logica para directivos
            if ($params->directivo && $params->precio <= 20) {


                // Guarda directo en factura y no en detalle
                $factura = new Factura();
                $factura->consumo_id = $params->consumo_id;
                $factura->total_pagado = $params->precio;
                $fecha = Carbon::now();
                $date = Carbon::parse($fecha);
                $fecha = $fecha->toDateString();

                switch ($date->month) {
                    case 1:
                        $mes = 'enero';
                        break;
                    case 2:
                        $mes = 'febrero';
                        break;
                    case 3:
                        $mes = 'marzo';
                        break;
                    case 4:
                        $mes = 'abril';
                        break;
                    case 5:
                        $mes = 'mayo';
                        break;
                    case 6:
                        $mes = 'junio';
                        break;
                    case 7:
                        $mes = 'julio';
                        break;
                    case 8:
                        $mes = 'agosto';
                        break;
                    case 9:
                        $mes = 'septiembre';
                        break;
                    case 10:
                        $mes = 'octubre';
                        break;
                    case 11:
                        $mes = 'noviembre';
                        break;
                    case 12:
                        $mes = 'diciembre';
                        break;
                }

                $factura->mes_pago = $mes;
                $factura->anio_pago = $date->year;

                $factura->fecha_emision = $fecha;
                $factura->estado_pago = 1;
                $factura->directivo_especial = 'si';
                try {

                    $factura->save();


                    // Logica para guardar facturaDetalle
                    $listaReunion = Reunion::all();
                    foreach ($listaReunion as $key => $item) {
                        if ($item->estado == 1 && $item->estado_consumo == 0) {
                            $socioMulta = DetalleReunion::with('reunion')
                                ->where("socio_id", "=", $params->socio_id)
                                ->where("reunion_id", "=", $item->id)
                                ->first();

                            switch ($socioMulta->opcion) {
                                case 'si':
                                    // Guardar en factura reunion
                                    $facturaReunion = new FacturaReunion();
                                    $facturaReunion->factura_id = $factura['id'];
                                    $facturaReunion->reunion_id = $socioMulta->reunion_id;
                                    $facturaReunion->opcion = $socioMulta->opcion;
                                    $facturaReunion->precio = 0;
                                    $facturaReunion->reunion = $socioMulta->reunion->reunion;
                                    $facturaReunion->fecha_reunion = $socioMulta->reunion->fecha;
                                    $facturaReunion->save();
                                    break;
                                case 'no':
                                    // Guardar en factura reunion
                                    $facturaReunion = new FacturaReunion();
                                    $facturaReunion->factura_id = $factura['id'];
                                    $facturaReunion->reunion_id = $socioMulta->reunion_id;
                                    $facturaReunion->opcion = $socioMulta->opcion;
                                    $facturaReunion->precio = $socioMulta->reunion->multa;
                                    $facturaReunion->reunion = $socioMulta->reunion->reunion;
                                    $facturaReunion->fecha_reunion = $socioMulta->reunion->fecha;
                                    $facturaReunion->save();
                                    break;
                                case 'retraso':
                                    // Guardar en factura reunion
                                    $facturaReunion = new FacturaReunion();
                                    $facturaReunion->factura_id = $factura['id'];
                                    $facturaReunion->reunion_id = $socioMulta->reunion_id;
                                    $facturaReunion->opcion = $socioMulta->opcion;
                                    $facturaReunion->precio = 5;
                                    $facturaReunion->reunion = $socioMulta->reunion->reunion;
                                    $facturaReunion->fecha_reunion = $socioMulta->reunion->fecha;
                                    $facturaReunion->save();
                                    break;
                            }
                        }
                    }
                    // Fin Logica para guardar facturaDetalle



                    $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'La factura se ha creado correctamente',
                        'factura'  => $factura,
                        'socioMulta' => $socioMulta,
                        // 'opcion' => $socioMulta->opcion,
                    );
                } catch (Exception $e) {
                    $data = array(
                        'status' => 'Error',
                        'code' => 404,
                        'message' => $e
                    );
                }
            } else {

                // Crear el objeto usuario para guardar en la base de datos
                $factura = new Factura();
                $factura->consumo_id = $params->consumo_id;

                try {
                    // Guardar en la base de datos

                    // Guarda en factura y en detalle los directivos mayores a 20
                    $factura->save();


                    // Logica para guardar facturaDetalle
                    $listaReunion = Reunion::all();
                    foreach ($listaReunion as $key => $item) {
                        if ($item->estado == 1 && $item->estado_consumo == 0) {
                            $socioMulta = DetalleReunion::with('reunion')
                                ->where("socio_id", "=", $params->socio_id)
                                ->where("reunion_id", "=", $item->id)
                                ->first();

                            switch ($socioMulta->opcion) {
                                case 'si':
                                    // Guardar en factura reunion
                                    $facturaReunion = new FacturaReunion();
                                    $facturaReunion->factura_id = $factura['id'];
                                    $facturaReunion->reunion_id = $socioMulta->reunion_id;
                                    $facturaReunion->opcion = $socioMulta->opcion;
                                    $facturaReunion->precio = 0;
                                    $facturaReunion->reunion = $socioMulta->reunion->reunion;
                                    $facturaReunion->fecha_reunion = $socioMulta->reunion->fecha;
                                    $facturaReunion->save();
                                    break;
                                case 'no':
                                    // Guardar en factura reunion
                                    $facturaReunion = new FacturaReunion();
                                    $facturaReunion->factura_id = $factura['id'];
                                    $facturaReunion->reunion_id = $socioMulta->reunion_id;
                                    $facturaReunion->opcion = $socioMulta->opcion;
                                    $facturaReunion->precio = $socioMulta->reunion->multa;
                                    $facturaReunion->reunion = $socioMulta->reunion->reunion;
                                    $facturaReunion->fecha_reunion = $socioMulta->reunion->fecha;
                                    $facturaReunion->save();
                                    break;
                                case 'retraso':
                                    // Guardar en factura reunion
                                    $facturaReunion = new FacturaReunion();
                                    $facturaReunion->factura_id = $factura['id'];
                                    $facturaReunion->reunion_id = $socioMulta->reunion_id;
                                    $facturaReunion->opcion = $socioMulta->opcion;
                                    $facturaReunion->precio = 5;
                                    $facturaReunion->reunion = $socioMulta->reunion->reunion;
                                    $facturaReunion->fecha_reunion = $socioMulta->reunion->fecha;
                                    $facturaReunion->save();
                                    break;
                            }
                        }
                    }
                    // Fin Logica para guardar facturaDetalle



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
                        'factura'  => $factura,
                        'socioMulta' => $socioMulta,
                        // 'opcion' => $socioMulta->opcion,
                    );
                } catch (Exception $e) {
                    $data = array(
                        'status' => 'Error',
                        'code' => 404,
                        'message' => $e,
                        'toto' => $listaReunion
                    );
                }
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
                "facturas.fecha_emision",
                "consumos.mes AS periodo",
                "consumos.anio",
                "consumos.lecturaAnterior",
                "consumos.lecturaActual",
                "consumos.consumo",
                "consumos.precio AS precioConsumo",
                "consumos.directivo",
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

    public function showFactura($id)
    {
        // 1.-Persona el la funcion que esta en el Modelo de soscio
        $factura = DB::table('facturas')
            ->join('consumos', 'facturas.consumo_id', '=', 'consumos.id')
            ->join('socios', 'consumos.socio_id', '=', 'socios.id')
            ->join('personas', 'socios.persona_id', '=', 'personas.id')
            ->join('barrios', 'socios.barrio_id', '=', 'barrios.id')
            ->select(
                "facturas.id AS idFactura",
                "facturas.fecha_emision",
                "facturas.total_pagado",
                "consumos.mes AS periodo",
                "consumos.anio",
                "consumos.lecturaAnterior",
                "consumos.lecturaActual",
                "consumos.consumo",
                "consumos.precio AS precioConsumo",
                "consumos.directivo",
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
    public function buscarFacturas(Request $request)
    {
        $params = (object) $request->all(); // Devuelve un obejto
        $texto = $params->textos;

        try {
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
                    "facturas.fecha_emision",
                    "facturas.total_pagado",
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
                    "consumos.estado",
                    "consumos.directivo"
                )
                // ->where('personas.carnet', '=', $texto)
                ->orWhere('socios.id', '=', $texto)
                ->orderBy('facturas.id', 'DESC')
                ->paginate(5);

            $data = array(
                'status' => 'success',
                'code' => 200,
                'factura' => $resultado,
                'texto' => $texto
            );
        } catch (Exception $e) {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'No puede buscar',
                'error' => $e,
            );
        }
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
                "consumos.directivo",
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

    public function pruebasFacturas(Request $request)
    {
        $params = (object) $request->all(); // Devuelve un obejto
        $texto = $params->textos;
        $data = array(
            'status' => 'success',
            'code' => 200,
            // 'factura' => $params,
        );
        return response()->json($data, $data['code']);
    }

    // para sacar el reporte de facturasReuniones
    public function showFacturaReunion($id)
    {
        // 1.-Persona el la funcion que esta en el Modelo de soscio
        $facturaReunion = DB::table('factura_reunion')
            ->join('facturas', 'factura_reunion.factura_id', '=', 'facturas.id')
            ->join('reuniones', 'factura_reunion.reunion_id', '=', 'reuniones.id')
            ->select(
                "factura_reunion.precio",
                "factura_reunion.reunion",
                "factura_reunion.fecha_reunion"
            )
            ->where("factura_reunion.factura_id", "=", $id)
            ->get();

        $data = array(
            'code' => 200,
            'status' => 'success',
            'facturaReunion' => $facturaReunion,
        );
        return response()->json($data, $data['code']);
    }
}
