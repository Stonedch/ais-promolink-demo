<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchid\Attachment\Models\Attachment;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignIdFor(Attachment::class)
                ->nullable()
                ->constrained()
                ->updateOnDelete()
                ->updateOnDelete();
        });
    }
};
