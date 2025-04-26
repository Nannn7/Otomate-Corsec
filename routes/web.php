<?php

use Illuminate\Support\Facades\Route;
    Route::middleware(['auth'])->group(function () {
        Route::get('/', function () {
            return view('welcome');
        })->name('dashboard');

        Route::get('/notifications/count', function () {
            return response()->json([
                'count' => auth()->user()->unreadNotifications->count()
            ]);
        })->name('notifications.count')->middleware('auth');
    });
