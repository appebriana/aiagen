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
        Schema::table('knowledge_files', function (Blueprint $table) {
            $table->string('type')->default('file')->after('department_id');
            $table->text('url')->nullable()->after('type');
            $table->string('file_name')->nullable()->change();
            $table->string('file_path')->nullable()->change();
            $table->string('file_type')->nullable()->change();
            $table->integer('file_size')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knowledge_files', function (Blueprint $table) {
            $table->dropColumn(['type', 'url']);
            $table->string('file_name')->nullable(false)->change();
            $table->string('file_path')->nullable(false)->change();
            $table->string('file_type')->nullable(false)->change();
            $table->integer('file_size')->nullable(false)->change();
        });
    }
};
