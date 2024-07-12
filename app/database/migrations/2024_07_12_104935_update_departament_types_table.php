<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departament_types', function (Blueprint $table) {
            $table->boolean('show_minister_view')->default(true)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('departament_types', function (Blueprint $table) {
            $table->dropColumn('show_minister_view');
        });
    }
};
