<?php

namespace App\Http\Controllers;

use App\Http\Requests\barrio\StoreRequest;
use App\Http\Requests\barrio\UpdateRequest;
use App\Models\Barrio;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarrioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $barrio = Barrio::all(); // Saca con el servicio relacionado de la base de datos
        $data = array(
            'code' => 200,
            'status' => 'success',
            'barrio' => $barrio
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
            'nombre' => 'required|unique:barrios',
            'descripcion' => 'required',
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
            $barrio = new Barrio();
            $barrio->nombre = $params->nombre;
            $barrio->descripcion = $params->descripcion;

            try {
                // Guardar en la base de datos

                // 5.-Crear el usuario
                $barrio->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El barrio se ha creado correctamente',
                    'barrio'  => $barrio
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
        $barrio = Barrio::find($id);

        // Comprobamos si es un objeto eso quiere decir si exist en la base de datos.
        if (is_object($barrio)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'barrio' => $barrio
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El barrio no existe.'
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
        $barrio = Barrio::find($id);

        if (!empty($barrio)) {


            // para actualizar unique
            $datoBarrio = $barrio->nombre;

            // 1.- Validar datos recogidos por POST. pasando al getIdentity true
            $validate = Validator::make($request->all(), [

                'nombre' => 'required',
                'descripcion' => 'required',
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


                if ($datoBarrio == $paramsArray['nombre']) {
                    unset($paramsArray['nombre']);
                }

                // 4.- Quitar los campos que no quiero actualizar de la peticion.
                unset($paramsArray['created_at']);
                // unset($paramsArray['updated_at']);


                try {
                    // 5.- Actualizar los datos en la base de datos.
                    Barrio::where('id', $id)->update($paramsArray);

                    // var_dump($user_update);
                    // die();
                    // 6.- Devolver el array con el resultado.
                    $data = array(
                        'status' => 'Succes',
                        'code' => 200,
                        'message' => 'El barrio se ha modificado correctamente',
                        'barrio' => $barrio,
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
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Este nombre de barrio no existe.',
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
        $barrio = Barrio::find($id); // Trae el usuario en formato JSON

        // echo $user->estado;
        // die();

        if (!empty($barrio)) {
            $paramsArray = json_decode($barrio, true); // devuelve un array
            // var_dump($paramsArray);
            // die();

            // Quitar los campos que no quiero actualizar de la peticion.
            unset($paramsArray['nombre']);
            unset($paramsArray['descripcion']);
            unset($paramsArray['created_at']);
            unset($paramsArray['updated_at']);

            // Campo stado a modificar
            $paramsArray['estado'] = 0;

            try {
                // 5.- Actualizar los datos en la base de datos.
                Barrio::where('id', $id)->update($paramsArray);

                // 6.- Devolver el array con el resultado.
                $data = array(
                    'status' => 'Succes',
                    'code' => 200,
                    'message' => 'Este nombre de barrio ha sido dado de baja correctamente',
                    'barrio' => $barrio,
                    'changes' => $paramsArray
                );
            } catch (Exception $e) {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Este nombre de barrio no ha sido dado de baja',
                    'error' => $e
                );
            }
            return response()->json($data, $data['code']);
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Este barrio no existe.',
            );
            return response()->json($data, $data['code']);
        }
    }
}
