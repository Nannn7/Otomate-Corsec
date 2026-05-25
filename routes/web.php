<?php

use Illuminate\Support\Facades\Route;
use Modules\Corsec\Services\CorsecPermissionService;

Route::middleware(['auth'])->group(function () {
    $buildNotificationPayload = static function ($user, bool $includeItems = false): array {
        if (!$user) {
            return [
                'count' => 0,
                'notifications' => [],
            ];
        }

        $unreadNotifications = $user->unreadNotifications()->latest()->get();
        $partitioned = corsecPartitionActionableNotifications($unreadNotifications);
        $resolvedIds = $partitioned['resolved_ids'];

        if (!empty($resolvedIds)) {
            \Illuminate\Support\Facades\DB::table('notifications')
                ->whereIn('id', $resolvedIds)
                ->whereNull('read_at')
                ->update([
                    'read_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        $actionableNotifications = $partitioned['actionable'];
        $payload = [
            'count' => $actionableNotifications->count(),
        ];

        if (!$includeItems) {
            return $payload + ['notifications' => []];
        }

        $payload['notifications'] = $actionableNotifications
            ->take(20)
            ->map(function ($notification) {
                $formatted = formatNotifikasi($notification);

                return [
                    'id' => (string) $notification->id,
                    'title' => (string) ($formatted['title'] ?? 'Notifikasi'),
                    'message' => (string) ($formatted['message'] ?? 'Ada notifikasi baru.'),
                    'created_human' => optional($notification->created_at)->diffForHumans(),
                    'created_at' => optional($notification->created_at)->toIso8601String(),
                ];
            })
            ->values();

        return $payload;
    };

    Route::get('/', function (CorsecPermissionService $permissionService) {
        $user = auth()->user();
        $counts = [
            'incomingOpen' => 0,
            'outgoingOpen' => 0,
            'meetingOpen' => 0,
            'workplanOpen' => 0,
        ];

        if (!$permissionService->canAccessDashboard($user)) {
            abort(403, 'Sorry! You are not allowed to view corsec app.');
        }

        $counts = $permissionService->dashboardCounts($user);

        $overview = $permissionService->dashboardOverviewData($counts);

        return view('welcome', array_merge($counts, $overview));
    })->name('dashboard');

        Route::get('/notifications/count', function () use ($buildNotificationPayload) {
            return response()->json($buildNotificationPayload(auth()->user()));
        })->name('notifications.count')->middleware('auth');

        Route::get('/notifications/list', function () use ($buildNotificationPayload) {
            return response()->json($buildNotificationPayload(auth()->user(), true));
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
