<?php

use App\Models\DepartamentType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departaments', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->foreignIdFor(DepartamentType::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->nullOnUpdate();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departaments');
    }
};
