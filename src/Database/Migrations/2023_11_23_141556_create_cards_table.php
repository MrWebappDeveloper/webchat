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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('نام کارت');
            $table->string('shortcut')->unique()->comment('اگر در متن هشتک تایپ کنیم این کارت ارسال می شود.کلمه کلیدی برای جستجوی سریع و آسان');
            $table->integer('order_index')->unique()->nullable()->comment('ترتیب نمایش کارت در لیست کارتها. از این فیلد برای مرتب سازی لیست کارت ها استفاده می شود.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
