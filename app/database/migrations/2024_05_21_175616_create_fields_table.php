<?php

use App\Models\Collection;
use App\Models\Form;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Form::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->text('name');
            $table->text('group')->nullable();
            $table->integer('type');
            $table->integer('sort')->default(100);
            $table->foreignIdFor(Collection::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->nullOnUpdate();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
