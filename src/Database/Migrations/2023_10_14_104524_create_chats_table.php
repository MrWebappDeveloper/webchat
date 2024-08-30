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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->string('channel')->unique();
            $table->string('token')->unique();
            $table->boolean('connected_to_operator')->default(false)
                ->comment('اگر این فیلد true باشد کاربر به کارشناس متصل شده و با او گفتگو می کند , در غیر این صورت منوی ویزارد ها را می بیند');
            $table->foreignId('chat_owner_id')->unique()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('chats');
    }
};
