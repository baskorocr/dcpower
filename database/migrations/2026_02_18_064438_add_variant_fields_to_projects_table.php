<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('use_variants')->default(false)->after('standard_packing_quantity');
            $table->json('variants')->nullable()->after('use_variants');
            $table->string('packing_format')->default('PACK-{RANDOM}')->after('variants');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['use_variants', 'variants', 'packing_format']);
        });
    }
};
