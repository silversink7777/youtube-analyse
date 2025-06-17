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
        Schema::create('tbl_analysis_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained('tbl_videos')->onDelete('cascade')->comment('動画ID');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('ユーザーID');
            $table->string('status')->default('pending')->comment('分析ステータス: pending, processing, completed, failed');
            $table->integer('total_comments')->default(0)->comment('総コメント数');
            $table->integer('processed_comments')->default(0)->comment('処理済みコメント数');
            $table->json('analysis_results')->nullable()->comment('分析結果（JSON形式）');
            $table->text('error_message')->nullable()->comment('エラーメッセージ');
            $table->timestamp('started_at')->nullable()->comment('分析開始時刻');
            $table->timestamp('completed_at')->nullable()->comment('分析完了時刻');
            $table->timestamps();
            
            // インデックス
            $table->index(['video_id']);
            $table->index(['user_id']);
            $table->index(['status']);
            $table->index(['started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_analysis_sessions');
    }
};
