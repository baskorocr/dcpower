<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, allow nullable temporarily
        DB::statement("ALTER TABLE product_trace_logs MODIFY COLUMN event_type VARCHAR(50) DEFAULT NULL");
        
        // Update any invalid event_type
        DB::table('product_trace_logs')->whereNotIn('event_type', ['manufactured', 'sold', 'claimed', 'scanned'])->update(['event_type' => 'scanned']);
        
        // Now set the enum
        DB::statement("ALTER TABLE product_trace_logs MODIFY COLUMN event_type ENUM('manufactured', 'stock_out', 'sold', 'claimed', 'scanned') DEFAULT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE product_trace_logs MODIFY COLUMN event_type ENUM('manufactured', 'sold', 'claimed', 'scanned') DEFAULT NULL");
    }
};
