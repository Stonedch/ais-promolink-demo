<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->foreignId('group_id')
                ->nullable()
                ->constrained('form_groups')
                ->nullOnDelete()
                ->nullOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
        });
    }
};
