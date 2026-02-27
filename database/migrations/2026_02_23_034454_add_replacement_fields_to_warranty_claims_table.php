<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warranty_claims', function (Blueprint $table) {
            $table->foreignId('replaced_by')->nullable()->after('handled_by')->constrained('users');
            $table->foreignId('replacement_product_id')->nullable()->after('replaced_by')->constrained('products');
            $table->timestamp('replaced_at')->nullable()->after('resolved_at');
        });
    }

    public function down(): void
    {
        Schema::table('warranty_claims', function (Blueprint $table) {
            $table->dropForeign(['replaced_by']);
            $table->dropForeign(['replacement_product_id']);
            $table->dropColumn(['replaced_by', 'replacement_product_id', 'replaced_at']);
        });
    }
};
