<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('rounds_id')->nullable();
            $table->unsignedInteger('conjugations_id');
            $table->string('answer');
            $table->unsignedTinyInteger('score');
            $table->decimal('seconds_elapsed');
            $table->timestamps();
            $table->softDeletes();
            $table->index('rounds_id');
            $table->index('conjugations_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answers');
    }
}
