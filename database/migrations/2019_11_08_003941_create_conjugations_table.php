<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConjugationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conjugations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('verbs_id');
            $table->unsignedInteger('persons_id');
            $table->unsignedInteger('tenses_id');
            $table->string('spanish');
            $table->string('english')->nullable();
            $table->boolean('is_irregular')->default(0);

            $table->timestamps();
            $table->softDeletes();
            $table->index('verbs_id');
            $table->index('persons_id');
            $table->index('tenses_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conjugations');
    }
}
