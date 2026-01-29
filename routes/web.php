<?php

use Illuminate\Support\Facades\Route;
use Modules\Corsec\Models\IncomingLetter;
use Modules\Corsec\Models\Meeting;
use Modules\Corsec\Models\OutgoingLetter;
use Modules\Corsec\Models\WorkProgram;

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        $incomingOpen = IncomingLetter::query()
            ->whereNotIn('status', [
                IncomingLetter::STATUS_VERIFIED,
                IncomingLetter::STATUS_REJECTED,
                IncomingLetter::STATUS_RETURNED,
            ])
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

        $workplanOpen = WorkProgram::query()
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereNotIn('status', ['done', 'returned', 'rejected']);
            })
            ->count();

        return view('welcome', compact('incomingOpen', 'outgoingOpen', 'meetingOpen', 'workplanOpen'));
    })->name('dashboard');

        Route::get('/notifications/count', function () {
            return response()->json([
                'count' => auth()->user()->unreadNotifications->count()
            ]);
        })->name('notifications.count')->middleware('auth');

        Route::post('/notifications/read-all', function () {
            auth()->user()->unreadNotifications->markAsRead();
            return response()->json(['success' => true]);
        })->name('notifications.read_all')->middleware('auth');
});
