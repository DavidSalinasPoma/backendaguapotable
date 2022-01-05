<?php

namespace App\Http\Controllers;

use App\Http\Requests\empleado\StoreRequest;
use App\Http\Requests\empleado\UpdateRequest;
use App\Models\Empleado;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $empleado = Empleado::all(); // Saca con el usuario relacionado de la base de datos
        $data = array(
            'code' => 200,
            'status' => 'success',
            'empleado' => $empleado
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
            'cargo' => 'required',
            'persona_id' => 'required',
        ]);

        // Comprobar si los datos son validos
        if ($validate->fails()) { // en caso si los datos fallan la validacion
            // La validacion ha fallado
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Los datos enviados no son correctos',
                'empleado' => $request->all(),
                'errors' => $validate->errors()
            );
        } else {
            // Si la validacion pasa correctamente
            // Crear el objeto usuario para guardar en la base de datos
            $empleado = new Empleado();
            $empleado->cargo = $params->cargo;
            $empleado->persona_id = $params->persona_id;

            try {
                // Guardar en la base de datos

                // 5.-Crear el usuario
                $empleado->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El empleado se ha creado correctamente',
                    'empleado'  => $empleado
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
        $empleado = Empleado::find($id);

        // Comprobamos si es un objeto eso quiere decir si exist en la base de datos.
        if (is_object($empleado)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'empleado' => $empleado
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El empleado no existe'
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
        $empleado = Empleado::find($id);
        // echo $user->estado;
        // die();

        if (!empty($empleado)) {

            // 1.- Validar datos recogidos por POST. pasando al getIdentity true
            $validate = Validator::make($request->all(), [

                'persona_id' => 'required',
                'cargo' => 'required',
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
                // unset($paramsArray['id']);
                // unset($paramsArray['password']);
                // // unset($paramsArray['antiguo']);
                unset($paramsArray['created_at']);
                // unset($paramsArray['updated_at']);

                // 3.- Cifrar la PASSWORD.
                // $paramsArray['password'] = hash('sha256', $paramsArray['password']); // para verificar que las contraseña a consultar sean iguales.
                try {
                    // 5.- Actualizar los datos en la base de datos.
                    Empleado::where('id', $id)->update($paramsArray);

                    // var_dump($user_update);
                    // die();
                    // 6.- Devolver el array con el resultado.
                    $data = array(
                        'status' => 'Succes',
                        'code' => 200,
                        'message' => 'El Socio se ha modificado correctamente',
                        'empleado' => $empleado,
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
                'message' => 'Este empleado no existe.',
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
        $empleado = Empleado::find($id); // Trae el usuario en formato JSON

        // echo $user->estado;
        // die();

        if (!empty($empleado)) {
            $paramsArray = json_decode($empleado, true); // devuelve un array
            // var_dump($paramsArray);
            // die();

            // Quitar los campos que no quiero actualizar de la peticion.
            unset($paramsArray['persona_id']);
            unset($paramsArray['cargo']);
            unset($paramsArray['created_at']);
            unset($paramsArray['updated_at']);

            // Campo stado a modificar
            $paramsArray['estado'] = 0;

            try {
                // 5.- Actualizar los datos en la base de datos.
                Empleado::where('id', $id)->update($paramsArray);

                // 6.- Devolver el array con el resultado.
                $data = array(
                    'status' => 'Succes',
                    'code' => 200,
                    'message' => 'El empleado ha sido dado de baja correctamente',
                    'empleado' => $empleado,
                    'changes' => $paramsArray
                );
            } catch (Exception $e) {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El empleado no ha sido dado de baja',
                    'error' => $e

                );
            }
            return response()->json($data, $data['code']);
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Este empleado no existe.',
            );
            return response()->json($data, $data['code']);
        }
    }
}
