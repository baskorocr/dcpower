<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warranty_claims', function (Blueprint $table) {
            if (!Schema::hasColumn('warranty_claims', 'motor_type')) {
                $table->string('motor_type')->nullable()->after('complaint_description');
            }
            if (!Schema::hasColumn('warranty_claims', 'has_modification')) {
                $table->boolean('has_modification')->default(false)->after('motor_type');
            }
            if (!Schema::hasColumn('warranty_claims', 'modification_types')) {
                $table->json('modification_types')->nullable()->after('has_modification');
            }
            if (!Schema::hasColumn('warranty_claims', 'whatsapp_number')) {
                $table->string('whatsapp_number')->nullable()->after('modification_types');
            }
            if (!Schema::hasColumn('warranty_claims', 'purchase_type')) {
                $table->enum('purchase_type', ['online', 'offline'])->nullable()->after('whatsapp_number');
            }
            if (!Schema::hasColumn('warranty_claims', 'purchase_date')) {
                $table->date('purchase_date')->nullable()->after('purchase_type');
            }
            if (!Schema::hasColumn('warranty_claims', 'battery_issue_date')) {
                $table->date('battery_issue_date')->nullable()->after('purchase_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('warranty_claims', function (Blueprint $table) {
            $table->dropColumn([
                'motor_type',
                'has_modification',
                'modification_types',
                'whatsapp_number',
                'purchase_type',
                'purchase_date',
                'battery_issue_date'
            ]);
        });
    }
};
