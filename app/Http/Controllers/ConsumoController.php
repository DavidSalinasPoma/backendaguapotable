<?php

namespace App\Http\Controllers;

use App\Http\Requests\consumo\StoreRequest;
use App\Http\Requests\consumo\UpdateRequest;
use App\Models\Consumo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConsumoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $consumo = Consumo::all();
        $data = array(
            'code' => 200,
            'status' => 'success',
            'consumo' => $consumo
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
            'lecturaAnterior' => 'required',
            'lecturaActual' => 'required',
            'img' => 'required',
            'observaciones' => 'required',
            'socio_id' => 'required',
            'apertura_id' => 'required',
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
            $consumo = new Consumo();
            $consumo->lecturaAnterior = $params->lecturaAnterior;
            $consumo->lecturaActual = $params->lecturaActual;
            // $consumo->consumo = $consumoTotal;
            // $consumo->precio = $precio;
            $consumo->img = $params->img;
            $consumo->observaciones = $params->observaciones;
            $consumo->socio_id = $params->socio_id;
            $consumo->apertura_id = $params->apertura_id;


            try {
                // Guardar en la base de datos

                // 5.-Crear el usuario
                $consumo->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El consumo se ha creado correctamente',
                    'consumo'  => $consumo
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
        $consumo = Consumo::find($id);

        // Comprobamos si es un objeto eso quiere decir si exist en la base de datos.
        if (is_object($consumo)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'consumo' => $consumo
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El consumo no existe'
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
        $consumo = Consumo::find($id);
        // echo $user->estado;
        // die();

        if (!empty($consumo)) {

            // 1.- Validar datos recogidos por POST. pasando al getIdentity true
            $validate = Validator::make($request->all(), [

                'lecturaAnterior' => 'required',
                'lecturaActual' => 'required',
                'img' => 'required',
                'observaciones' => 'required',
                'socio_id' => 'required',
                'apertura_id' => 'required',

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
                unset($paramsArray['lecturaAnterior']);
                unset($paramsArray['created_at']);
                // unset($paramsArray['updated_at']);

                // 3.- Cifrar la PASSWORD.
                // $paramsArray['password'] = hash('sha256', $paramsArray['password']); // para verificar que las contraseña a consultar sean iguales.
                try {
                    // 5.- Actualizar los datos en la base de datos.
                    Consumo::where('id', $id)->update($paramsArray);

                    // var_dump($user_update);
                    // die();
                    // 6.- Devolver el array con el resultado.
                    $data = array(
                        'status' => 'Succes',
                        'code' => 200,
                        'message' => 'El consumo se ha modificado correctamente',
                        'consumo' => $consumo,
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
                'message' => 'Este consumo no existe.',
                // 'error' => $e
            );
            return response()->json($data, $data['code']);
        }
    }
}
