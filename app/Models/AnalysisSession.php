<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisSession extends Model
{
    use HasFactory;

    protected $table = 'tbl_analysis_sessions';

    protected $fillable = [
        'video_id',
        'user_id',
        'status',
        'total_comments',
        'processed_comments',
        'analysis_results',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_comments' => 'integer',
        'processed_comments' => 'integer',
        'analysis_results' => 'array',
    ];

    /**
     * 動画とのリレーション
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * ユーザーとのリレーション
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ステータス定数
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    /**
     * 分析が完了しているかどうかを判定
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * 分析が失敗しているかどうかを判定
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * 分析が進行中かどうかを判定
     */
    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * 進捗率を取得（0-100）
     */
    public function getProgressPercentageAttribute(): int
    {
        if ($this->total_comments === 0) {
            return 0;
        }
        return min(100, round(($this->processed_comments / $this->total_comments) * 100));
    }

    /**
     * 分析時間を取得（秒）
     */
    public function getAnalysisDurationAttribute(): ?int
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }
        return $this->started_at->diffInSeconds($this->completed_at);
    }

    /**
     * 分析時間をフォーマット
     */
    public function getFormattedDurationAttribute(): string
    {
        $duration = $this->analysis_duration;
        if ($duration === null) {
            return 'N/A';
        }

        if ($duration < 60) {
            return "{$duration}秒";
        }

        $minutes = floor($duration / 60);
        $seconds = $duration % 60;
        return "{$minutes}分{$seconds}秒";
    }
}
