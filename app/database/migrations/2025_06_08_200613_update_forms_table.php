<?php

use App\Models\Departament;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->boolean('requires_approval')->default(false);
        });

        Schema::table('events', function (Blueprint $table) {

            $table->foreignIdFor(Departament::class, 'approval_departament_id')
                ->nullable()
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn(['requires_approval']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['approval_departament_id']);
        });
    }
};
