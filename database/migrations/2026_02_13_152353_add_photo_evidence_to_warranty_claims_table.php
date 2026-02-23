<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warranty_claims', function (Blueprint $table) {
            $table->string('photo_evidence')->nullable()->after('complaint_description');
            $table->string('approved_by')->nullable()->after('handled_by');
            $table->timestamp('approved_at')->nullable()->after('reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('warranty_claims', function (Blueprint $table) {
            $table->dropColumn(['photo_evidence', 'approved_by', 'approved_at']);
        });
    }
};
