<?php

namespace Database\Seeders;

use App\Models\Persona;
use Illuminate\Database\Seeder;

class PersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $persona = new Persona();

        $persona->carnet = "5406766";
        $persona->expedito = "CB";
        $persona->nombres = "Janet";
        $persona->ap_paterno = "Salinas";
        $persona->ap_materno = "Poma";
        $persona->sexo = "Masculino";
        $persona->direccion = "Av. Cocpacabana";
        $persona->email = "davidsalinasdev@gmail.com";
        $persona->celular = "76931047";
        $persona->celular_familiar = "44733282";
        $persona->nacimiento = "Cochabamba";
        $persona->estado_civil = "Soltero";

        $persona->save();

        $persona = new Persona();

        $persona->carnet = "9406766";
        $persona->expedito = "CB";
        $persona->nombres = "David";
        $persona->ap_paterno = "Salinas";
        $persona->ap_materno = "Poma";
        $persona->sexo = "Masculino";
        $persona->direccion = "Av. Cocpacabana";
        $persona->email = "davidsalinasdev@gmail.com";
        $persona->celular = "76931047";
        $persona->celular_familiar = "44733282";
        $persona->nacimiento = "Cochabamba";
        $persona->estado_civil = "Soltero";

        $persona->save();

        $persona = new Persona();

        $persona->carnet = "7406766";
        $persona->expedito = "CB";
        $persona->nombres = "Karen";
        $persona->ap_paterno = "Soliz";
        $persona->ap_materno = "Poma";
        $persona->sexo = "Masculino";
        $persona->direccion = "Av. Cocpacabana";
        $persona->email = "davidsalinasdev@gmail.com";
        $persona->celular = "76931047";
        $persona->celular_familiar = "44733282";
        $persona->nacimiento = "Cochabamba";
        $persona->estado_civil = "Soltero";

        $persona->save();
    }
}
