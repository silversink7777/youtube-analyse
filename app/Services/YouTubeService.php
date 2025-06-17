<?php

namespace App\Services;

use Google_Client;
use Google_Service_YouTube;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class YouTubeService
{
    private Google_Service_YouTube $youtubeService;
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.youtube.api_key');
        
        if (empty($this->apiKey)) {
            throw new Exception('YouTube API key is not configured');
        }

        $client = new Google_Client();
        $client->setDeveloperKey($this->apiKey);
        $client->setApplicationName(config('services.youtube.application_name', 'YouTube Comment Analyzer'));
        
        $this->youtubeService = new Google_Service_YouTube($client);
    }

    /**
     * YouTube動画IDから動画情報を取得
     */
    public function getVideoInfo(string $videoId): array
    {
        $cacheKey = "youtube_video_{$videoId}";
        
        // キャッシュから取得を試行
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = $this->youtubeService->videos->listVideos('snippet,statistics,contentDetails', [
                'id' => $videoId
            ]);

            if (empty($response->items)) {
                throw new Exception("Video not found: {$videoId}");
            }

            $video = $response->items[0];
            $snippet = $video->snippet;
            $statistics = $video->statistics;
            $contentDetails = $video->contentDetails;

            $videoInfo = [
                'youtube_id' => $videoId,
                'title' => $snippet->title,
                'description' => $snippet->description,
                'channel_title' => $snippet->channelTitle,
                'channel_id' => $snippet->channelId,
                'thumbnail_url' => $snippet->thumbnails->default->url ?? null,
                'thumbnail_medium_url' => $snippet->thumbnails->medium->url ?? null,
                'thumbnail_high_url' => $snippet->thumbnails->high->url ?? null,
                'view_count' => (int) ($statistics->viewCount ?? 0),
                'like_count' => (int) ($statistics->likeCount ?? 0),
                'comment_count' => (int) ($statistics->commentCount ?? 0),
                'published_at' => $snippet->publishedAt,
                'duration' => $contentDetails->duration ?? null,
                'tags' => $snippet->tags ?? [],
                'category_id' => $snippet->categoryId ?? null,
                'default_language' => $snippet->defaultLanguage ?? null,
                'default_audio_language' => $snippet->defaultAudioLanguage ?? null,
                'is_public' => $snippet->privacyStatus === 'public',
            ];

            // 1時間キャッシュ
            Cache::put($cacheKey, $videoInfo, 3600);

            return $videoInfo;

        } catch (Exception $e) {
            Log::error('YouTube API Error: ' . $e->getMessage(), [
                'video_id' => $videoId,
                'error' => $e->getMessage()
            ]);
            throw new Exception('動画情報の取得に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * YouTube URLから動画IDを抽出
     */
    public function extractVideoId(string $url): string
    {
        $patterns = [
            '/[?&]v=([^&]+)/',
            '/youtu\.be\/([^?]+)/',
            '/embed\/([^?]+)/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        throw new Exception('有効なYouTube動画URLではありません');
    }

    /**
     * YouTube URLが有効かどうかを検証
     */
    public function isValidYoutubeUrl(string $url): bool
    {
        $patterns = [
            '/^https?:\/\/(www\.)?youtube\.com\/watch\?v=[\w-]+/',
            '/^https?:\/\/youtu\.be\/[\w-]+/',
            '/^https?:\/\/(www\.)?youtube\.com\/embed\/[\w-]+/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 動画のコメント数を取得
     */
    public function getCommentCount(string $videoId): int
    {
        try {
            $response = $this->youtubeService->videos->listVideos('statistics', [
                'id' => $videoId
            ]);

            if (!empty($response->items)) {
                return (int) ($response->items[0]->statistics->commentCount ?? 0);
            }

            return 0;
        } catch (Exception $e) {
            Log::error('Failed to get comment count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * 動画の基本情報のみを取得（軽量版）
     */
    public function getVideoBasicInfo(string $videoId): array
    {
        $cacheKey = "youtube_video_basic_{$videoId}";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = $this->youtubeService->videos->listVideos('snippet', [
                'id' => $videoId
            ]);

            if (empty($response->items)) {
                throw new Exception("Video not found: {$videoId}");
            }

            $snippet = $response->items[0]->snippet;

            $basicInfo = [
                'youtube_id' => $videoId,
                'title' => $snippet->title,
                'channel_title' => $snippet->channelTitle,
                'thumbnail_url' => $snippet->thumbnails->medium->url ?? null,
                'published_at' => $snippet->publishedAt,
            ];

            // 30分キャッシュ
            Cache::put($cacheKey, $basicInfo, 1800);

            return $basicInfo;

        } catch (Exception $e) {
            Log::error('YouTube API Error (Basic Info): ' . $e->getMessage());
            throw new Exception('動画の基本情報取得に失敗しました');
        }
    }

    /**
     * YouTube動画IDからコメントを一括取得
     * @param string $videoId
     * @param int $maxResults
     * @return array
     */
    public function fetchAllComments(string $videoId, int $maxResults = 100): array
    {
        $comments = [];
        $pageToken = null;
        $service = $this->youtubeService;
        $params = [
            'videoId' => $videoId,
            'part' => 'snippet',
            'maxResults' => min($maxResults, 100), // API上限
            'textFormat' => 'plainText',
            'order' => 'time',
        ];
        $fetched = 0;
        do {
            if ($pageToken) {
                $params['pageToken'] = $pageToken;
            }
            $response = $service->commentThreads->listCommentThreads('snippet', $params);
            foreach ($response->getItems() as $item) {
                $snippet = $item->getSnippet();
                $topComment = $snippet->getTopLevelComment()->getSnippet();
                $comments[] = [
                    'youtube_comment_id' => $item->getId(),
                    'author_display_name' => $topComment->getAuthorDisplayName(),
                    'author_channel_id' => $topComment->getAuthorChannelId()['value'] ?? null,
                    'text_display' => $topComment->getTextDisplay(),
                    'like_count' => $topComment->getLikeCount(),
                    'published_at' => $topComment->getPublishedAt(),
                    'updated_at_youtube' => $topComment->getUpdatedAt(),
                    'is_public' => true,
                ];
                $fetched++;
                if ($fetched >= $maxResults) break 2;
            }
            $pageToken = $response->getNextPageToken();
        } while ($pageToken);
        return $comments;
    }
}
