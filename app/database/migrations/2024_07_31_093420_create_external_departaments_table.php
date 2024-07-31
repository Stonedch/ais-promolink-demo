<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_departaments', function (Blueprint $table) {
            $table->id();

            $table->text('orgname');
            $table->text('orgsokrname')->nullable();
            $table->text('orgpubname')->nullable();
            $table->text('type')->nullable();
            $table->text('post')->nullable();
            $table->text('rukfio')->nullable();
            $table->text('orgfunc')->nullable();
            $table->text('index')->nullable();
            $table->text('region')->nullable();
            $table->text('area')->nullable();
            $table->text('town')->nullable();
            $table->text('street')->nullable();
            $table->text('house')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->text('mail')->nullable();
            $table->text('telephone')->nullable();
            $table->text('fax')->nullable();
            $table->text('telephonedop')->nullable();
            $table->text('url')->nullable();
            $table->text('okpo')->nullable();
            $table->text('ogrn')->nullable();
            $table->text('inn')->nullable();
            $table->text('schedule')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_departaments');
    }
};
