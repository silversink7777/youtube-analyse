<?php

namespace App\Repositories;

use App\Models\Video;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\VideoRepositoryInterface;

class VideoRepository implements VideoRepositoryInterface
{
    /**
     * ユーザーの動画を保存
     */
    public function createVideo(array $videoData, int $userId): Video
    {
        return Video::create([
            ...$videoData,
            'user_id' => $userId,
        ]);
    }

    /**
     * ユーザーの既存動画を検索
     */
    public function findExistingVideo(string $youtubeId, int $userId): ?Video
    {
        return Video::where('youtube_id', $youtubeId)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * 動画にコメントを一括保存
     */
    public function saveComments(Video $video, array $comments): void
    {
        foreach ($comments as $comment) {
            $video->comments()->updateOrCreate(
                [
                    'youtube_comment_id' => $comment['youtube_comment_id'],
                ],
                $comment
            );
        }
    }

    /**
     * ユーザーの動画一覧を取得
     */
    public function getUserVideos(int $userId): Collection
    {
        return Video::where('user_id', $userId)
            ->withCount([
                'comments',
                'comments as analyzed_count' => function ($q) {
                    $q->whereNotNull('sentiment_label');
                }
            ])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * 動画IDで動画を取得
     */
    public function findById(int $videoId): Video
    {
        return Video::findOrFail($videoId);
    }

    /**
     * 動画のキーワードを更新
     */
    public function updateKeywords(Video $video, array $keywords): void
    {
        $video->keywords = $keywords;
        $video->save();
    }

    /**
     * 動画のコメントテキストを取得
     */
    public function getCommentTexts(int $videoId): array
    {
        return Video::findOrFail($videoId)
            ->comments()
            ->pluck('text_display')
            ->toArray();
    }
} 