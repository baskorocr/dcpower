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
            // Add motor_year field
            if (!Schema::hasColumn('warranty_claims', 'motor_year')) {
                $table->integer('motor_year')->nullable()->after('motor_type');
            }
            
            // Drop old modification_types JSON column
            if (Schema::hasColumn('warranty_claims', 'modification_types')) {
                $table->dropColumn('modification_types');
            }
            
            // Add new modification_type (single selection) and modification_other
            if (!Schema::hasColumn('warranty_claims', 'modification_type')) {
                $table->string('modification_type')->nullable()->after('has_modification');
            }
            if (!Schema::hasColumn('warranty_claims', 'modification_other')) {
                $table->string('modification_other')->nullable()->after('modification_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warranty_claims', function (Blueprint $table) {
            $table->dropColumn(['motor_year', 'modification_type', 'modification_other']);
            $table->json('modification_types')->nullable()->after('has_modification');
        });
    }
};
