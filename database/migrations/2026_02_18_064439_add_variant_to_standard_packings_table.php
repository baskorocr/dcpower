<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('standard_packings', function (Blueprint $table) {
            $table->string('variant')->nullable()->after('project_id');
        });
    }

    public function down(): void
    {
        Schema::table('standard_packings', function (Blueprint $table) {
            $table->dropColumn('variant');
        });
    }
};
