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

            $table->foreignId('servicios_id')->constrained('servicios')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('eventos_id')->constrained('eventos')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('consumo_id')->constrained('consumos')->onUpdate('cascade')->onDelete('restrict');

            $table->double('retraso')->default(0);
            $table->double('total');
            $table->dateTime('fecha_limite_pago');

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
