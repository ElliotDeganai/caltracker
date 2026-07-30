<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calorie_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('week_start_date'); // toujours un lundi
            $table->unsignedInteger('target_calories');
            $table->timestamps();

            $table->unique(['user_id', 'week_start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calorie_targets');
    }
};
