<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('Empleado')->nullable();
            $table->string('Fecha')->nullable();
            $table->string('TipoProblema')->nullable();
            $table->string('Sede')->nullable();
            $table->string('Prioridad')->nullable();
            $table->string('PeriodoDeTiempo')->nullable();
            $table->string('TipoPeriodo')->nullable();
            $table->string('Locacion')->nullable();
            $table->string('Piso')->nullable();
            $table->string('Departamento')->nullable();
            $table->string('Sistema')->nullable();
            $table->string('ModuloReferencia')->nullable();
            $table->string('Descripcion')->nullable();
            $table->string('Evidencia')->nullable();
            $table->string('Comentario')->nullable();
            $table->integer('Estatus')->nullable();
            $table->string('RazonRechazo')->nullable();
            $table->string('Calificacion')->nullable();
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
        Schema::dropIfExists('tickets');
    }
}
