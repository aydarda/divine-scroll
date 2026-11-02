<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\Admin\ModerationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\Api\FeedController;
use App\Http\Controllers\Api\LibraryController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\SuggestionController;
use App\Http\Controllers\Api\BlockController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureNotSuspended;
use Illuminate\Support\Facades\Route;

// Every throttle has its own name (the third value) so the counters stay separate.
Route::prefix('v1')->middleware('throttle:120,1,api')->group(function () {

    // ---- Public. If a valid token is sent, "saved" and the user's note are filled in. ----
    Route::get('/config', [ConfigController::class, 'show']);
    Route::get('/feed', [FeedController::class, 'index']);
    Route::get('/search', [SearchController::class, 'index'])->middleware('throttle:60,1,api-search');
    Route::get('/passages/{passage}', [FeedController::class, 'show']);
    Route::get('/passages/{passage}/comments', [CommentController::class, 'index']);
    Route::get('/voices', [FeedController::class, 'voices']);
    Route::get('/voices/{person}', [FeedController::class, 'voice']);
    Route::get('/suggestions/options', [SuggestionController::class, 'options']);

    // ---- Accounts ----
    Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:5,10,api-register');
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:8,1,api-login');
    Route::post('/auth/forgot-password', [PasswordController::class, 'forgot'])->middleware('throttle:5,10,api-forgot');
    Route::post('/auth/reset-password', [PasswordController::class, 'reset'])->middleware('throttle:5,10,api-reset');

    // ---- Signed in ----
    Route::middleware(['auth:sanctum', EnsureNotSuspended::class])->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::delete('/me', [AuthController::class, 'destroy'])->middleware('throttle:5,10,api-delete');
        Route::get('/me/export', [AccountController::class, 'export'])->middleware('throttle:5,10,api-export');
        Route::get('/me/contributions', [AccountController::class, 'contributions']);

        // Blocking: stop seeing one person's comments
        Route::get('/me/blocks', [BlockController::class, 'index']);
        Route::post('/users/{user}/block', [BlockController::class, 'store'])->middleware('throttle:30,1,api-block');
        Route::delete('/users/{user}/block', [BlockController::class, 'destroy']);

        // Saved passages and private notes
        Route::get('/library/saved', [LibraryController::class, 'saved']);
        Route::get('/library/notes', [LibraryController::class, 'notes']);
        Route::post('/passages/{passage}/save', [LibraryController::class, 'toggle']);   // flips
        Route::put('/passages/{passage}/save', [LibraryController::class, 'save']);      // makes sure it is saved
        Route::delete('/passages/{passage}/save', [LibraryController::class, 'unsave']); // makes sure it is not
        Route::put('/passages/{passage}/note', [LibraryController::class, 'note'])->middleware('throttle:30,1,api-note');
        Route::delete('/passages/{passage}/note', [LibraryController::class, 'deleteNote']);

        // Comments
        Route::post('/passages/{passage}/comments', [CommentController::class, 'store'])->middleware('throttle:5,60,api-comment');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
        Route::post('/comments/{comment}/report', [CommentController::class, 'report'])->middleware('throttle:10,60,api-report');

        // Suggestions: a new passage, or a correction to a passage's voice or badge
        Route::post('/suggestions', [SuggestionController::class, 'store'])->middleware('throttle:10,1440,api-suggest');
        Route::post('/passages/{passage}/correction', [SuggestionController::class, 'correction'])->middleware('throttle:10,1440,api-correction');

        // ---- Moderators only (anyone else gets a 404) ----
        Route::prefix('admin')->middleware(EnsureAdmin::class)->group(function () {
            Route::get('/summary', [ModerationController::class, 'summary']);
            Route::get('/comments', [ModerationController::class, 'comments']); // ?list=pending|reported|recent
            Route::post('/comments/{comment}/approve', [ModerationController::class, 'approveComment']);
            Route::post('/comments/{comment}/reject', [ModerationController::class, 'rejectComment']);
            Route::post('/comments/{comment}/dismiss', [ModerationController::class, 'dismissReports']);
            Route::get('/suggestions', [ModerationController::class, 'suggestions']);
            Route::post('/suggestions/{suggestion}/accept', [ModerationController::class, 'acceptSuggestion']);
            Route::post('/suggestions/{suggestion}/decline', [ModerationController::class, 'declineSuggestion']);
            Route::get('/users', [ModerationController::class, 'users']);                           // ?q=name or email
            Route::post('/users/{user}/suspend', [ModerationController::class, 'suspendUser']);
            Route::post('/users/{user}/unsuspend', [ModerationController::class, 'unsuspendUser']);
        });
    });
});
