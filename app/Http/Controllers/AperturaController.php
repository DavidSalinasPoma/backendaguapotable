<?php

namespace App\Http\Controllers;

use App\Http\Requests\apertura\StoreRequest;
use App\Http\Requests\apertura\UpdateRequest;
use App\Models\Apertura;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AperturaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $apertura = Apertura::all(); // Saca con la apertura relacionado de la base de datos
        $data = array(
            'code' => 200,
            'status' => 'success',
            'apertura' => $apertura
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
            'año' => 'required',
            'mes' => 'required',
            'fecha_pago_ini' => 'required',
            'fecha_pago_final' => 'required',
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
            $apertura = new Apertura();
            $apertura->año = $params->año;
            $apertura->mes = $params->mes;
            $apertura->fecha_pago_ini = $params->fecha_pago_ini;
            $apertura->fecha_pago_final = $params->fecha_pago_final;

            try {
                // Guardar en la base de datos

                // 5.-Crear el usuario
                $apertura->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'La apertura se ha creado correctamente',
                    'apertura'  => $apertura
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
        $apertura = Apertura::find($id);

        // Comprobamos si es un objeto eso quiere decir si exist en la base de datos.
        if (is_object($apertura)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'apertura' => $apertura
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'La apertura no existe'
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
        $apertura = Apertura::find($id);
        // echo $user->estado;
        // die();

        if (!empty($apertura)) {

            // 1.- Validar datos recogidos por POST. pasando al getIdentity true
            $validate = Validator::make($request->all(), [

                'año' => 'required',
                'mes' => 'required',
                'fecha_pago_ini' => 'required',
                'fecha_pago_final' => 'required',

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
                    Apertura::where('id', $id)->update($paramsArray);

                    // var_dump($user_update);
                    // die();
                    // 6.- Devolver el array con el resultado.
                    $data = array(
                        'status' => 'Succes',
                        'code' => 200,
                        'message' => 'La apertura se ha modificado correctamente',
                        'apertura' => $apertura,
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
                'message' => 'Esta apertura no existe.',
                // 'error' => $e
            );
            return response()->json($data, $data['code']);
        }
    }
}
