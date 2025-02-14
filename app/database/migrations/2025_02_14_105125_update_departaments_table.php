<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departaments', function (Blueprint $table) {
            $table->text('dadata')->nullable();
            $table->text('address')->nullable();
            $table->float('lat')->nullable();
            $table->float('lon')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('departaments', function (Blueprint $table) {
            $table->dropColumn('dadata');
            $table->dropColumn('address');
            $table->dropColumn('lat');
            $table->dropColumn('lon');
        });
    }
};
