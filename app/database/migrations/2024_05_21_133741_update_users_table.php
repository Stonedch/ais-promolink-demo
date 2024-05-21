<?php

use App\Models\Departament;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignIdFor(Departament::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->nullOnupdate();
        });
    }
};
