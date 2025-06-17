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
        Schema::create('tbl_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained('tbl_videos')->onDelete('cascade')->comment('動画ID');
            $table->string('youtube_comment_id')->comment('YouTubeコメントID');
            $table->string('author_display_name')->nullable()->comment('投稿者名');
            $table->string('author_channel_id')->nullable()->comment('投稿者チャンネルID');
            $table->text('text_display')->comment('コメント本文');
            $table->integer('like_count')->default(0)->comment('いいね数');
            $table->timestamp('published_at')->nullable()->comment('投稿日時');
            $table->timestamp('updated_at_youtube')->nullable()->comment('YouTube上の更新日時');
            $table->boolean('is_public')->default(true)->comment('公開状態');
            $table->timestamps();
            $table->unique(['video_id', 'youtube_comment_id']);
            $table->index(['video_id']);
            $table->index(['published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_comments');
    }
};
