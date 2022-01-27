<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personas', function (Blueprint $table) {

            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('carnet')->unique();;
            $table->string('expedito');
            $table->string('nombres');
            $table->string('ap_paterno');
            $table->string('ap_materno')->nullable();
            $table->string('sexo');
            $table->string('direccion');
            $table->string('email');
            $table->string('celular');
            $table->string('celular_familiar');
            $table->string('nacimiento');
            $table->string('estado_civil');
            $table->string('estado')->default(1);
            $table->string('socio')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personas');
    }
}
