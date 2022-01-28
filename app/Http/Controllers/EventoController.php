<?php

namespace App\Http\Controllers;

use App\Http\Requests\evento\BuscarEventoRequest;
use App\Http\Requests\evento\StoreRequest;
use App\Http\Requests\evento\UpdateRequest;
use App\Models\Evento;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $evento = Evento::orderBy('id', 'DESC')->paginate(5);

        $data = array(
            'code' => 200,
            'status' => 'success',
            'evento' => $evento
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
            'evento' => 'required|unique:eventos',
            'descripcion' => 'required',
            'precio' => 'required',
            'tiempo_event' => 'required',
        ]);

        // Comprobar si los datos son validos
        if ($validate->fails()) { // en caso si los datos fallan la validacion
            // La validacion ha fallado
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Los datos enviados no son correctos',
                'evento' => $request->all(),
                'errors' => $validate->errors()
            );
        } else {
            // Si la validacion pasa correctamente
            // Crear el objeto usuario para guardar en la base de datos
            $evento = new Evento();
            $evento->evento = $params->evento;
            $evento->descripcion = $params->descripcion;
            $evento->precio = $params->precio;
            $evento->tiempo_event = $params->tiempo_event;

            try {
                // Guardar en la base de datos

                // 5.-Crear el usuario
                $evento->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El evento se ha creado correctamente',
                    'evento'  => $evento
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
        $evento = Evento::find($id);

        // Comprobamos si es un objeto eso quiere decir si exist en la base de datos.
        if (is_object($evento)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'evento' => $evento
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El evento no existe'
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
        $evento = Evento::find($id);

        if (!empty($evento)) {

            // para actualizar unique
            $datoEvento = $evento->evento;

            // 1.- Validar datos recogidos por POST. pasando al getIdentity true
            $validate = Validator::make($request->all(), [

                'evento' => 'required',
                'descripcion' => 'required',
                'precio' => 'required',
                'tiempo_event' => 'required',
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


                if ($datoEvento == $paramsArray['evento']) {
                    unset($paramsArray['evento']);
                }

                // 4.- Quitar los campos que no quiero actualizar de la peticion.
                unset($paramsArray['created_at']);
                // unset($paramsArray['updated_at']);


                try {
                    // 5.- Actualizar los datos en la base de datos.
                    Evento::where('id', $id)->update($paramsArray);
                    $changes = Evento::find($id);
                    // var_dump($user_update);
                    // die();
                    // 6.- Devolver el array con el resultado.
                    $data = array(
                        'status' => 'Succes',
                        'code' => 200,
                        'message' => 'El evento se ha modificado correctamente',
                        'evento' => $evento,
                        'changes' => $changes
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
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Este evento no existe.',
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
        $evento = Evento::find($id); // Trae el usuario en formato JSON

        // echo $user->estado;
        // die();

        if (!empty($evento)) {
            $paramsArray = json_decode($evento, true); // devuelve un array
            // var_dump($paramsArray);
            // die();

            // Quitar los campos que no quiero actualizar de la peticion.
            unset($paramsArray['evento']);
            unset($paramsArray['descripcion']);
            unset($paramsArray['precio']);
            unset($paramsArray['tiempo_event']);
            unset($paramsArray['created_at']);
            unset($paramsArray['updated_at']);

            // Campo stado a modificar
            $paramsArray['estado'] = 0;

            try {
                // 5.- Actualizar los datos en la base de datos.
                Evento::where('id', $id)->update($paramsArray);

                // 6.- Devolver el array con el resultado.
                $data = array(
                    'status' => 'Succes',
                    'code' => 200,
                    'message' => 'El evento ha sido dado de baja correctamente',
                    'evento' => $evento,
                    'changes' => $paramsArray
                );
            } catch (Exception $e) {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El evento no ha sido dado de baja',
                    'error' => $e
                );
            }
            return response()->json($data, $data['code']);
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Este evento no existe.',
            );
            return response()->json($data, $data['code']);
        }
    }

    // Buscar Usuario
    public function buscarEvento(BuscarEventoRequest $request)
    {
        $params = (object) $request->all(); // Devuelve un obejto
        $texto = trim($params->textos);

        $resultado = DB::table('eventos')
            ->select("eventos.id", "eventos.evento", "eventos.descripcion", "eventos.estado")
            ->where('eventos.id', 'like', "%$texto%")
            ->orWhere('eventos.evento', 'like', "%$texto%")
            ->orWhere('eventos.descripcion', 'like', "%$texto%")
            ->orWhere('eventos.estado', 'like', "%$texto%")
            ->paginate(5);

        $data = array(
            'status' => 'success',
            'code' => 200,
            'evento' => $resultado
        );

        // Devuelve en json con laravel
        return response()->json($data, $data['code']);
    }
}
