<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{
    use HasFactory;

    protected $table = 'tbl_videos';

    protected $fillable = [
        'youtube_id',
        'title',
        'description',
        'channel_title',
        'channel_id',
        'thumbnail_url',
        'thumbnail_medium_url',
        'thumbnail_high_url',
        'view_count',
        'like_count',
        'comment_count',
        'published_at',
        'duration',
        'tags',
        'category_id',
        'default_language',
        'default_audio_language',
        'is_public',
        'is_analyzed',
        'last_analyzed_at',
        'user_id',
        'keywords',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'last_analyzed_at' => 'datetime',
        'is_public' => 'boolean',
        'is_analyzed' => 'boolean',
        'view_count' => 'integer',
        'like_count' => 'integer',
        'comment_count' => 'integer',
        'tags' => 'array',
        'keywords' => 'array',
    ];

    /**
     * ユーザーとのリレーション
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 分析セッションとのリレーション
     */
    public function analysisSessions(): HasMany
    {
        return $this->hasMany(AnalysisSession::class);
    }

    /**
     * 最新の分析セッションを取得
     */
    public function latestAnalysisSession()
    {
        return $this->hasOne(AnalysisSession::class)->latest();
    }

    /**
     * YouTube IDで動画を検索
     */
    public static function findByYoutubeId(string $youtubeId): ?self
    {
        return static::where('youtube_id', $youtubeId)->first();
    }

    /**
     * ユーザーの動画一覧を取得
     */
    public static function getUserVideos(int $userId, int $limit = 10)
    {
        return static::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    /**
     * 分析済みの動画かどうかを判定
     */
    public function isAnalyzed(): bool
    {
        return $this->is_analyzed && $this->last_analyzed_at !== null;
    }

    /**
     * 動画のURLを生成
     */
    public function getYoutubeUrlAttribute(): string
    {
        return "https://www.youtube.com/watch?v={$this->youtube_id}";
    }

    /**
     * 動画の埋め込みURLを生成
     */
    public function getEmbedUrlAttribute(): string
    {
        return "https://www.youtube.com/embed/{$this->youtube_id}";
    }

    /**
     * 動画の短縮URLを生成
     */
    public function getShortUrlAttribute(): string
    {
        return "https://youtu.be/{$this->youtube_id}";
    }

    /**
     * 視聴回数をフォーマット
     */
    public function getFormattedViewCountAttribute(): string
    {
        return $this->formatNumber($this->view_count);
    }

    /**
     * いいね数をフォーマット
     */
    public function getFormattedLikeCountAttribute(): string
    {
        return $this->formatNumber($this->like_count);
    }

    /**
     * コメント数をフォーマット
     */
    public function getFormattedCommentCountAttribute(): string
    {
        return $this->formatNumber($this->comment_count);
    }

    /**
     * 数字をフォーマット（K, M, B単位）
     */
    private function formatNumber(int $number): string
    {
        if ($number >= 1000000000) {
            return round($number / 1000000000, 1) . 'B';
        }
        if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        }
        if ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        }
        return (string) $number;
    }

    /**
     * コメントとのリレーション
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
