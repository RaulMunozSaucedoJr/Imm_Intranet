<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservacionSalasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservacion-salas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('Nombre')->nullable();
            $table->string('Sala')->nullable();
            $table->date('Fecha Inicial')->nullable();
            $table->date('Fecha Final')->nullable();
            $table->time('Hora Inicial')->nullable();
            $table->time('Hora Final')->nullable();
            $table->string('Descripcion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservacion-salas');
    }
}
