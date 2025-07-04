<?php

use App\Models\FormCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->foreignIdFor(FormCategory::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->nullOnUpdate();
        });
    }
};
