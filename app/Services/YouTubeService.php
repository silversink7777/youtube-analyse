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

    /**
     * OpenAI APIで日本語コメントの感情分析を行う
     * @param string $text
     * @return array [score, label, raw]
     */
    public function analyzeSentiment(string $text): array
    {
        $apiKey = config('services.openai.api_key');
        if (empty($apiKey)) {
            return [null, null, null];
        }
        $endpoint = 'https://api.openai.com/v1/chat/completions';
        $prompt = "次の日本語テキストの感情を1文で分類し、スコア（-1.0〜1.0）とラベル（positive, negative, neutral）で返してください。\nテキスト: {$text}";
        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'あなたは日本語の感情分析AIです。'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 100,
            'temperature' => 0.2,
        ];
        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ];
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($result, true);
        $content = $response['choices'][0]['message']['content'] ?? '';
        // 例: "score: 0.8\nlabel: positive"
        preg_match('/score\s*[:：]\s*([\-0-9\.]+)/i', $content, $scoreMatch);
        preg_match('/label\s*[:：]\s*(\w+)/i', $content, $labelMatch);
        $score = isset($scoreMatch[1]) ? floatval($scoreMatch[1]) : null;
        $label = $labelMatch[1] ?? null;
        return [$score, $label, $response];
    }

    /**
     * コメント群からキーワードを抽出（OpenAI API使用）
     * @param string[] $comments
     * @param int $maxKeywords
     * @return array
     */
    public function extractKeywords(array $comments, int $maxKeywords = 10): array
    {
        $apiKey = config('services.openai.api_key');
        if (empty($apiKey)) {
            return [];
        }
        $endpoint = 'https://api.openai.com/v1/chat/completions';
        $joined = implode("\n", array_slice($comments, 0, 100)); // 100件まで
        $prompt = "次の日本語コメント群から重要なキーワードを最大{$maxKeywords}個、カンマ区切りで抽出してください。単語のみ返してください。\nコメント:\n{$joined}";
        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'あなたは日本語のキーワード抽出AIです。'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 100,
            'temperature' => 0.2,
        ];
        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ];
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($result, true);
        $content = $response['choices'][0]['message']['content'] ?? '';
        // 例: "キーワード1, キーワード2, ..."
        $keywords = array_map('trim', explode(',', $content));
        $keywords = array_filter($keywords, fn($k) => mb_strlen($k) > 0);
        return array_slice($keywords, 0, $maxKeywords);
    }
}
