<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\YouTubeService;
use App\Models\Video;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class VideoController extends Controller
{
    /**
     * YouTube動画URLを受け取り、動画情報を取得・保存
     */
    public function store(Request $request, YouTubeService $youtubeService)
    {
        $request->validate([
            'video_url' => ['required', 'string'],
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => '認証が必要です。ログインしてください。',
                'error' => 'unauthorized'
            ], 401);
        }

        try {
            // 動画ID抽出
            $videoId = $youtubeService->extractVideoId($request->video_url);

            // 既存動画チェック
            $video = Video::where('youtube_id', $videoId)->where('user_id', $user->id)->first();
            if ($video) {
                return response()->json([
                    'message' => '既に保存済みの動画です',
                    'video' => $video
                ], 200);
            }

            // 動画情報取得
            $videoInfo = $youtubeService->getVideoInfo($videoId);

            // 保存
            DB::beginTransaction();
            $video = Video::create([
                ...$videoInfo,
                'user_id' => $user->id,
            ]);

            // コメント一括取得・保存
            $comments = $youtubeService->fetchAllComments($videoId, 500); // 最大500件取得
            foreach ($comments as $comment) {
                $video->comments()->updateOrCreate(
                    [
                        'youtube_comment_id' => $comment['youtube_comment_id'],
                    ],
                    $comment
                );
            }

            DB::commit();

            return response()->json([
                'message' => '動画情報を保存しました',
                'video' => $video
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('動画保存エラー: ' . $e->getMessage(), [
                'user_id' => $user->id ?? 'unknown',
                'video_url' => $request->video_url,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'message' => '動画情報の保存に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
