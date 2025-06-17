<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Api\VideoController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => app()->version(),
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    
    // YouTube動画保存API
    Route::post('/api/videos', [VideoController::class, 'store']);
    // コメント感情分析API
    Route::post('/api/videos/{video}/analyze-comments', [VideoController::class, 'analyzeComments']);
    // 自分の保存動画一覧API
    Route::get('/api/my-videos', [VideoController::class, 'myVideos']);

    // 保存動画一覧ページ
    Route::get('/my-videos', function () {
        return Inertia::render('MyVideos');
    })->name('my-videos');
});
