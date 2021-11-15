<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->unsignedBigInteger('id');
            // $table->unsignedBigInteger('persona_id'); // Relación con persona
            // $table->foreign('persona_id')->references('id')->on('personas');
            $table->foreignId('persona_id')->constrained('personas')->onUpdate('cascade')->onDelete('restrict');
            $table->string('username');
            $table->string('password');
            $table->integer('estado')->default(1);
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
        Schema::dropIfExists('usuarios');
    }
}
