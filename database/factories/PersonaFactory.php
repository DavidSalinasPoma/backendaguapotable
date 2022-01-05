<?php

namespace Database\Factories;

use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonaFactory extends Factory
{

    protected $model = Persona::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'carnet' => $this->faker->Str::random_int(6),
            'expedito' => $this->faker,
            'nombres' => $this->faker,
            'ap_paterno' => $this->faker,
            'ap_materno' => $this->faker,
            'sexo' =>       $this->faker,
            'direccion' => $this->faker,
            'email' => $this->faker,
            'celular' => $this->faker,
            'celular_familiar' => $this->faker,
            'nacimiento' => $this->faker,
            'estado_civil' => $this->faker,

        ];
    }
}
