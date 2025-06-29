<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vulnerabilities', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('description');
            $table->text('bdu');
            $table->text('cve');
            $table->text('vector');
            $table->text('grade');
            $table->text('elimination');
            $table->timestamps();
        });
    }
};
