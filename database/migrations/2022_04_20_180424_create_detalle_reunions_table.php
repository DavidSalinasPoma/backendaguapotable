<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleReunionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_reunions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');

            $table->foreignId('socio_id')->constrained('socios')->onUpdate('cascade')->onDelete('restrict');

            $table->foreignId('reunion_id')->constrained('reuniones')->onUpdate('cascade')->onDelete('restrict');

            $table->string('opcion')->default('');;

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
        Schema::dropIfExists('detalle_reunions');
    }
}
