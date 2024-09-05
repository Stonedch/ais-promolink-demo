<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bot_users', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)
                ->nullable()
                ->unique()
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->text('phone')->unique();

            $table->text('telegram_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bot_users');
    }
};
