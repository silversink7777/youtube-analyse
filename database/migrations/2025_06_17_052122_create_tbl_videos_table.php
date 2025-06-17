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
        Schema::create('tbl_videos', function (Blueprint $table) {
            $table->id();
            $table->string('youtube_id')->unique()->comment('YouTube動画ID');
            $table->string('title')->comment('動画タイトル');
            $table->text('description')->nullable()->comment('動画説明');
            $table->string('channel_title')->comment('チャンネル名');
            $table->string('channel_id')->comment('チャンネルID');
            $table->string('thumbnail_url')->nullable()->comment('サムネイルURL');
            $table->string('thumbnail_medium_url')->nullable()->comment('中サイズサムネイルURL');
            $table->string('thumbnail_high_url')->nullable()->comment('高解像度サムネイルURL');
            $table->integer('view_count')->default(0)->comment('視聴回数');
            $table->integer('like_count')->default(0)->comment('いいね数');
            $table->integer('comment_count')->default(0)->comment('コメント数');
            $table->timestamp('published_at')->nullable()->comment('投稿日時');
            $table->string('duration')->nullable()->comment('動画の長さ');
            $table->string('tags')->nullable()->comment('タグ（JSON形式）');
            $table->string('category_id')->nullable()->comment('カテゴリID');
            $table->string('default_language')->nullable()->comment('デフォルト言語');
            $table->string('default_audio_language')->nullable()->comment('デフォルト音声言語');
            $table->boolean('is_public')->default(true)->comment('公開状態');
            $table->boolean('is_analyzed')->default(false)->comment('分析済みフラグ');
            $table->timestamp('last_analyzed_at')->nullable()->comment('最終分析日時');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('ユーザーID');
            $table->timestamps();
            
            // インデックス
            $table->index(['youtube_id']);
            $table->index(['user_id']);
            $table->index(['is_analyzed']);
            $table->index(['published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_videos');
    }
};
