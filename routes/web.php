<?php

use Illuminate\Support\Facades\Route;
use Modules\Corsec\Services\CorsecPermissionService;

Route::middleware(['auth'])->group(function () {
    Route::get('/', function (CorsecPermissionService $permissionService) {
        $user = auth()->user();
        $counts = [
            'incomingOpen' => 0,
            'outgoingOpen' => 0,
            'meetingOpen' => 0,
            'workplanOpen' => 0,
        ];

        if ($permissionService->canAccessDashboard($user)) {
            $counts = $permissionService->dashboardCounts($user);
        }

        $overview = $permissionService->dashboardOverviewData($counts);

        return view('welcome', array_merge($counts, $overview));
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
