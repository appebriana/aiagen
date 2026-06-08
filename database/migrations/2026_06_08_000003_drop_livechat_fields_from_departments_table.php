<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // Drop unique index first if using SQLite or if needed
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropUnique(['livechat_token']);
            } else {
                // SQLite drops indexes automatically on dropColumn in newer versions, but has index bugs if not dropped.
                // Let's drop index manually.
                $table->dropUnique('departments_livechat_token_unique');
            }
            $table->dropColumn(['livechat_token', 'livechat_active', 'livechat_primary_color', 'livechat_welcome_message']);
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->string('livechat_token')->nullable()->unique()->after('ai_job_description');
            $table->boolean('livechat_active')->default(false)->after('livechat_token');
            $table->string('livechat_primary_color')->default('#4f46e5')->after('livechat_active');
            $table->text('livechat_welcome_message')->nullable()->after('livechat_primary_color');
        });
    }
};
