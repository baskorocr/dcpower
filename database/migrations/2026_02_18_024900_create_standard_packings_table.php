<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standard_packings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('packing_code')->unique();
            $table->integer('quantity');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('packed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standard_packings');
    }
};
