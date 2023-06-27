<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees_attendance_records', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->unsignedInteger('hours');
            $table->unsignedInteger('minutes');
            $table->unsignedInteger('time_in_office');
            $table->json('locations');
            $table->date('date');
            $table->string('day_status');
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
        Schema::dropIfExists('employees_attendance_records');
    }
};
