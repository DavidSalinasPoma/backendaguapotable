<?php

namespace App\Http\Controllers;

use App\Http\Requests\consumo\ConsumoRequest;
use App\Http\Requests\consumo\PrecioRequest;
use App\Http\Requests\consumo\StoreRequest;
use App\Http\Requests\consumo\UpdateRequest;
use App\Models\Consumo;
use App\Models\Evento;
use App\Models\Lista;
use App\Models\Productos;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'mes' => 'required',
            'anio' => 'required',
            'socio_id' => 'required',
            'apertura_id' => 'required',
            'precio' => 'required',
            'consumo' => 'required',
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
            $consumo->consumo = $params->consumo;
            $consumo->precio = $params->precio;
            $consumo->mes = $params->mes;
            $consumo->anio = $params->anio;
            $consumo->socio_id = $params->socio_id;
            $consumo->apertura_id = $params->apertura_id;

            $paramsArray = array(
                'estado' => 1
            );


            try {
                // Guardar en la base de datos

                // 5.-Crear el usuario
                $consumo->save();
                // 5.- Actualizar los datos en la base de datos.
                Lista::where('id', $params->lista_id)->update($paramsArray);

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

    public function showConsumos(ConsumoRequest $request)
    {
        // 1.-Recoger los usuarios por post

        $params = (object) $request->all(); // Devulve un obejto
        // var_dump($params->fecha);
        // die();
        // 1.-Persona el la funcion que esta en el Modelo de soscio
        $consumo = DB::table('consumos')
            ->join('socios', 'consumos.socio_id', '=', 'socios.id')
            ->join('aperturas', 'consumos.apertura_id', '=', 'aperturas.id')
            ->join('personas', 'socios.persona_id', '=', 'personas.id')
            ->where('socios.id', '=', $params->id)
            ->where('consumos.mes', '=', $params->mes)
            ->where('consumos.anio', '=', $params->anio)
            ->select(
                "socios.id",
                "personas.nombres",
                "personas.ap_paterno",
                "personas.ap_materno",
                "personas.carnet",
                "aperturas.mes",
                "consumos.lecturaAnterior",
                "consumos.lecturaActual",
                "consumos.consumo",
                "consumos.precio",
                "consumos.mes",
                "consumos.anio",
                "consumos.estado",

            )
            ->paginate(10);

        // var_dump($consumo[0]->mes);
        // $fecha = Carbon::createFromDate($consumo[0]->mes);

        // echo ($fecha->locale('es')->monthName);
        // echo ($fecha->year);
        // die();

        $data = array(
            'status' => 'success',
            'code' => 200,
            'socio' => $consumo
        );

        // Devuelve en json con laravel
        return response()->json($data, $data['code']);
    }

    public function preciosConsumos(PrecioRequest $request)
    {
        // 1.-Recoger los usuarios por post

        $params = (object) $request->all(); // Devulve un obejto

        switch ($params->cubos) {
            case 0:
                $precio = 6;
                break;
            case 1:
                $precio = 6;
                break;
            case 2:
                $precio = 6;
                break;
            case 3:
                $precio = 6;
                break;
            case 4:
                $precio = 6;
                break;
            case 5:
                $precio = 6;
                break;
            case 6:
                $precio = 6;
                break;
            case 7:
                $precio = 7;
                break;
            case 8:
                $precio = 8;
                break;
            case 9:
                $precio = 9;
                break;
            case 10:
                $precio = 10;
                break;
            case 11:
                $precio = 11.5;
                break;
            case 12:
                $precio = 13;
                break;
            case 13:
                $precio = 14.5;
                break;
            case 14:
                $precio = 16.5;
                break;
            case 15:
                $precio = 17.5;
                break;
            case 16:
                $precio = 19.5;
                break;
            case 17:
                $precio = 21.5;
                break;
            case 18:
                $precio = 23.5;
                break;
            case 19:
                $precio = 25.5;
                break;
            case 20:
                $precio = 27.5;
                break;
            case 21:
                $precio = 29.5;
                break;
            case 22:
                $precio = 31.5;
                break;
            case 23:
                $precio = 33.5;
                break;
            case 24:
                $precio = 35.5;
                break;
            case 25:
                $precio = 37.5;
                break;
            case 26:
                $precio = 39.5;
                break;
            case 27:
                $precio = 41.5;
                break;
            case 28:
                $precio = 43.5;
                break;
            case 29:
                $precio = 45.5;
                break;
            case 30:
                $precio = 47.5;
                break;
            case 31:
                $precio = 49.5;
                break;
            case 32:
                $precio = 51.5;
                break;
            case 33:
                $precio = 53.5;
                break;
            case 34:
                $precio = 55.5;
                break;
            case 35:
                $precio = 57.5;
                break;
            case 36:
                $precio = 59.5;
                break;
            case 37:
                $precio = 61.5;
                break;
            case 38:
                $precio = 63.5;
                break;
            case 39:
                $precio = 65.5;
                break;
            case 40:
                $precio = 67.5;
                break;
            case 41:
                $precio = 69.5;
                break;
            case 42:
                $precio = 71.5;
                break;
            case 43:
                $precio = 73.5;
                break;
            case 44:
                $precio = 75.5;
                break;
            case 45:
                $precio = 77.5;
                break;
            case 46:
                $precio = 79.5;
                break;
            case 47:
                $precio = 81.5;
                break;
            case 48:
                $precio = 83.5;
                break;
            case 49:
                $precio = 85.5;
                break;
            case 50:
                $precio = 87.5;
                break;
            case 51:
                $precio = 89.5;
                break;
            case 52:
                $precio = 91.5;
                break;
            case 53:
                $precio = 93.5;
                break;
            case 54:
                $precio = 95.5;
                break;
            case 55:
                $precio = 97.5;
                break;
            case 56:
                $precio = 99.5;
                break;
            case 57:
                $precio = 101.5;
                break;
            case 58:
                $precio = 103.5;
                break;
            case 59:
                $precio = 105.5;
                break;
            case 60:
                $precio = 107.5;
                break;
            case 61:
                $precio = 109.5;
                break;
            case 62:
                $precio = 111.5;
                break;
            case 63:
                $precio = 113.5;
                break;
            case 64:
                $precio = 115.5;
                break;
            case 65:
                $precio = 117.5;
                break;
            case 66:
                $precio = 119.5;
                break;
            case 67:
                $precio = 121.5;
                break;
            case 68:
                $precio = 123.5;
                break;
            case 69:
                $precio = 125.5;
                break;
            case 70:
                $precio = 127.5;
                break;
            case 71:
                $precio = 129.5;
                break;
            case 72:
                $precio = 131.5;
                break;
            case 73:
                $precio = 133.5;
                break;
            case 74:
                $precio = 135.5;
                break;
            case 75:
                $precio = 137.5;
                break;
            case 76:
                $precio = 139.5;
                break;
            case 77:
                $precio = 141.5;
                break;
            case 78:
                $precio = 143.5;
                break;
            case 79:
                $precio = 145.5;
                break;
            case 80:
                $precio = 147.5;
                break;
            case 81:
                $precio = 149.5;
                break;
            case 82:
                $precio = 151.5;
                break;
            case 83:
                $precio = 153.5;
                break;
            case 84:
                $precio = 155.5;
                break;
            case 85:
                $precio = 157.5;
                break;
            case 86:
                $precio = 159.5;
                break;
            case 87:
                $precio = 161.5;
                break;
            case 88:
                $precio = 163.5;
                break;
            case 89:
                $precio = 165.5;
                break;
            case 90:
                $precio = 167.5;
                break;
            case 91:
                $precio = 169.5;
                break;
            case 92:
                $precio = 171.5;
                break;
            case 93:
                $precio = 173.5;
                break;
            case 94:
                $precio = 175.5;
                break;
            case 95:
                $precio = 177.5;
                break;
            case 96:
                $precio = 179.5;
                break;
            case 97:
                $precio = 181.5;
                break;
            case 98:
                $precio = 183.5;
                break;
            case 99:
                $precio = 185.5;
                break;
            case 100:
                $precio = 187.5;
                break;
            case 101:
                $precio = 189.5;
                break;
            case 102:
                $precio = 191.5;
                break;
            case 103:
                $precio = 193.5;
                break;
            case 104:
                $precio = 195.5;
                break;
            case 105:
                $precio = 197.5;
                break;
            case 106:
                $precio = 199.5;
                break;
            case 107:
                $precio = 201.5;
                break;
            case 108:
                $precio = 203.5;
                break;
            case 109:
                $precio = 205.5;
                break;
            case 110:
                $precio = 207.5;
                break;
            case 111:
                $precio = 209.5;
                break;
            case 112:
                $precio = 211.5;
                break;
            case 113:
                $precio = 213.5;
                break;
            case 114:
                $precio = 215.5;
                break;
            case 115:
                $precio = 217.5;
                break;
            case 116:
                $precio = 219.5;
                break;
            case 117:
                $precio = 221.5;
                break;
            case 118:
                $precio = 223.5;
                break;
            case 119:
                $precio = 225.5;
                break;
            case 120:
                $precio = 227.5;
                break;
            case 121:
                $precio = 229.5;
                break;
            case 122:
                $precio = 231.5;
                break;
            case 123:
                $precio = 233.5;
                break;
            case 124:
                $precio = 235.5;
                break;
            case 125:
                $precio = 237.5;
                break;
            case 126:
                $precio = 239.5;
                break;
            case 127:
                $precio = 241.5;
                break;
            case 128:
                $precio = 243.5;
                break;
            case 129:
                $precio = 245.5;
                break;
            case 130:
                $precio = 247.5;
                break;
            case 131:
                $precio = 249.5;
                break;
            case 132:
                $precio = 251.5;
                break;
            case 133:
                $precio = 253.5;
                break;
            case 134:
                $precio = 255.5;
                break;
            case 135:
                $precio = 257.5;
                break;
            case 136:
                $precio = 259.5;
                break;

            default:
                $precio = 0;
                break;
        }

        $data = array(
            'status' => 'success',
            'code' => 200,
            'precio' => $precio
        );

        // Devuelve en json con laravel
        return response()->json($data, $data['code']);
    }
}
