<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reunion extends Model
{
    use HasFactory;

    // Nuestro modelo para la base de datos

    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'reuniones';

    // Se dirige hacia detalle reunion
    public function detallereunion()
    {
        return $this->hasMany('App\Models\DetalleReunion'); // se dirige hacia reunnion detalle
    }

    // Se dirige hacia factura reunion
    public function facturareunion()
    {
        return $this->hasMany('App\Models\FacturaReunion'); // se dirige hacia Factura reunion
    }
}
