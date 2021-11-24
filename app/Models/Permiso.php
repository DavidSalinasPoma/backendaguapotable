<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;


    // Relación de muchos a muchos
    public function usuarios()
    {
        return $this->belongsToMany('App\models\Usuario');
    }
}
