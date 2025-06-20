<?php

namespace App\Repositories\Interfaces;

use App\Models\Video;
use Illuminate\Database\Eloquent\Collection;

interface VideoRepositoryInterface
{
    /**
     * ユーザーの動画を保存
     */
    public function createVideo(array $videoData, int $userId): Video;

    /**
     * ユーザーの既存動画を検索
     */
    public function findExistingVideo(string $youtubeId, int $userId): ?Video;

    /**
     * 動画にコメントを一括保存
     */
    public function saveComments(Video $video, array $comments): void;

    /**
     * ユーザーの動画一覧を取得
     */
    public function getUserVideos(int $userId): Collection;

    /**
     * 動画IDで動画を取得
     */
    public function findById(int $videoId): Video;

    /**
     * 動画のキーワードを更新
     */
    public function updateKeywords(Video $video, array $keywords): void;

    /**
     * 動画のコメントテキストを取得
     */
    public function getCommentTexts(int $videoId): array;
} 