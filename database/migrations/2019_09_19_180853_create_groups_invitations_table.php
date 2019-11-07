<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups_invitations', function (Blueprint $table) {
            $table->increments('id');            
            $table->unsignedInteger('groups_id');
            $table->unsignedInteger('inviter_members_id');                        
            $table->string('email');
            $table->enum('status', ['pending', 'accepted', 'expired'])->default('pending');
            $table->enum('groups_role', ['teacher', 'assistant', 'student'])->default('student');
            $table->string('token',40);            
            $table->timestamps();
            $table->softDeletes();
            $table->index('groups_id');
            $table->index('inviter_members_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups_invitations');
    }
}
