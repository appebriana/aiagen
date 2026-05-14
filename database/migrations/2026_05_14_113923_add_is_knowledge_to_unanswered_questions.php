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
        Schema::table('unanswered_questions', function (Blueprint $table) {
            $table->boolean('is_knowledge')->default(false)->after('is_answered');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unanswered_questions', function (Blueprint $table) {
            $table->dropColumn('is_knowledge');
        });
    }
};
