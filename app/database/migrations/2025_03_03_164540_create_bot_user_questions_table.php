<?php

use App\Models\BotUser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bot_user_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(BotUser::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->text('question');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bot_user_questions');
    }
};
