<?php

use App\Http\Controllers\FeedController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\NoteController;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SuggestionController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\SearchController;
use App\Http\Middleware\EnsureNotSuspended;


Route::get('/voice', function () { return view('voice');});

Route::get('/', [FeedController::class, 'index'])->name('feed');
Route::get('/search', [SearchController::class, 'index'])->middleware('throttle:60,1,search')->name('search');
Route::get('/voices/{person}', [FeedController::class, 'person'])->name('person');
Route::get('/passage/{passage}', [FeedController::class, 'show'])->name('passage');
Route::get('/passage/{passage}/card.png', [FeedController::class, 'card'])->name('passage.card');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,10,register');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:8,1,login');

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])->middleware('throttle:5,10,forgot')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('throttle:5,10,reset')->name('password.update');
});

Route::middleware(['auth', EnsureNotSuspended::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/library', [LibraryController::class, 'index'])->name('library');
    Route::post('/bookmarks/{passage}', [BookmarkController::class, 'toggle'])->middleware('throttle:60,1,bookmark')->name('bookmark.toggle');
    Route::post('/passages/{passage}/note', [NoteController::class, 'save'])->middleware('throttle:30,1,note')->name('note.save');
    Route::get('/account', [AccountController::class, 'show'])->name('account');
    Route::get('/account/export', [AccountController::class, 'export'])->name('account.export');
    Route::delete('/account', [AccountController::class, 'destroy'])->name('account.destroy');

    // Blocking: stop seeing one person's comments
    Route::get('/blocked', [BlockController::class, 'index'])->name('blocked');
    Route::post('/users/{user}/block', [BlockController::class, 'store'])->middleware('throttle:30,1,block')->name('user.block');
    Route::delete('/users/{user}/block', [BlockController::class, 'destroy'])->name('user.unblock');
});

Route::middleware(['auth', EnsureNotSuspended::class])->group(function () {
    Route::post('/passages/{passage}/comments', [CommentController::class, 'store'])->middleware('throttle:5,60,comment')->name('comment.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comment.destroy');
    Route::post('/comments/{comment}/report', [CommentController::class, 'report'])->middleware('throttle:10,60,report')->name('comment.report');

    Route::get('/suggest', [SuggestionController::class, 'create'])->name('suggest');
    Route::post('/suggest', [SuggestionController::class, 'storePassage'])->middleware('throttle:10,1440,suggest')->name('suggest.store');
    Route::post('/passages/{passage}/correction', [SuggestionController::class, 'storeCorrection'])->middleware('throttle:10,1440,correction')->name('correction.store');
});

Route::middleware(['auth', EnsureAdmin::class])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin');
    Route::get('/comments', [AdminController::class, 'comments'])->name('admin.comments');
    Route::post('/comments/{comment}/approve', [AdminController::class, 'approveComment'])->name('admin.comment.approve');
    Route::post('/comments/{comment}/reject', [AdminController::class, 'rejectComment'])->name('admin.comment.reject');
    Route::post('/comments/{comment}/dismiss', [AdminController::class, 'dismissReports'])->name('admin.comment.dismiss');
    Route::get('/suggestions', [AdminController::class, 'suggestions'])->name('admin.suggestions');
    Route::post('/suggestions/{suggestion}/accept', [AdminController::class, 'acceptSuggestion'])->name('admin.suggestion.accept');
    Route::post('/suggestions/{suggestion}/decline', [AdminController::class, 'declineSuggestion'])->name('admin.suggestion.decline');

    // People: suspend and restore
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users/{user}/suspend', [AdminController::class, 'suspendUser'])->name('admin.user.suspend');
    Route::post('/users/{user}/unsuspend', [AdminController::class, 'unsuspendUser'])->name('admin.user.unsuspend');
});

Route::view('/guidelines', 'guidelines')->name('guidelines');

Route::view('/privacy', 'privacy')->name('privacy');