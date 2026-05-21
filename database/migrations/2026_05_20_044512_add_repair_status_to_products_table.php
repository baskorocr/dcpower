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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('can_repair')->default(false)->after('status');
            $table->unsignedBigInteger('repair_distributor_id')->nullable()->after('can_repair');
            $table->text('repair_notes')->nullable()->after('repair_distributor_id');
            $table->timestamp('repair_sent_at')->nullable()->after('repair_notes');
            
            $table->foreign('repair_distributor_id')->references('id')->on('distributors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['repair_distributor_id']);
            $table->dropColumn(['can_repair', 'repair_distributor_id', 'repair_notes', 'repair_sent_at']);
        });
    }
};
