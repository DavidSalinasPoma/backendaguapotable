<?php

namespace App\Http\Controllers;

use App\Http\Requests\reportes\CobroxMesRequest;
use App\Models\Detalle;
use App\Models\Productos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
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

    // Reportes del total de ganancias
    public function cobroxMes(CobroxMesRequest $request)
    {

        // 1.-Recoger los usuarios por post
        $params = (object) $request->all(); // Devulve un obejto


        // Consulta nro:1 solo consumo de todos en general
        $consumoTotal = DB::table('facturas')
            ->join(
                'consumos',
                'facturas.consumo_id',
                '=',
                'consumos.id'
            )
            ->select(
                "facturas.mes_pago",
                DB::raw('SUM(consumos.precio) as consumoTotal'),
            )
            ->where('facturas.mes_pago', '=', $params->mes)
            ->where('facturas.anio_pago', '=', $params->anio)
            ->where('facturas.estado_pago', '=', 1)
            ->groupBy('facturas.mes_pago')
            ->get();


        // Agrupacion  de productos por mes de cobro
        $details = DB::table('detalles')
            ->join('facturas', 'detalles.factura_id', '=', 'facturas.id')
            ->join('productos', 'detalles.producto_id', '=', 'productos.id')
            ->join('consumos', 'facturas.consumo_id', '=', 'consumos.id')
            ->join('socios', 'consumos.socio_id', '=', 'socios.id')
            ->join('aperturas', 'consumos.apertura_id', '=', 'aperturas.id')
            ->join('personas', 'socios.persona_id', '=', 'personas.id')
            ->join('barrios', 'socios.barrio_id', '=', 'barrios.id')

            ->select(
                "productos.producto",
                DB::raw('SUM(productos.precio) as sumaProducto_total'),
            )
            ->where('facturas.mes_pago', '=', $params->mes)
            ->where('facturas.anio_pago', '=', $params->anio)
            ->groupBy('productos.producto')
            ->get();


        // Agrupacion del cobro total de la factura por mes
        $facturas = DB::table('facturas')
            ->join('consumos', 'facturas.consumo_id', '=', 'consumos.id')
            ->select(
                "facturas.mes_pago",
                DB::raw('SUM(consumos.precio) as sumaFacturas_total'),
            )
            ->where('facturas.mes_pago', '=', $params->mes)
            ->where('facturas.anio_pago', '=', $params->anio)
            ->where('facturas.estado_pago', '=', 1)
            ->groupBy('facturas.mes_pago')
            ->get();


        // Agrupacion del cobro total de la factura por mes
        $facturaTotal = DB::table('facturas')
            ->join(
                'consumos',
                'facturas.consumo_id',
                '=',
                'consumos.id'
            )
            ->select(
                "facturas.mes_pago",
                DB::raw('SUM(facturas.total_pagado) as sumaFacturas_total'),
            )
            ->where('facturas.mes_pago', '=', $params->mes)
            ->where('facturas.anio_pago', '=', $params->anio)
            ->where('facturas.estado_pago', '=', 1)
            ->groupBy('facturas.mes_pago')
            ->get();

        // Agrupación del cobro total de los directivos menores a 20BS
        $directivos = DB::table('facturas')
            ->join('consumos', 'facturas.consumo_id', '=', 'consumos.id')
            ->select(
                "consumos.mes",
                DB::raw('SUM(facturas.total_pagado) as sumaFacturasDirectivos_total'),
            )
            ->where('facturas.mes_pago', '=', $params->mes)
            ->where('facturas.anio_pago', '=', $params->anio)
            ->where('facturas.estado_pago', '=', 1)
            ->where('consumos.precio', '<=', 20)
            ->where('consumos.directivo', '=', 1)
            ->groupBy('consumos.mes')
            ->get();


        // Agrupación del cobro total de los directivos menores a 20BS
        $multasRetrasos = DB::table('facturas')
            ->select(
                "facturas.mes_pago",
                DB::raw('SUM(facturas.retraso) as sumaFacturasRetrasos_total'),
            )
            ->where('facturas.mes_pago', '=', $params->mes)
            ->where('facturas.anio_pago', '=', $params->anio)
            ->where('facturas.estado_pago', '=', 1)
            ->where('facturas.retraso', '=', 5)
            ->groupBy('facturas.mes_pago')
            ->get();


        // Reporte de socios que tienen multa de reuniones
        $multaReunion = DB::table('factura_reunion')
            ->join('facturas', 'factura_reunion.factura_id', '=', 'facturas.id')
            ->select(
                "facturas.mes_pago",
                DB::raw('SUM(factura_reunion.precio) as sumaReunion'),
            )
            ->where('facturas.mes_pago', '=', $params->mes)
            ->where('facturas.anio_pago', '=', $params->anio)
            ->where('facturas.estado_pago', '=', 1)
            ->groupBy('facturas.mes_pago')
            ->get();

        // echo count($directivos);
        // Validar cuando sea cero
        $sumaFinal = 0;
        if (count($facturas) == 0) {
            $sumaFinal = 0;
        } else if (count($directivos) == 0 && count($multasRetrasos) != 0 && count($details) != 0) {

            $sumaFinal = $facturas[0]->sumaFacturas_total - 0 - $multasRetrasos[0]->sumaFacturasRetrasos_total;
            foreach ($details as $key => $value) {
                $sumaFinal = $sumaFinal - $value->sumaProducto_total;
            }
        } else if (count($directivos) != 0 && count($multasRetrasos) == 0 && count($details) != 0) {

            $sumaFinal = $facturas[0]->sumaFacturas_total - $directivos[0]->sumaFacturasDirectivos_total - 0;
            foreach ($details as $key => $value) {
                $sumaFinal = $sumaFinal - $value->sumaProducto_total;
            }
        } else  if (count($directivos) == 0 && count($multasRetrasos) == 0 && count($details) != 0) {
            $sumaFinal = $facturas[0]->sumaFacturas_total;
            foreach ($details as $key => $value) {
                $sumaFinal = $sumaFinal - $value->sumaProducto_total;
            }
        } else if (count($directivos) != 0 && count($multasRetrasos) == 0 && count($details) == 0) {
            $sumaFinal = $facturas[0]->sumaFacturas_total - $directivos[0]->sumaFacturasDirectivos_total - 0;
        } else if (count($directivos) == 0 && count($multasRetrasos) != 0 && count($details) == 0) {
            $sumaFinal = $facturas[0]->sumaFacturas_total - 0 - $multasRetrasos[0]->sumaFacturasRetrasos_total;
        } else if (count($directivos) == 0 && count($multasRetrasos) == 0 && count($details) == 0) {
            $sumaFinal = $facturas[0]->sumaFacturas_total - $directivos[0]->sumaFacturasDirectivos_total - $multasRetrasos[0]->sumaFacturasRetrasos_total;
        } else {
            $sumaFinal = $facturas[0]->sumaFacturas_total - $directivos[0]->sumaFacturasDirectivos_total - $multasRetrasos[0]->sumaFacturasRetrasos_total;
            foreach ($details as $key => $value) {
                $sumaFinal = $sumaFinal - $value->sumaProducto_total;
            }
        }

        // die();
        $data = array(
            'status' => 'success',
            'code' => 200,
            'consumoTotal' => $consumoTotal,
            'facturaTotalMes' => $facturas,
            'facturaTotalDirectivos' => $directivos,
            'facturaTotalRetrasos' => $multasRetrasos,
            'agrupados' => $details,
            'multaReunion' => $multaReunion,
            'consumoPrecioTotal' => $consumoTotal,
            'facturaTotal' => $facturaTotal
        );

        // Devuelve en json con laravel
        return response()->json($data, $data['code']);
    }
}
