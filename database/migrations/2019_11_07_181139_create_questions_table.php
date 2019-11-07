<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('verbs_id');
            $table->unsignedInteger('tenses_id');
            $table->unsignedInteger('persons_id');
            $table->string('english');
            $table->string('notes');
            $table->timestamps();
            $table->softDeletes();
            $table->index('verbs_id');
            $table->index('tenses_id');
            $table->index('persons_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
