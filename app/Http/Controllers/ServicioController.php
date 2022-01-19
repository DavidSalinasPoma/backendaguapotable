<?php

namespace App\Http\Controllers;

use App\Http\Requests\servicio\BuscarServicioRequest;
use App\Http\Requests\servicio\StoreRequest;
use App\Http\Requests\servicio\UpdateRequest;
use App\Models\Servicio;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $servicio = Servicio::orderBy('id', 'DESC')->paginate(5);

        $data = array(
            'code' => 200,
            'status' => 'success',
            'servicio' => $servicio
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
            'nombre' => 'required|unique:servicios',
            'descripcion' => 'required',
            'costo' => 'required',
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
            $servicio = new Servicio();
            $servicio->nombre = $params->nombre;
            $servicio->descripcion = $params->descripcion;
            $servicio->costo = $params->costo;

            try {
                // Guardar en la base de datos

                // 5.-Crear el usuario
                $servicio->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El servicio se ha creado correctamente',
                    'servicio'  => $servicio
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
        $servicio = Servicio::find($id);

        // Comprobamos si es un objeto eso quiere decir si exist en la base de datos.
        if (is_object($servicio)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'servicio' => $servicio
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El servicio no existe'
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
        $servicio = Servicio::find($id);

        if (!empty($servicio)) {

            // para actualizar unique
            $datoServicio = $servicio->nombre;

            // 1.- Validar datos recogidos por POST. pasando al getIdentity true
            $validate = Validator::make($request->all(), [

                'nombre' => 'required',
                'descripcion' => 'required',
                'costo' => 'required',
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


                if ($datoServicio == $paramsArray['nombre']) {
                    unset($paramsArray['nombre']);
                }

                // 4.- Quitar los campos que no quiero actualizar de la peticion.
                unset($paramsArray['created_at']);
                // unset($paramsArray['updated_at']);


                try {
                    // 5.- Actualizar los datos en la base de datos.
                    Servicio::where('id', $id)->update($paramsArray);

                    // var_dump($user_update);
                    // die();
                    // 6.- Devolver el array con el resultado.
                    $data = array(
                        'status' => 'Succes',
                        'code' => 200,
                        'message' => 'El evento se ha modificado correctamente',
                        'servicio' => $servicio,
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
                'message' => 'Este servicio no existe.',
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
        $servicio = Servicio::find($id); // Trae el usuario en formato JSON

        // echo $user->estado;
        // die();

        if (!empty($servicio)) {
            $paramsArray = json_decode($servicio, true); // devuelve un array
            // var_dump($paramsArray);
            // die();

            // Quitar los campos que no quiero actualizar de la peticion.
            unset($paramsArray['nombre']);
            unset($paramsArray['descripcion']);
            unset($paramsArray['costo']);
            unset($paramsArray['created_at']);
            unset($paramsArray['updated_at']);

            // Campo stado a modificar
            $paramsArray['estado'] = 0;

            try {
                // 5.- Actualizar los datos en la base de datos.
                Servicio::where('id', $id)->update($paramsArray);

                // 6.- Devolver el array con el resultado.
                $data = array(
                    'status' => 'Succes',
                    'code' => 200,
                    'message' => 'El servicio ha sido dado de baja correctamente',
                    'servicio' => $servicio,
                    'changes' => $paramsArray
                );
            } catch (Exception $e) {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El servicio no ha sido dado de baja',
                    'error' => $e
                );
            }
            return response()->json($data, $data['code']);
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Este servicio no existe.',
            );
            return response()->json($data, $data['code']);
        }
    }

    // Buscar Usuario
    public function buscarServicio(BuscarServicioRequest $request)
    {
        $params = (object) $request->all(); // Devuelve un obejto
        $texto = trim($params->textos);

        $resultado = DB::table('servicios')
            ->select("servicios.id", "servicios.nombre", "servicios.descripcion", "servicios.estado")
            ->where('servicios.id', 'like', "%$texto%")
            ->orWhere('servicios.nombre', 'like', "%$texto%")
            ->orWhere('servicios.descripcion', 'like', "%$texto%")
            ->orWhere('servicios.estado', 'like', "%$texto%")
            ->paginate(5);
        $data = array(
            'status' => 'success',
            'code' => 200,
            'servicio' => $resultado
        );

        // Devuelve en json con laravel
        return response()->json($data, $data['code']);
    }
}
