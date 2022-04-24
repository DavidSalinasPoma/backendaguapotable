<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaReunion extends Model
{
    use HasFactory;
    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'factura_reunion';

    // relacion de muchos a uno inversa(muchos a uno)
    public function reunion()
    {
        return $this->belongsTo('App\Models\Reunion', 'reunion_id'); // Recibe a Reunion
    }

    // relacion de muchos a uno inversa(muchos a uno)
    public function factura()
    {
        return $this->belongsTo('App\Models\Factura', 'factura_id'); // Recibe a Reunion
    }
}
