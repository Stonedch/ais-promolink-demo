<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ibkb_articles', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('author_id');
            $table->unsignedBigInteger('system_id')->nullable();
            $table->integer('status')->default(100);
            $table->text('tags')->nullable();

            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('ibkb_articles');
            $table->foreign('author_id')->references('id')->on('users');
            $table->foreign('system_id')->references('id')->on('ibkb_information_systems');
        });
    }

    public function down(): void
    {
        Schema::drop('ibkb_articles');
    }
};
