<?php

namespace App\Http\Controllers;

use App\Http\Requests\reunion\BuscarReunionRequest;
use App\Http\Requests\reunion\StoreRequest;
use App\Models\Reunion;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReunionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reunion = Reunion::orderBy('id', 'DESC')->paginate(5);

        $data = array(
            'code' => 200,
            'status' => 'success',
            'reunion' => $reunion
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
        // 1.-Recoger datos por post

        $params = (object) $request->all(); // Devulve un obejto



        // 2.-Validar datos
        $validate = Validator::make($request->all(), [
            'reunion' => 'required|unique:reuniones',
            'multa' => 'required',
            'fecha' => 'required',
        ]);

        // Comprobar si los datos son validos
        if ($validate->fails()) { // en caso si los datos fallan la validacion
            // La validacion ha fallado
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Los datos enviados no son correctos ó la reunión ya existe!',
                'reunion' => $request->all(),
                'errors' => $validate->errors()
            );
        } else {
            // Si la validacion pasa correctamente
            // Crear el objeto usuario para guardar en la base de datos
            $reunion = new Reunion();
            $reunion->reunion = $params->reunion;
            $reunion->multa = $params->multa;
            $reunion->fecha = $params->fecha;

            try {
                // Guardar en la base de datos
                // 5.-Crear el usuario
                $reunion->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'La reunión se correctamente',
                    'reunion'  => $reunion
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


    // Buscar Reuniones
    public function buscarReuniones(BuscarReunionRequest $request)
    {
        $params = (object) $request->all(); // Devuelve un obejto
        $texto = trim($params->textos);

        $resultado = DB::table('reuniones')
            ->select(
                "reuniones.id",
                "reuniones.reunion",
                "reuniones.multa",
                "reuniones.retraso",
                "reuniones.estadomulta",
                "reuniones.fecha",
                "reuniones.estado",
            )
            ->where('reuniones.reunion', 'like', "%$texto%")
            ->paginate(5);
        $data = array(
            'status' => 'success',
            'code' => 200,
            'reunion' => $resultado
        );

        // Devuelve en json con laravel
        return response()->json($data, $data['code']);
    }
}
