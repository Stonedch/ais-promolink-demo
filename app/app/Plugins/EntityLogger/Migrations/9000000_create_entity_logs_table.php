<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_logs', function (Blueprint $table) {
            $table->id();
            $table->text('message')->nullable();
            $table->text('model')->nullable();
            $table->longText('fields')->nullable();
            $table->text('user')->nullable();
            $table->text('ip')->nullable();
            $table->timestamps();
        });
    }
};
