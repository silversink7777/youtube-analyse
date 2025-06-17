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
        Schema::table('tbl_videos', function (Blueprint $table) {
            $table->text('keywords')->nullable()->comment('抽出キーワード（JSON形式）')->after('last_analyzed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_videos', function (Blueprint $table) {
            $table->dropColumn('keywords');
        });
    }
}; 