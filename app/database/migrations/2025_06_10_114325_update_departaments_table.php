<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departaments', function (Blueprint $table) {
            $table->text('phone')->nullable();
            $table->text('contact_fullname')->nullable();
            $table->text('email')->nullable();
            $table->text('email_fullname')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('departaments', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('contact_fullname');
            $table->dropColumn('email');
            $table->dropColumn('email_fullname');
        });
    }
};
