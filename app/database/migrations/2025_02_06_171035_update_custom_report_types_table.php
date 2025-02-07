<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchid\Attachment\Models\Attachment;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_report_types', function (Blueprint $table) {
            $table->foreignIdFor(Attachment::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('custom_report_types', function (Blueprint $table) {
            $table->dropForeignIdFor(Attachment::class);
            $table->dropColumn('attachment_id');
        });
    }
};
