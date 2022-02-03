<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalles', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->foreignId('factura_id')->constrained('facturas')->onUpdate('cascade')->onDelete('restrict');
            $table->bigIncrements('num_detalle');

            $table->foreignId('producto_id')->constrained('productos')->onUpdate('cascade')->onDelete('restrict');

            $table->double('precio');

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
        Schema::dropIfExists('detalles');
    }
}
