<?php

namespace App\Http\Controllers;

use App\Http\Requests\lista\BuscarSocioRequest;
use App\Models\Lista;
use App\Models\Socio;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // 1.-Persona el la funcion que esta en el Modelo de soscio
        $lista = DB::table('listas')
            ->join('socios', 'listas.socio_id', '=', 'socios.id')
            ->join('aperturas', 'listas.apertura_id', '=', 'aperturas.id')
            ->join('personas', 'socios.persona_id', '=', 'personas.id')
            ->join('barrios', 'socios.barrio_id', '=', 'barrios.id')
            ->select("socios.id", "personas.nombres", "personas.ap_paterno", "personas.ap_materno", "personas.carnet", "barrios.nombre", "aperturas.mes", "listas.estado")
            ->where('listas.estado', "=", 0)
            ->orderBy("id")
            ->paginate(10);

        // Con lectura
        $conLectura = Lista::where('listas.estado', '=', 1)->count();
        $sinLectura = Lista::where('listas.estado', '=', 0)->count();

        $totalSocios = Socio::where('socios.estado', '=', 1)->count();

        if ($sinLectura == 0) {
            DB::table('listas')->delete();
        }


        $data = array(
            'code' => 200,
            'status' => 'success',
            'socio' => $lista,
            'sLectura' => $sinLectura,
            'cLectura' => $conLectura,
            'totalsocio' => $totalSocios,
        );
        return response()->json($data, $data['code']);
    }

    // Buscar Usuario
    public function buscarSocios(BuscarSocioRequest $request)
    {
        $params = (object) $request->all(); // Devuelve un obejto
        $texto = trim($params->textos);


        $resultado = DB::table('listas')
            ->join('socios', 'listas.socio_id', '=', 'socios.id')
            ->join('aperturas', 'listas.apertura_id', '=', 'aperturas.id')
            ->join('personas', 'socios.persona_id', '=', 'personas.id')
            ->join('barrios', 'socios.barrio_id', '=', 'barrios.id')
            ->select("socios.id", "personas.nombres", "personas.ap_paterno", "personas.ap_materno", "personas.carnet", "barrios.nombre", "aperturas.mes", "listas.estado")
            ->where('personas.carnet', 'like', $texto)
            ->orWhere('personas.nombres', 'like', "%$texto%")
            ->orWhere('socios.id', 'like', $texto)
            ->orWhere('personas.ap_paterno', 'like', "%$texto%")
            ->orWhere('personas.ap_materno', 'like', "%$texto%")
            ->orWhere('barrios.nombre', 'like', "%$texto%")
            ->paginate(10);


        // Con lectura
        $conLectura = Lista::where('listas.estado', '=', 1)->count();
        $sinLectura = Lista::where('listas.estado', '=', 0)->count();

        $totalSocios = Socio::where('socios.estado', '=', 1)->count();

        $data = array(
            'status' => 'success',
            'code' => 200,
            'socio' => $resultado,
            'sLectura' => $sinLectura,
            'cLectura' => $conLectura,
            'totalsocio' => $totalSocios,
        );

        // Devuelve en json con laravel
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

    // Validar eventos y servicios
    public function validarLista()
    {
        $lista = Lista::all(); // Trae el usuario en formato JSON
        try {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'lista' => $lista,
            );
        } catch (Exception $e) {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Sucedio un error en la consulta',
                'error' => $e

            );
        }
        return response()->json($data, $data['code']);
    }
}
