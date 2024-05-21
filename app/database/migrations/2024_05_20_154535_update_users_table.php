<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->text('phone')->unique();
            $table->text('last_name')->nullable();
            $table->text('first_name')->nullable();
            $table->text('middle_name')->nullable();
            $table->string('email')->nullable()->change();
        });
    }
};
