<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('members_id');
            $table->unsignedInteger('conjugations_id')->nullable(); //only if left on a question
            $table->unsignedInteger('groups_id')->nullable(); //only if left in a group
            $table->text('content');
            $table->timestamps();
            $table->softDeletes();
            $table->index('members_id');
            $table->index('conjugations_id');
            $table->index('groups_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
