<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('immunizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('balita_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('administered_at');
            $table->enum('status', ['recorded', 'needs_review'])->default('recorded');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['balita_id', 'administered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('immunizations');
    }
};
