<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->enum('status', ['new', 'read', 'replied'])->default('new')->after('message');
        });

        // Update existing records based on is_read
        DB::table('contact_messages')->where('is_read', 1)->update(['status' => 'read']);
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
