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
        Schema::table('product_trace_logs', function (Blueprint $table) {
            $table->foreignId('scanned_by')->nullable()->after('user_id')->constrained('users');
            $table->string('action')->nullable()->after('event_type');
            $table->text('notes')->nullable()->after('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_trace_logs', function (Blueprint $table) {
            $table->dropForeign(['scanned_by']);
            $table->dropColumn(['scanned_by', 'action', 'notes']);
        });
    }
};
