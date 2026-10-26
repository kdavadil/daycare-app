<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('category');
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('meal_amount')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('photo_original_name')->nullable();
            $table->timestamp('occurred_at');
            $table->string('status')->default('published');
            $table->timestamps();

            $table->index(['school_id', 'school_class_id', 'occurred_at']);
            $table->index(['child_id', 'status', 'occurred_at']);
            $table->index(['author_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
