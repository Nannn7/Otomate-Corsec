<?php

use Illuminate\Support\Facades\Route;
use Modules\Corsec\Models\IncomingLetter;
use Modules\Corsec\Models\Meeting;
use Modules\Corsec\Models\OutgoingLetter;

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        $incomingOpen = IncomingLetter::query()
            ->whereNotIn('status', ['verified'])
            ->count();

        $outgoingOpen = OutgoingLetter::query()
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereNotIn('status', ['done', 'completed', 'sent', 'verified']);
            })
            ->count();

        $meetingOpen = Meeting::query()
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereNotIn('status', ['done', 'completed', 'closed', 'verified']);
            })
            ->count();

        return view('welcome', compact('incomingOpen', 'outgoingOpen', 'meetingOpen'));
    })->name('dashboard');

        Route::get('/notifications/count', function () {
            return response()->json([
                'count' => auth()->user()->unreadNotifications->count()
            ]);
        })->name('notifications.count')->middleware('auth');
});
