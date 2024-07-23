<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departament_types', function (Blueprint $table) {
            if (Schema::hasColumn('departament_types', 'sort') == false) {
                $table->integer('sort')->default(100)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('departament_types', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
};
