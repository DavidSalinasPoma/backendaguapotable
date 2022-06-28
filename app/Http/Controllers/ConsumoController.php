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
use App\Models\Reunion;
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
            'directivo' => 'required',
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
            $consumo->directivo = $params->directivo;
            $paramsArray = array(
                'estado' => 1
            );

            try {

                // Validar que no exita datos duplicados
                $validarDuplicado = Consumo::where("mes", "=", $params->mes)
                    ->where("anio", "=", $params->anio)
                    ->where("socio_id", "=", $params->socio_id)
                    ->get(); // Trae el usuario en formato JSON

                if (count($validarDuplicado) == 0) {
                    // 5.-Crear el usuario
                    $consumo->save();
                    // 5.- Actualizar los datos en la base de datos.
                    // Lista::where('id', $params->lista_id)->update($paramsArray);
                    DB::table('listas')
                        ->where('id', $params->lista_id)
                        ->update($paramsArray);

                    // Logica para modificar el estado_consumo de reunion
                    $lista = Lista::where("estado", "=", 0)->get(); // Trae el usuario en formato JSON
                    if (count($lista) == 0) {
                        $paramsArrayLista = array(
                            'estado_consumo' => 1
                        );
                        // Reunion::where('estado', '=', 1)->update($paramsArrayLista);
                        DB::table('reuniones')
                            ->where('estado', '=', 1)
                            ->update($paramsArrayLista);
                    }
                    // Fin Logica para modificar el estado de reunion Consumo
                    $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'El consumo se ha creado correctamente',
                        'consumo'  => $consumo
                    );
                } else {
                    // Fin Logica para modificar el estado de reunion Consumo
                    $data = array(
                        'status' => 'success',
                        'code' => 404,
                        'message' => 'Este registro ya existe',
                        'validarDuplicado'  => $validarDuplicado
                    );
                }
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
            case 137:
                $precio = 261.5;
                break;
            case 138:
                $precio = 263.5;
                break;
            case 139:
                $precio = 265.5;
                break;
            case 140:
                $precio = 267.5;
                break;
            case 141:
                $precio = 269.5;
                break;
            case 142:
                $precio = 271.5;
                break;
            case 143:
                $precio = 273.5;
                break;
            case 144:
                $precio = 275.5;
                break;
            case 145:
                $precio = 277.5;
                break;
            case 146:
                $precio = 279.5;
                break;
            case 147:
                $precio = 281.5;
                break;
            case 148:
                $precio = 283.5;
                break;
            case 149:
                $precio = 285.5;
                break;
            case 150:
                $precio = 287.5;
                break;
            case 151:
                $precio = 289.5;
                break;
            case 152:
                $precio = 291.5;
                break;
            case 153:
                $precio = 293.5;
                break;
            case 154:
                $precio = 295.5;
                break;
            case 155:
                $precio = 297.5;
                break;
            case 156:
                $precio = 299.5;
                break;
            case 157:
                $precio = 301.5;
                break;
            case 158:
                $precio = 303.5;
                break;
            case 159:
                $precio = 305.5;
                break;
            case 160:
                $precio = 307.5;
                break;
            case 161:
                $precio = 309.5;
                break;
            case 162:
                $precio = 311.5;
                break;
            case 163:
                $precio = 313.5;
                break;
            case 164:
                $precio = 315.5;
                break;
            case 165:
                $precio = 317.5;
                break;
            case 166:
                $precio = 319.5;
                break;
            case 167:
                $precio = 321.5;
                break;
            case 168:
                $precio = 323.5;
                break;
            case 169:
                $precio = 325.5;
                break;
            case 170:
                $precio = 327.5;
                break;
            case 171:
                $precio = 329.5;
                break;
            case 172:
                $precio = 331.5;
                break;
            case 173:
                $precio = 333.5;
                break;
            case 174:
                $precio = 335.5;
                break;
            case 175:
                $precio = 337.5;
                break;
            case 176:
                $precio = 339.5;
                break;
            case 177:
                $precio = 341.5;
                break;
            case 178:
                $precio = 343.5;
                break;
            case 179:
                $precio = 345.5;
                break;
            case 180:
                $precio = 347.5;
                break;
            case 181:
                $precio = 349.5;
                break;
            case 182:
                $precio = 351.5;
                break;
            case 183:
                $precio = 353.5;
                break;
            case 184:
                $precio = 355.5;
                break;
            case 185:
                $precio = 357.5;
                break;
            case 186:
                $precio = 359.5;
                break;
            case 187:
                $precio = 361.5;
                break;
            case 188:
                $precio = 363.5;
                break;
            case 189:
                $precio = 365.5;
                break;
            case 190:
                $precio = 367.5;
                break;
            case 191:
                $precio = 369.5;
                break;
            case 192:
                $precio = 371.5;
                break;
            case 193:
                $precio = 373.5;
                break;
            case 194:
                $precio = 375.5;
                break;
            case 195:
                $precio = 377.5;
                break;
            case 196:
                $precio = 379.5;
                break;
            case 197:
                $precio = 381.5;
                break;
            case 198:
                $precio = 383.5;
                break;
            case 199:
                $precio = 385.5;
                break;
            case 200:
                $precio = 387.5;
                break;
            case 201:
                $precio = 389.5;
                break;
            case 202:
                $precio = 391.5;
                break;
            case 203:
                $precio = 393.5;
                break;
            case 204:
                $precio = 395.5;
                break;
            case 205:
                $precio = 397.5;
                break;
            case 206:
                $precio = 399.5;
                break;
            case 207:
                $precio = 401.5;
                break;
            case 208:
                $precio = 403.5;
                break;
            case 209:
                $precio = 405.5;
                break;
            case 210:
                $precio = 407.5;
                break;
            case 211:
                $precio = 409.5;
                break;
            case 212:
                $precio = 411.5;
                break;
            case 213:
                $precio = 413.5;
                break;
            case 214:
                $precio = 415.5;
                break;
            case 215:
                $precio = 417.5;
                break;
            case 216:
                $precio = 419.5;
                break;
            case 217:
                $precio = 421.5;
                break;
            case 218:
                $precio = 423.5;
                break;
            case 219:
                $precio = 425.5;
                break;
            case 220:
                $precio = 427.5;
                break;
            case 221:
                $precio = 429.5;
                break;
            case 222:
                $precio = 431.5;
                break;
            case 223:
                $precio = 433.5;
                break;
            case 224:
                $precio = 435.5;
                break;
            case 225:
                $precio = 437.5;
                break;
            case 226:
                $precio = 439.5;
                break;
            case 227:
                $precio = 441.5;
                break;
            case 228:
                $precio = 443.5;
                break;
            case 229:
                $precio = 445.5;
                break;
            case 230:
                $precio = 447.5;
                break;
            case 231:
                $precio = 449.5;
                break;
            case 232:
                $precio = 451.5;
                break;
            case 233:
                $precio = 453.5;
                break;
            case 234:
                $precio = 455.5;
                break;
            case 235:
                $precio = 457.5;
                break;
            case 236:
                $precio = 459.5;
                break;
            case 237:
                $precio = 461.5;
                break;
            case 238:
                $precio = 463.5;
                break;
            case 239:
                $precio = 465.5;
                break;
            case 240:
                $precio = 467.5;
                break;
            case 241:
                $precio = 469.5;
                break;
            case 242:
                $precio = 471.5;
                break;
            case 243:
                $precio = 473.5;
                break;
            case 244:
                $precio = 475.5;
                break;
            case 245:
                $precio = 477.5;
                break;
            case 246:
                $precio = 479.5;
                break;
            case 247:
                $precio = 481.5;
                break;
            case 248:
                $precio = 483.5;
                break;
            case 249:
                $precio = 485.5;
                break;
            case 250:
                $precio = 487.5;
                break;
            case 251:
                $precio = 489.5;
                break;
            case 252:
                $precio = 491.5;
                break;
            case 253:
                $precio = 493.5;
                break;
            case 254:
                $precio = 495.5;
                break;
            case 255:
                $precio = 497.5;
                break;
            case 256:
                $precio = 499.5;
                break;
            case 257:
                $precio = 501.5;
                break;
            case 258:
                $precio = 503.5;
                break;
            case 259:
                $precio = 505.5;
                break;
            case 260:
                $precio = 507.5;
                break;
            case 261:
                $precio = 509.5;
                break;
            case 262:
                $precio = 511.5;
                break;
            case 263:
                $precio = 513.5;
                break;
            case 264:
                $precio = 515.5;
                break;
            case 265:
                $precio = 517.5;
                break;
            case 266:
                $precio = 519.5;
                break;
            case 267:
                $precio = 521.5;
                break;
            case 268:
                $precio = 523.5;
                break;
            case 269:
                $precio = 525.5;
                break;
            case 270:
                $precio = 527.5;
                break;
            case 271:
                $precio = 529.5;
                break;
            case 272:
                $precio = 531.5;
                break;
            case 273:
                $precio = 533.5;
                break;
            case 274:
                $precio = 535.5;
                break;
            case 275:
                $precio = 537.5;
                break;
            case 276:
                $precio = 539.5;
                break;
            case 277:
                $precio = 541.5;
                break;
            case 278:
                $precio = 543.5;
                break;
            case 279:
                $precio = 545.5;
                break;
            case 280:
                $precio = 547.5;
                break;
            case 281:
                $precio = 549.5;
                break;
            case 282:
                $precio = 551.5;
                break;
            case 283:
                $precio = 553.5;
                break;
            case 284:
                $precio = 555.5;
                break;
            case 285:
                $precio = 557.5;
                break;
            case 286:
                $precio = 559.5;
                break;
            case 287:
                $precio = 561.5;
                break;
            case 288:
                $precio = 563.5;
                break;
            case 289:
                $precio = 565.5;
                break;
            case 290:
                $precio = 567.5;
                break;
            case 291:
                $precio = 569.5;
                break;
            case 292:
                $precio = 571.5;
                break;
            case 293:
                $precio = 573.5;
                break;
            case 294:
                $precio = 575.5;
                break;
            case 295:
                $precio = 577.5;
                break;
            case 296:
                $precio = 579.5;
                break;
            case 297:
                $precio = 581.5;
                break;
            case 298:
                $precio = 583.5;
                break;
            case 299:
                $precio = 585.5;
                break;
            case 300:
                $precio = 587.5;
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
