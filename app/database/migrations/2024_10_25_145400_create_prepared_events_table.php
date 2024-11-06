<?php

use App\Models\Event;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prepared_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->text('user_fullname');
            $table->text('departament_name');
            $table->text('form_name');
            $table->timestamp('event_created_at');
            $table->timestamp('event_filled_at');
            $table->timestamp('event_refilled_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prepared_events');
    }
};
