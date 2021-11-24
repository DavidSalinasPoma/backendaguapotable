<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;


    // Nuestro modelo para la base de datos

    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'roles';

    // Relación de muchos a muchos
    public function usuarios()
    {
        return $this->belongsToMany('App\models\Usuario');
    }

    // Relación de muchos a muchos
    public function permisos()
    {
        return $this->belongsToMany('App\models\Permiso');
    }
}
