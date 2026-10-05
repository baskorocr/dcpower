<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('q1_riding_experience');
            $table->string('q2_battery_lifespan');
            $table->string('q3_replacement_reason');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_surveys');
    }
};
