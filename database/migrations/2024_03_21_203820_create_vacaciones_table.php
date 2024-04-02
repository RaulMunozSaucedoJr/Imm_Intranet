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
            $table->string('area')->nullable();
            $table->string('Jefe')->nullable();
            $table->date('initialDate')->nullable();
            $table->date('finalDate')->nullable();
            $table->date('extraInitialDate')->nullable();
            $table->date('extraFinalDate')->nullable();
            $table->date('extraordinaryDay1')->nullable();
            $table->date('extraordinaryDay2')->nullable();
            $table->string('totalDays')->nullable();
            $table->string('color')->nullable();
            $table->string('daysDifference')->nullable();
            $table->string('remainingDays')->nullable();
            $table->string('rejectionReason')->nullable();
            $table->string('user_id')->nullable();
            $table->string('hollidayPeriods')->nullable();
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
        Schema::dropIfExists('vacaciones');
    }
}
