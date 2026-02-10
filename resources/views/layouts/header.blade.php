<header class="header fixed top-0 z-10 left-0 right-0 flex items-stretch shrink-0 bg-[#fefefe] dark:bg-coal-500"
        data-sticky="true" data-sticky-class="shadow-sm dark:border-b dark:border-b-coal-100" data-sticky-name="header"
        id="header">
    <!-- begin: container -->
    <div class="container-fluid flex justify-between items-stretch lg:gap-4" id="header_container">
        <div class="flex gap-1 lg:hidden items-center -ml-1">
            <a class="shrink-0" href="{{ url('/') }}">
                <img class="max-h-[25px] w-full" src="assets/media/app/logo-agi-mini.png" />
            </a>
            <div class="flex items-center">
                <button class="btn btn-icon btn-light btn-clear btn-sm" data-drawer-toggle="#sidebar">
                    <i class="ki-filled ki-menu">
                    </i>
                </button>
                <button class="btn btn-icon btn-light btn-clear btn-sm" data-drawer-toggle="#megamenu_wrapper">
                    <i class="ki-filled ki-burger-menu-2">
                    </i>
                </button>
            </div>
        </div>
        <div class="flex items-stretch" id="megamenu_container">
            <div class="flex items-stretch" data-reparent="true" data-reparent-mode="prepend|lg:prepend"
                 data-reparent-target="body|lg:#megamenu_container">
            </div>

            @yield('breadcrumbs')
        </div>

        <div class="flex items-center gap-2 lg:gap-3.5">
            @php
                $user = auth()->user();
                $unreadNotifications = $user?->unreadNotifications ?? collect();

                $incomingIds = $unreadNotifications
                    ->pluck('data.incoming_letter_id')
                    ->filter()
                    ->unique()
                    ->values();
                $outgoingIds = $unreadNotifications
                    ->pluck('data.outgoing_letter_id')
                    ->filter()
                    ->unique()
                    ->values();
                $workplanIds = $unreadNotifications
                    ->map(function ($notification) {
                        $data = is_array($notification->data) ? $notification->data : [];
                        return $data['work_program_id'] ?? $data['workplan_id'] ?? null;
                    })
                    ->filter()
                    ->unique()
                    ->values();
                $meetingIds = $unreadNotifications
                    ->pluck('data.meeting_id')
                    ->filter()
                    ->unique()
                    ->values();

                $incomingStatusMap = $incomingIds->isEmpty()
                    ? collect()
                    : \Modules\Corsec\Models\IncomingLetter::query()
                        ->whereIn('id', $incomingIds)
                        ->pluck('status', 'id');
                $outgoingStatusMap = $outgoingIds->isEmpty()
                    ? collect()
                    : \Modules\Corsec\Models\OutgoingLetter::query()
                        ->whereIn('id', $outgoingIds)
                        ->pluck('status', 'id');
                $workplanStatusMap = $workplanIds->isEmpty()
                    ? collect()
                    : \Modules\Corsec\Models\WorkProgram::query()
                        ->whereIn('id', $workplanIds)
                        ->pluck('status', 'id');
                $meetingStatusMap = $meetingIds->isEmpty()
                    ? collect()
                    : \Modules\Corsec\Models\Meeting::query()
                        ->whereIn('id', $meetingIds)
                        ->pluck('status', 'id');

                $filteredNotifications = $unreadNotifications->filter(function ($notification) use (
                    $incomingStatusMap,
                    $outgoingStatusMap,
                    $workplanStatusMap,
                    $meetingStatusMap
                ) {
                    $data = is_array($notification->data) ? $notification->data : [];

                    $incomingId = $data['incoming_letter_id'] ?? null;
                    if (!$incomingId) {
                        $outgoingId = $data['outgoing_letter_id'] ?? null;
                        if (!$outgoingId) {
                            $workplanId = $data['work_program_id'] ?? $data['workplan_id'] ?? null;
                            if (!$workplanId) {
                                $meetingId = $data['meeting_id'] ?? null;
                                if (!$meetingId) {
                                    return true;
                                }

                                $meetingStatus = $meetingStatusMap->get((string) $meetingId);
                                return !in_array((string) $meetingStatus, ['done', 'completed', 'closed', 'verified'], true);
                            }

                            return $workplanStatusMap->get((string) $workplanId) !== \Modules\Corsec\Models\WorkProgram::STATUS_DONE;
                        }

                        return $outgoingStatusMap->get((string) $outgoingId) !== \Modules\Corsec\Models\OutgoingLetter::STATUS_VERIFIED;
                    }

                    $status = $incomingStatusMap->get((string) $incomingId);
                    return $status !== \Modules\Corsec\Models\IncomingLetter::STATUS_VERIFIED;
                })->values();
                $unreadCount = $filteredNotifications->count();
            @endphp
            <div class="dropdown" data-dropdown="true" data-dropdown-offset="70px, 10px" data-dropdown-placement="bottom-end"
                 data-dropdown-trigger="click|lg:click">
                <button
                    class="dropdown-toggle btn btn-icon btn-icon-lg relative cursor-pointer size-9 rounded-full hover:bg-primary-light hover:text-primary dropdown-open:bg-primary-light dropdown-open:text-primary text-gray-500">
                    <i class="ki-filled ki-notification-on">
                    </i>
                    @if ($unreadCount > 0)
                        <span
                            class="badge badge-xs badge-danger absolute -top-1 -right-1 min-w-[18px] h-[18px] flex items-center justify-center">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </button>
                <div class="dropdown-content light:border-gray-300 w-full max-w-[460px]">
                    <div class="flex items-center justify-between gap-2.5 text-sm text-gray-900 font-semibold px-5 py-2.5"
                         id="notifications_header">
                        Notifications
                        <button class="btn btn-sm btn-icon btn-light btn-clear shrink-0" data-dropdown-dismiss="true">
                            <i class="ki-filled ki-cross">
                            </i>
                        </button>
                    </div>
                    <div class="border-b border-b-gray-200">
                    </div>

                    <div class="flex flex-col">
                        <div class="scrollable-y-auto" data-scrollable="true" data-scrollable-dependencies="#header"
                             data-scrollable-max-height="auto" data-scrollable-offset="200px">
                            <div class="flex flex-col gap-5 py-5 divider-y divider-gray-200">
                                @forelse ($filteredNotifications as $notification)
                                    <div class="flex items-center grow gap-2.5 px-5">
                                        <div
                                            class="flex items-center justify-center size-8 bg-success-light rounded-full border border-success-clarity">
                                            <i class="ki-filled ki-check text-lg text-success">
                                            </i>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <span class="text-2sm font-medium text-gray-700">
                                                {{ formatNotifikasi($notification)['title'] }}<br>
                                                {{ formatNotifikasi($notification)['message'] }}<br>

                                            </span>
                                            <span class="font-medium text-gray-500 text-2xs">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    @if(!$loop->last)
                                    <div class="border-b border-b-gray-200"></div>
                                    @endif
                                @empty
                                    <div class="flex items-center justify-center px-5 text-sm text-gray-500">
                                        Belum ada notifikasi baru.
                                    </div>
                                @endforelse

                                    <!-- Notification sound -->
                                    <audio id="notification-sound" style="display: none;">
                                        <source src="{{ asset('assets/media/notif/1.mp3') }}" type="audio/mpeg">
                                    </audio>

                            </div>
                        </div>
                        <div class="border-b border-b-gray-200">
                        </div>
                        <div class="grid grid-cols-2 p-5 gap-2.5" id="notifications_all_footer">
                            <button class="btn btn-sm btn-light justify-center" id="mark-notifications-read"
                                data-read-url="{{ route('notifications.read_all') }}">
                                Mark all as read
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="menu" data-menu="true">
                <div class="menu-item" data-menu-item-offset="20px, 10px" data-menu-item-placement="bottom-end"
                     data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                    <div class="menu-toggle btn btn-icon rounded-full">
                        <img alt="" class="size-9 rounded-full border-2 border-success shrink-0"
                             src="assets/media/avatars/300-2.png" />
                    </div>
                    <div class="menu-dropdown menu-default light:border-gray-300 w-full max-w-[350px]">
                        <div class="flex items-start justify-between px-5 py-1.5 gap-1.5">
                            <div class="flex items-start gap-2">
                                <img alt="" class="size-9 rounded-full border-2 border-success" src="assets/media/avatars/300-2.png" />
                                <div class="flex flex-col gap-1.5">
                                    <span class="text-sm text-gray-800 font-semibold leading-none">
                                        {{ Auth::user()->name }}
                                    </span>
                                    <span class="text-xs text-gray-600 hover:text-primary font-medium leading-none">
                                        {{ Auth::user()->nik ?? "" }} | {{ Auth::user()->branch->name ?? "" }}
                                    </span>
                                </div>
                            </div>
                            <span class="badge badge-xs badge-primary badge-outline">
                                {{ Auth::user()->roles[0]->name?? "" }}
                            </span>
                        </div>
                        <div class="menu-separator">
                        </div>
                        <div class="flex flex-col" data-menu-dismiss="true">
                            <div class="menu-item">
                                <a class="menu-link" href="{{ route('users.profile') }}">
                                    <span class="menu-icon">
                                        <i class="ki-filled ki-profile-circle">
                                        </i>
                                    </span>
                                    <span class="menu-title">
                                        My Profile
                                    </span>
                                </a>
                            </div>
                        </div>
                        <div class="menu-separator">
                        </div>
                        <div class="flex flex-col">
                            <div class="menu-item mb-0.5">
                                <div class="menu-link">
                                    <span class="menu-icon">
                                        <i class="ki-filled ki-moon">
                                        </i>
                                    </span>
                                    <span class="menu-title">
                                        Dark Mode
                                    </span>
                                    <label class="switch switch-sm">
                                        <input data-theme-state="dark" data-theme-toggle="true" name="check" type="checkbox" value="1" />
                                    </label>
                                </div>
                            </div>
                            <div class="menu-item px-4 py-1.5">
                                <a class="btn btn-sm btn-light justify-center" href="{{ route('logout') }}">
                                    Log out
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end: container -->
</header>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initial count of unread notifications
            let previousNotificationCount = {{ $unreadCount }};
            const notificationSound = document.getElementById('notification-sound');
            const markReadButton = document.getElementById('mark-notifications-read');
            const markReadUrl = markReadButton ? markReadButton.dataset.readUrl : null;

            // Play sound if there are unread notifications on page load
            if (previousNotificationCount > 0 && notificationSound) {
                // Set a flag in localStorage to track if sound has been played in this session
                const soundPlayed = localStorage.getItem('notification_sound_played');

                if (!soundPlayed) {
                    // Play the notification sound
                    notificationSound.play().catch(error => {
                        console.error('Error playing notification sound:', error);
                    });

                    // Set the flag to prevent playing the sound again in this session
                    localStorage.setItem('notification_sound_played', 'true');

                    // Clear the flag after 5 minutes to allow sound to play again if user refreshes
                    setTimeout(() => {
                        localStorage.removeItem('notification_sound_played');
                    }, 5 * 60 * 1000);
                }
            }

            // Function to check for new notifications
            function checkForNewNotifications() {
                fetch('{{ route("notifications.count") }}')
                    .then(response => response.json())
                    .then(data => {
                        const currentCount = data.count;

                        // If there are more notifications than before, play the sound
                        if (currentCount > previousNotificationCount) {
                            // Play notification sound
                            if (notificationSound) {
                                setTimeout(() => {
                                    window.location.reload();
                                },5000);
                                notificationSound.play().catch(error => {
                                    console.error('Error playing notification sound:', error);
                                });
                            }

                            // Update the notification UI (optional)
                            updateNotificationUI(currentCount);
                        }

                        // Update the previous count
                        previousNotificationCount = currentCount;
                    })
                    .catch(error => {
                        console.error('Error checking for notifications:', error);
                    });
            }

            // Function to update notification UI (optional)
            function updateNotificationUI(count) {
                // Update notification badge or UI elements if needed
                const badge = document.querySelector('.badge-dot.badge-success');
                if (badge) {
                    // Make the badge more visible when new notifications arrive
                    badge.classList.add('animate-pulse');
                    setTimeout(() => {
                        badge.classList.remove('animate-pulse');
                    }, 3000);
                }

                // You might want to refresh the notification list here
                // This would require an additional endpoint to fetch the latest notifications
            }

            // Check for new notifications every 30 seconds
            setInterval(checkForNewNotifications, 5000);

            if (markReadButton && markReadUrl) {
                markReadButton.addEventListener('click', function() {
                    fetch(markReadUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(() => window.location.reload());
                });
            }
        });
    </script>
@endpush
