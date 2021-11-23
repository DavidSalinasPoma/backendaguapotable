<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PersonaController extends Controller
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
        // 1.- RECIBIR DATOS
        // Recibimos los datos de angular en una variable
        $json = $request->input('json', null);

        // Convertimos los datos en objeto y array
        $params = json_decode($json); // objeto
        $paramsArray = json_decode($json, true); // Array


        // Validamos si esta vacio
        if (!empty($params) && !empty($paramsArray)) {

            // 2.-VALIDAR DATOS
            $validate = Validator::make($paramsArray, [
                'carnet' => 'required|unique:personas',
                'expedito' => 'required',
                'nombres' => 'required',
                'ap_paterno' => 'required',
                'ap_materno' => 'required',
                'sexo' => 'required',
                'direccion' => 'required',
                'nacimiento' => 'required',
                'estado_civil' => 'required',
            ]);

            // 5.- SI LA VALIDACION FUE CORRECTA
            // Comprobar si los datos son validos
            if ($validate->fails()) { // en caso si los datos fallan la validacion
                // La validacion ha fallado
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El registro no se ha creado',
                    'errors' => $validate->errors()
                );
            } else {

                // CONSEGUIR EL USUARIO IDENTIFICADO->El que hace el registro.
                $jwtAuth = new JwtAuth();
                $token = $request->header('token-usuario', null);
                $user = $jwtAuth->checkToken($token, true); // Devuelve el token decodificado en un objeto.

                // Si la validacion pasa correctamente  
                // Crear el objeto usuario para guardar en la base de datos
                $promocion = new Promocion();
                $promocion->titulo = $paramsArray['titulo'];
                $promocion->descripcion = $paramsArray['descripcion'];
                $promocion->imagen = $paramsArray['imagen'];
                $promocion->usuarios_id = $user->sub;


                // 7.-GUARDAR EN LA BASE DE DATOS
                $promocion->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'La promoción se ha creado correctamente.',
                );
            }
        } else {
            $data = array(
                'status' => 'Error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos.'
            );
        }
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

    // Pruebas de este controlador
    public function pruebas(Request $request)
    {
        return "Acción de pruebas de PERSONA-CONTROLLER";
    }
}
