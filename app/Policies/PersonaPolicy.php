<?php

namespace App\Policies;

use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class PersonaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the Usuario can view any models.
     *
     * @param  \App\Models\Usuario  $usuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(Usuario $usuario)
    {
        //
    }

    /**
     * Determine whether the Usuario can view the model.
     *
     * @param  \App\Models\Usuario  $usuario
     * @param  \App\Models\Persona  $persona
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Usuario $usuario, Persona $persona)
    {
        //
    }

    /**
     * Determine whether the Usuario can create models.
     *
     * @param  \App\Models\Usuario  $usuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Usuario $usuario)
    {
        //
    }

    /**
     * Determine whether the Usuario can update the model.
     *
     * @param  \App\Models\Usuario  $usuario
     * @param  \App\Models\Persona  $persona
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Usuario $usuario, Persona $persona)
    {
        return true;
    }

    /**
     * Determine whether the Usuario can delete the model.
     *
     * @param  \App\Models\Usuario  $usuario
     * @param  \App\Models\Persona  $persona
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Usuario $usuario, Persona $persona)
    {
        //
    }

    /**
     * Determine whether the Usuario can restore the model.
     *
     * @param  \App\Models\Usuario  $usuario
     * @param  \App\Models\Persona  $persona
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Usuario $usuario, Persona $persona)
    {
        //
    }

    /**
     * Determine whether the Usuario can permanently delete the model.
     *
     * @param  \App\Models\Usuario  $usuario
     * @param  \App\Models\Persona  $persona
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(Usuario $usuario, Persona $persona)
    {
        //
    }
}
