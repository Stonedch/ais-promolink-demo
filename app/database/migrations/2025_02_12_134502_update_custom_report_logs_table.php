<?php

use App\Models\CustomReport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_report_logs', function (Blueprint $table) {
            $table->foreignIdFor(CustomReport::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('custom_report_logs', function (Blueprint $table) {
            $table->dropForeignIdFor(CustomReport::class);
            $table->dropColumn('custom_report_id');
        });
    }
};
