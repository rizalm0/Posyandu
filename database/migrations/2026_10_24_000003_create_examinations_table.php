<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posyandu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('balita_id')->constrained()->cascadeOnDelete();
            $table->date('examined_at');
            $table->decimal('weight_kg', 5, 2);
            $table->decimal('height_cm', 5, 1)->nullable();
            $table->decimal('head_circumference_cm', 5, 1)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['balita_id', 'examined_at']);
            $table->index(['posyandu_id', 'examined_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examinations');
    }
};
