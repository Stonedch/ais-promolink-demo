<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_categories', function (Blueprint $table) {
            $table->integer('sort')->nullable()->default(100);
        });
    }

    public function down(): void
    {
        Schema::table('form_categories', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
};
