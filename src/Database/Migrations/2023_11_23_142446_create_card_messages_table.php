<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('card_messages', function (Blueprint $table) {
            $table->id();
            $table->json('content')->comment('محتوای پیام و نوع آن');
            $table->integer('send_order_index')->nullable()->comment("ترتیب ارسال مسیج ها که اول مسیج 1 کارت ارسال می شود و بعد مسیج 2 کارت ارسال می گردد");
            $table->foreignId('card_id')->comment('آیدی کارتی که پیام متعلق به آن است')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_messages');
    }
};
