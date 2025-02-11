<?php

use App\Models\CustomReportType;
use App\Models\Departament;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_report_datas', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Departament::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignIdFor(User::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignIdFor(CustomReportType::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->integer('page')->nullable();
            $table->integer('row')->nullable();
            $table->integer('column')->nullable();
            $table->text('value');
            $table->text('type');
            $table->timestamp('loaded_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_report_datas');
    }
};
