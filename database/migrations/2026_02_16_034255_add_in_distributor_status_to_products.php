<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update any invalid status first
        DB::table('products')->whereNotIn('status', ['manufactured', 'sold', 'claimed', 'in_transit'])->update(['status' => 'manufactured']);
        
        DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('manufactured', 'in_distributor', 'sold', 'claimed', 'in_transit') DEFAULT 'manufactured'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('manufactured', 'sold', 'claimed', 'in_transit') DEFAULT 'manufactured'");
    }
};
