<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained('warranty_claims')->onDelete('cascade');
            $table->foreignId('actor_user_id')->constrained('users');
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('notes')->nullable();
            $table->timestamp('acted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_histories');
    }
};
