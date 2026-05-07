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
        Schema::table('departments', function (Blueprint $table) {
            $table->string('ai_name')->nullable()->after('name');
            $table->text('ai_job_description')->nullable()->after('ai_name');
            $table->boolean('reply_to_groups')->default(false)->after('ai_job_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['ai_name', 'ai_job_description', 'reply_to_groups']);
        });
    }
};
