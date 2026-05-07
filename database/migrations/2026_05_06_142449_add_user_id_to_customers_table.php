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
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id')->nullable();
            
            // Hapus index unik lama pada phone saja
            $table->dropUnique(['phone']);
            
            // Tambahkan index unik baru (Kombinasi User + Phone)
            $table->unique(['user_id', 'phone']);
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id', 'phone']);
            $table->unique(['phone']);
            $table->dropColumn('user_id');
        });
    }
};
