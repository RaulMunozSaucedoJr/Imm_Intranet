<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpleadosRHTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empleadosRH', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('NumeroEmpleado')->nullable();
            $table->string('Departamento')->nullable();
            $table->string('Posicion')->nullable();
            $table->string('Empleado')->nullable();
            $table->date('FechaEntrada')->nullable();
            $table->string('Direccion')->nullable();
            $table->string('RFC')->nullable();
            $table->string('Curp')->nullable();
            $table->string('NSS')->nullable();
            $table->string('Banco')->nullable();
            $table->string('CuentaBancaria')->nullable();
            $table->string('ClabeBanco')->nullable();
            $table->string('Correo')->nullable();
            $table->date('FechaNacimiento')->nullable();
            $table->string('Sede')->nullable();
            $table->string('Estatus')->nullable();
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
        Schema::dropIfExists('empleadosRH');
    }
}
