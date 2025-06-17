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
        Schema::table('tbl_comments', function (Blueprint $table) {
            $table->float('sentiment_score')->nullable()->after('is_public')->comment('感情スコア');
            $table->string('sentiment_label')->nullable()->after('sentiment_score')->comment('感情ラベル');
            $table->json('sentiment_json')->nullable()->after('sentiment_label')->comment('感情分析APIの生データ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_comments', function (Blueprint $table) {
            $table->dropColumn(['sentiment_score', 'sentiment_label', 'sentiment_json']);
        });
    }
};
