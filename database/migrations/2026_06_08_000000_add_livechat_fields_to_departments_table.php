<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->string('livechat_token')->nullable()->unique()->after('ai_job_description');
            $table->boolean('livechat_active')->default(false)->after('livechat_token');
            $table->string('livechat_primary_color')->default('#4f46e5')->after('livechat_active');
            $table->text('livechat_welcome_message')->nullable()->after('livechat_primary_color');
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['livechat_token', 'livechat_active', 'livechat_primary_color', 'livechat_welcome_message']);
        });
    }
};
