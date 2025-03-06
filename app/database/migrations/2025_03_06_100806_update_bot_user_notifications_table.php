<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bot_user_notifications', function (Blueprint $table) {
            $table->integer('status')->default(100);
            $table->text('status_message')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('bot_user_notifications', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('status_message');
        });
    }
};
