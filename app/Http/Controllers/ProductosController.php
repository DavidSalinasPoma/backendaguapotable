<?php

namespace App\Http\Controllers;

use App\Http\Requests\productos\EliminarProductoRequest;
use App\Http\Requests\productos\StoreRequest;
use App\Http\Requests\productos\UpdateRequest;
use App\Models\Productos;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductosController extends Controller
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

        // Si la validacion pasa correctamente
        // Crear el objeto usuario para guardar en la base de datos
        $producto = new Productos();
        $producto->nombre = $params->nombre;
        $producto->producto = $params->producto;
        $producto->num_producto = $params->num_producto;
        $producto->precio = $params->precio;
        $producto->cantidad = $params->cantidad;

        try {
            // Guardar en la base de datos
            // 5.-Crear el usuario
            $producto->save();
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'El producto se ha creado correctamente',
                'producto'  => $producto
            );
        } catch (Exception $e) {
            $data = array(
                'status' => 'Error',
                'code' => 404,
                'message' => $e
            );
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
    public function update(UpdateRequest $request, $id)
    {
        $params = (object) $request->all(); // Devuelve un obejto


        if ($params->estado == 1) {
            // 1.-Persona el la funcion que esta en el Modelo de soscio

            // Elimina Producto
            $producto = DB::table('productos')
                ->select("*")
                ->where("productos.nombre", "=", $params->nombre)
                ->where("productos.num_producto", "=", $id)
                ->delete();
            // Crear producto
            $producto = new Productos();
            $producto->nombre = $params->nombre;
            $producto->producto = $params->producto;
            $producto->num_producto = $params->num_producto;
            $producto->precio = $params->precio;
            $producto->cantidad = $params->cantidad;
            $producto->save();

            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => "El $params->nombre fue añadido a la lista de productos.",
                'producto' => $producto
            );
            return response()->json($data, $data['code']);
        } else {
            // Elimina Producto
            $producto = DB::table('productos')
                ->select("*")
                ->where("productos.nombre", "=", $params->nombre)
                ->where("productos.num_producto", "=", $id)
                ->delete();
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => "El $params->nombre fue retirado de la lista de productos.",
                'producto' => $producto
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
        //
    }

    public function eliminarProducto(EliminarProductoRequest $request, $id)
    {

        $params = (object) $request->all(); // Devuelve un obejto

        // Elimina Producto
        $producto = DB::table('productos')
            ->select("*")
            ->where("productos.nombre", "=", $params->nombre)
            ->where("productos.num_producto", "=", $id)
            ->delete();

        $data = array(
            'status' => 'success',
            'code' => 200,
            'message' => "El $params->nombre fue retirado de la lista de productos.",
            'producto' => $producto
        );
        return response()->json($data, $data['code']);
    }
}
