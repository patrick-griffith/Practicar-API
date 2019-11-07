<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members_groups', function (Blueprint $table) {            

            $table->increments('id');
            $table->unsignedInteger('members_id');
            $table->unsignedInteger('groups_id');
            $table->enum('role', ['teacher', 'assistant', 'student'])->default('student');
            $table->boolean('is_admin')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index('members_id');
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
        Schema::dropIfExists('members_groups');
    }
}
