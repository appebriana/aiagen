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
        Schema::table('ai_chat_logs', function (Blueprint $table) {
            $table->string('sentiment', 20)->nullable()->after('rating');
            $table->boolean('is_resolved')->nullable()->after('sentiment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_chat_logs', function (Blueprint $table) {
            $table->dropColumn(['sentiment', 'is_resolved']);
        });
    }
};
