<?php

namespace App\Http\Controllers;

use App\Http\Requests\reportes\CobroxMesRequest;
use App\Http\Requests\reportes\CobroxMesSociosRequest;
use App\Http\Requests\reportes\ListaDeudoresRequest;
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

        // Consulta NRO:1 solo consumo de todos en general
        $consumoDirectivoVeinte = DB::table('facturas')
            ->join(
                'consumos',
                'facturas.consumo_id',
                '=',
                'consumos.id'
            )
            ->select(
                "facturas.mes_pago",
                DB::raw('SUM(consumos.precio) as consumoDirectivoMenorVeinte'),
            )
            ->where('facturas.mes_pago', '=', $params->mes)
            ->where('facturas.anio_pago', '=', $params->anio)
            ->where('facturas.estado_pago', '=', 1)
            ->where('consumos.directivo', '=', 1)
            ->where('consumos.precio', '<=', 20)
            ->groupBy('facturas.mes_pago')
            ->get();

        // Consulta NRO:2 Factura total pagado sin directivos menores a 20bs
        $facturaTotalPagado = DB::table('facturas')
            ->join(
                'consumos',
                'facturas.consumo_id',
                '=',
                'consumos.id'
            )
            ->select(
                "facturas.mes_pago",
                DB::raw('SUM(facturas.total_pagado) as facturaTotal'),
            )
            ->where('facturas.mes_pago', '=', $params->mes)
            ->where('facturas.anio_pago', '=', $params->anio)
            ->where('facturas.estado_pago', '=', 1)
            ->groupBy('facturas.mes_pago')
            ->get();

        // consulta Nro.-3 Agrupacion  de productos por mes de cobro
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

        // consulta NRO.4.- Total multas
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

        // Consulta NRO. 5 Reporte de socios que tienen multa de reuniones
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


        $data = array(
            'status' => 'success',
            'code' => 200,

            'consumoDirectivoMenorVeinte' => $consumoDirectivoVeinte, // Nro.-1
            'facturaTotalPagado' => $facturaTotalPagado, // Nro.-2
            'facturaDetalle' => $details, // Nro.3
            'facturaTotalRetrasos' => $multasRetrasos, // Nro.4
            'multaReunion' => $multaReunion, // Nro.5

        );

        // Devuelve en json con laravel
        return response()->json($data, $data['code']);
    }

    public function cobroxMesSocios(CobroxMesSociosRequest $request)
    {
        // 1.-Recoger los usuarios por post
        $params = (object) $request->all(); // Devulve un obejto

        // Consulta NRO:1 solo consumo de todos en general
        $listaSociosPagaron = DB::table('facturas')
            ->leftJoin('factura_reunion', 'facturas.id', '=', 'factura_reunion.factura_id')
            ->join('consumos', 'facturas.consumo_id', '=', 'consumos.id')
            ->join('socios', 'consumos.socio_id', '=', 'socios.id')
            ->join('personas', 'socios.persona_id', '=', 'personas.id')
            ->select(
                "facturas.id AS idFactura",
                "facturas.estado_pago",
                "facturas.retraso",
                "facturas.directivo_especial",
                "facturas.fecha_emision",
                "facturas.total_pagado",
                "factura_reunion.opcion",
                "factura_reunion.precio AS reunionPrecio",
                "socios.id AS idSocio",
                "personas.nombres",
                "personas.ap_paterno AS paterno",
                "personas.ap_materno AS materno",
                "personas.carnet",
                "consumos.mes",
                "consumos.anio",
                "consumos.lecturaAnterior",
                "consumos.LecturaActual",
                "consumos.consumo",
                "consumos.precio AS precioConsumo",
                "consumos.estado",
                "consumos.directivo"
            )
            ->where('facturas.mes_pago', '=', $params->mes)
            ->where('facturas.anio_pago', '=', $params->anio)
            ->where('facturas.estado_pago', '=', 1)
            ->where('facturas.directivo_especial', '=', 'no')
            ->orderBy('socios.id', 'ASC')
            ->get();


        // Consulta NRO:2 solo consumo de todos en general
        $suma = DB::table('facturas')
            ->leftJoin('factura_reunion', 'facturas.id', '=', 'factura_reunion.factura_id')
            ->select(
                "facturas.mes_pago",
                DB::raw('SUM(facturas.total_pagado) as facturaTotalPagado'),
            )
            ->where('facturas.mes_pago', '=', $params->mes)
            ->where('facturas.anio_pago', '=', $params->anio)
            ->where('facturas.estado_pago', '=', 1)
            ->where('facturas.directivo_especial', '=', 'no')
            ->groupBy('facturas.mes_pago')
            ->get();

        // Consulta NRO:3 solo consumo de todos en general
        $listaDirectivosBeneficiarios = DB::table('facturas')
            ->leftJoin('factura_reunion', 'facturas.id', '=', 'factura_reunion.factura_id')
            ->join(
                'consumos',
                'facturas.consumo_id',
                '=',
                'consumos.id'
            )
            ->join(
                'socios',
                'consumos.socio_id',
                '=',
                'socios.id'
            )
            ->join('personas', 'socios.persona_id', '=', 'personas.id')
            ->select(
                "facturas.id AS idFactura",
                "facturas.estado_pago",
                "facturas.retraso",
                "facturas.directivo_especial",
                "facturas.fecha_emision",
                "facturas.total_pagado",
                "factura_reunion.opcion",
                "factura_reunion.precio AS reunionPrecio",
                "socios.id AS idSocio",
                "personas.nombres",
                "personas.ap_paterno AS paterno",
                "personas.ap_materno AS materno",
                "personas.carnet",
                "consumos.mes",
                "consumos.anio",
                "consumos.lecturaAnterior",
                "consumos.LecturaActual",
                "consumos.consumo",
                "consumos.precio AS precioConsumo",
                "consumos.estado",
                "consumos.directivo"
            )
            ->where('facturas.mes_pago', '=', $params->mes)
            ->where('facturas.anio_pago', '=', $params->anio)
            ->where('facturas.estado_pago', '=', 1)
            ->where('facturas.directivo_especial', '=', 'si')
            ->orderBy('socios.id', 'ASC')
            ->get();


        $data = array(
            'status' => 'success',
            'code' => 200,
            'listaSociosPagaron' => $listaSociosPagaron,
            'listaDirectivosBeneficiarios' => $listaDirectivosBeneficiarios,
            'suma' => $suma,

        );

        // Devuelve en json con laravel
        return response()->json($data, $data['code']);
    }

    public function listaDeudores(ListaDeudoresRequest $request)
    {
        // 1.-Recoger los usuarios por post
        $params = (object) $request->all(); // Devulve un obejto
    }
}
