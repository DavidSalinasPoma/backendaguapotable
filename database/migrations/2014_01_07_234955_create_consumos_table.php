<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsumosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consumos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->bigInteger('lecturaAnterior');
            $table->bigInteger('lecturaActual');
            $table->bigInteger('consumo');
            $table->double('precio');
            $table->string('imagen');
            $table->string('observaciones');
            $table->foreignId('socio_id')->constrained('socios')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('apertura_id')->constrained('aperturas')->onUpdate('cascade')->onDelete('restrict');

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
        Schema::dropIfExists('consumos');
    }
}
