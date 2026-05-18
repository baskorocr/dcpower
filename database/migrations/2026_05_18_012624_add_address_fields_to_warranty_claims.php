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
        Schema::table('warranty_claims', function (Blueprint $table) {
            if (!Schema::hasColumn('warranty_claims', 'address')) {
                $table->text('address')->nullable()->after('whatsapp_number');
            }
            if (!Schema::hasColumn('warranty_claims', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('warranty_claims', 'province')) {
                $table->string('province')->nullable()->after('city');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warranty_claims', function (Blueprint $table) {
            $table->dropColumn(['address', 'city', 'province']);
        });
    }
};
