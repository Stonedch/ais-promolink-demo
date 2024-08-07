<?php

use App\Models\Form;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_groups', function (Blueprint $table) {
            $table->id();

            $table->text('name');
            $table->text('slug');
            $table->integer('sort')->default(100);

            $table->foreignIdFor(Form::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedBigInteger('parent_id')->nullable();

            $table->foreign('parent_id')
                ->references('id')
                ->on('form_groups')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_groups');
    }
};
