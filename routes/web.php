<?php

use App\Http\Controllers\FeedController;
use Illuminate\Support\Facades\Route;

Route::get('/voice', function () { return view('voice');});

Route::get('/', [FeedController::class, 'index'])->name('feed');
Route::get('/voices/{person}', [FeedController::class, 'person'])->name('person');
Route::get('/passage/{passage}', [FeedController::class, 'show'])->name('passage');
Route::get('/passage/{passage}/card.png', [FeedController::class, 'card'])->name('passage.card');

