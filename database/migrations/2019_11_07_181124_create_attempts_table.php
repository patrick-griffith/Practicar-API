<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attempts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rounds_id')->nullable();
            $table->unsignedInteger('questions_id');
            $table->string('answer');
            $table->unsignedTinyInteger('score');
            $table->decimal('seconds_elapsed');
            $table->timestamps();
            $table->softDeletes();
            $table->index('rounds_id');
            $table->index('questions_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attempts');
    }
}
