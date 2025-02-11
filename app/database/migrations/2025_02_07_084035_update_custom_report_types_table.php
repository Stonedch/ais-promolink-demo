<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_report_types', function (Blueprint $table) {
            $table->boolean('is_freelance')->default(false);
            $table->text('command')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('custom_report_types', function (Blueprint $table) {
            $table->dropColumn('is_freelance');
            $table->dropColumn('command');
        });
    }
};
