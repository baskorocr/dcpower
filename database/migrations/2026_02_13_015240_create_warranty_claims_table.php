<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('claimed_by_user_id')->constrained('users');
            $table->foreignId('distributor_id')->nullable()->constrained();
            $table->foreignId('handled_by')->nullable()->constrained('users');
            $table->string('claim_number')->unique();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->enum('complaint_type', ['defect', 'damage', 'malfunction', 'other']);
            $table->text('complaint_description');
            $table->text('defect_notes')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranty_claims');
    }
};
