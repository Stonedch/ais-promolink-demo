<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();

            $table->text('name');

            $table->integer('periodicity')->nullable();
            $table->integer('periodicity_step')->nullable();
            $table->integer('deadline')->nullable();
            $table->integer('type')->nullable();

            $table->boolean('is_active')->nullable()->default(false);
            $table->boolean('is_editable')->nullable()->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
