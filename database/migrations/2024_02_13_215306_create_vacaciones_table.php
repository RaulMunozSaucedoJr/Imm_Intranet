<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVacacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacaciones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('employee')->nullable();
            $table->string('boss')->nullable();
            $table->string('initialDate')->nullable();
            $table->string('finalDate')->nullable();
            $table->string('extraInitialDate')->nullable();
            $table->string('extraFinalDate')->nullable();
            $table->string('motive')->nullable();
            $table->string('totalDays')->nullable();
            $table->string('color')->nullable();
            $table->string('daysDifference')->nullable();
            $table->string('remainingDays')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('vacaciones');
    }
}
