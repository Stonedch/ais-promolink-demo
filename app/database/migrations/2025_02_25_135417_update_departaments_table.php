<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departaments', function (Blueprint $table) {
            $table->longText('polygon')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('departaments', function (Blueprint $table) {
            $table->dropColumn('polygon');
        });
    }
};
