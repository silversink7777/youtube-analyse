<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'tbl_comments';

    protected $fillable = [
        'video_id',
        'youtube_comment_id',
        'author_display_name',
        'author_channel_id',
        'text_display',
        'like_count',
        'published_at',
        'updated_at_youtube',
        'is_public',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'updated_at_youtube' => 'datetime',
        'is_public' => 'boolean',
        'like_count' => 'integer',
    ];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }
}
