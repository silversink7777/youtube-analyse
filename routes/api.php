<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VideoController;

Route::middleware(['web'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:web');

    Route::get('/my-videos', [VideoController::class, 'myVideos'])->middleware('auth:web');

    Route::post('/videos', [VideoController::class, 'store'])->middleware('auth:web');
    Route::post('/videos/{video}/analyze-comments', [VideoController::class, 'analyzeComments'])->middleware('auth:web');
    Route::post('/videos/{video}/extract-keywords', [VideoController::class, 'extractKeywords'])->middleware('auth:web');
    Route::post('/videos/{video}/analyze-word-frequency', [VideoController::class, 'analyzeWordFrequency'])->middleware('auth:web');
});
