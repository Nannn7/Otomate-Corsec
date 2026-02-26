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
                    ->orWhereNotIn('status', ['done', 'completed', 'sent', 'verified', OutgoingLetter::STATUS_CANCELLED]);
            })
            ->where(function ($q) {
                $q->whereNull('authorized_status')
                    ->orWhere('authorized_status', '!=', 'cancelled');
            })
            ->whereNull('cancelled_at')
            ->count();

        $meetingOpen = Meeting::query()
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereNotIn('status', [
                        'done',
                        'completed',
                        'closed',
                        'verified',
                        Meeting::STATUS_DONE_TINDAKLANJUT_HASIL_RAPAT,
                    ]);
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
            $user = auth()->user();
            if (!$user) {
                return response()->json(['count' => 0]);
            }

            $unreadNotifications = $user->unreadNotifications()->latest()->get();
            corsecAutoReadResolvedNotifications($unreadNotifications);

            $unreadNotifications = $user->unreadNotifications()->latest()->get();
            $filteredNotifications = corsecFilterActionableNotifications($unreadNotifications);

            return response()->json([
                'count' => $filteredNotifications->count(),
            ]);
        })->name('notifications.count')->middleware('auth');

        Route::get('/notifications/list', function () {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'count' => 0,
                    'notifications' => [],
                ]);
            }

            $unreadNotifications = $user->unreadNotifications()->latest()->get();
            corsecAutoReadResolvedNotifications($unreadNotifications);

            $unreadNotifications = $user->unreadNotifications()->latest()->get();
            $filteredNotifications = corsecFilterActionableNotifications($unreadNotifications);

            $notifications = $filteredNotifications->map(function ($notification) {
                $formatted = formatNotifikasi($notification);

                return [
                    'id' => (string) $notification->id,
                    'title' => (string) ($formatted['title'] ?? 'Notifikasi'),
                    'message' => (string) ($formatted['message'] ?? 'Ada notifikasi baru.'),
                    'created_human' => optional($notification->created_at)->diffForHumans(),
                    'created_at' => optional($notification->created_at)->toIso8601String(),
                ];
            })->values();

            return response()->json([
                'count' => $notifications->count(),
                'notifications' => $notifications,
            ]);
        })->name('notifications.list')->middleware('auth');

        Route::post('/notifications/read-all', function () {
            $user = auth()->user();
            if ($user) {
                $user->unreadNotifications()->update([
                    'read_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'count' => 0,
            ]);
        })->name('notifications.read_all')->middleware('auth');
});
