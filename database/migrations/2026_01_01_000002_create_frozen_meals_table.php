<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('frozen_meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('calories_per_portion');
            $table->unsignedInteger('portions');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('frozen_meals');
    }
};
