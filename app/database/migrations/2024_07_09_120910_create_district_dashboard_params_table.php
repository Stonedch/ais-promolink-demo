<?php

use App\Models\District;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('district_dashboard_params', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(District::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->text('name');
            $table->text('value');
            $table->integer('sort')->default(100);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('district_dashboard_params');
    }
};
