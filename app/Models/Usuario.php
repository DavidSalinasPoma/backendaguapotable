<?php

namespace App\Models;


// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    //Modelo para la base de datos
    use HasApiTokens, HasFactory, Notifiable;

    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'usuarios';

    // relacion de muchos a uno inversa(muchos a uno)
    public function persona()
    {
        return $this->belongsTo('App\Models\Persona', 'persona_id'); // Recibe a Persona
    }


    /**
     * Campos que se rellena de manera masiva
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Datos que estaran bloqueados de la BD
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relación de muchos a muchos
    public function permisos()
    {
        return $this->belongsToMany('App\models\Permiso');
    }
}
