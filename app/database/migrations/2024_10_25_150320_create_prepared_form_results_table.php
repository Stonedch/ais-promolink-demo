<?php

use App\Models\Field;
use App\Models\PreparedEvent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prepared_form_results', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PreparedEvent::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedBigInteger('field_id')->nullable();
            $table->text('row_key_structure')->nullable();
            $table->text('row_key_first')->nullable();
            $table->text('group_key_structure')->nullable();
            $table->text('key');
            $table->text('value');
            $table->integer('index')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prepared_form_results');
    }
};
