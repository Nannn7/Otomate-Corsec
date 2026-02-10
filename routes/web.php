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
            $user = auth()->user();
            $notifications = $user?->unreadNotifications ?? collect();

            $incomingIds = $notifications
                ->pluck('data.incoming_letter_id')
                ->filter()
                ->unique()
                ->values();
            $outgoingIds = $notifications
                ->pluck('data.outgoing_letter_id')
                ->filter()
                ->unique()
                ->values();
            $workplanIds = $notifications
                ->map(function ($notification) {
                    $data = is_array($notification->data) ? $notification->data : [];
                    return $data['work_program_id'] ?? $data['workplan_id'] ?? null;
                })
                ->filter()
                ->unique()
                ->values();
            $meetingIds = $notifications
                ->pluck('data.meeting_id')
                ->filter()
                ->unique()
                ->values();

            $incomingStatusMap = $incomingIds->isEmpty()
                ? collect()
                : IncomingLetter::query()
                    ->whereIn('id', $incomingIds)
                    ->pluck('status', 'id');
            $outgoingStatusMap = $outgoingIds->isEmpty()
                ? collect()
                : OutgoingLetter::query()
                    ->whereIn('id', $outgoingIds)
                    ->pluck('status', 'id');
            $workplanStatusMap = $workplanIds->isEmpty()
                ? collect()
                : WorkProgram::query()
                    ->whereIn('id', $workplanIds)
                    ->pluck('status', 'id');
            $meetingStatusMap = $meetingIds->isEmpty()
                ? collect()
                : Meeting::query()
                    ->whereIn('id', $meetingIds)
                    ->pluck('status', 'id');

            $count = $notifications->filter(function ($notification) use (
                $incomingStatusMap,
                $outgoingStatusMap,
                $workplanStatusMap,
                $meetingStatusMap
            ) {
                $data = is_array($notification->data) ? $notification->data : [];

                $incomingId = $data['incoming_letter_id'] ?? null;
                if ($incomingId) {
                    return $incomingStatusMap->get((string) $incomingId) !== IncomingLetter::STATUS_VERIFIED;
                }

                $outgoingId = $data['outgoing_letter_id'] ?? null;
                if ($outgoingId) {
                    return $outgoingStatusMap->get((string) $outgoingId) !== OutgoingLetter::STATUS_VERIFIED;
                }

                $workplanId = $data['work_program_id'] ?? $data['workplan_id'] ?? null;
                if ($workplanId) {
                    return $workplanStatusMap->get((string) $workplanId) !== WorkProgram::STATUS_DONE;
                }

                $meetingId = $data['meeting_id'] ?? null;
                if ($meetingId) {
                    $meetingStatus = $meetingStatusMap->get((string) $meetingId);
                    return !in_array((string) $meetingStatus, ['done', 'completed', 'closed', 'verified'], true);
                }

                return true;
            })->count();

            return response()->json([
                'count' => $count
            ]);
        })->name('notifications.count')->middleware('auth');

        Route::post('/notifications/read-all', function () {
            auth()->user()->unreadNotifications->markAsRead();
            return response()->json(['success' => true]);
        })->name('notifications.read_all')->middleware('auth');
});
