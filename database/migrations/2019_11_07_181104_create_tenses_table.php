<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('moods_id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
            $table->index('moods_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenses');
    }
}
