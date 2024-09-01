<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_owners', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('user first name and last name');
            $table->string('email');
            $table->string('socket_id')->nullable()->unique()->comment('Owner is online when this column value is not null, otherwise is offline');
            $table->string('session_id')->comment('this column keep user session id for realize user in next times');
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
        Schema::dropIfExists('chat_owners');
    }
};
