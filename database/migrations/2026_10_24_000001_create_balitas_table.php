<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posyandu_id')->constrained()->cascadeOnDelete();
            $table->string('registration_number')->unique();
            $table->string('full_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['L', 'P']);
            $table->string('parent_name');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->date('registered_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['posyandu_id', 'is_active']);
            $table->index(['posyandu_id', 'full_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balitas');
    }
};
