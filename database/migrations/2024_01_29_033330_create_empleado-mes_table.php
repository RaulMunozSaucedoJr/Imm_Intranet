<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpleadoMesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empleado-mes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('Nombre')->nullable();
            $table->date('Fecha')->nullable();
            $table->string('Descripcion')->nullable();
            $table->string('Imagen')->nullable();
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
        Schema::dropIfExists('empleado-mes');
    }
}
