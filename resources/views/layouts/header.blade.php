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
                $unreadCount = 0;
            @endphp
            <div class="dropdown" data-dropdown="true" data-dropdown-offset="70px, 10px" data-dropdown-placement="bottom-end"
                 data-dropdown-trigger="click|lg:click">
                <button
                    class="dropdown-toggle btn btn-icon btn-icon-lg relative cursor-pointer size-9 rounded-full hover:bg-primary-light hover:text-primary dropdown-open:bg-primary-light dropdown-open:text-primary text-gray-500">
                    <i class="ki-filled ki-notification-on">
                    </i>
                    <span id="notification-badge"
                        class="badge badge-xs badge-danger absolute -top-1 -right-1 min-w-[18px] h-[18px] flex items-center justify-center {{ $unreadCount > 0 ? '' : 'hidden' }}">
                        {{ $unreadCount > 0 ? $unreadCount : '' }}
                    </span>
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
                            <div class="flex flex-col gap-5 py-5 divider-y divider-gray-200" id="notifications-list">
                                <div class="flex items-center justify-center px-5 text-sm text-gray-500">
                                    Belum ada notifikasi baru.
                                </div>
                            </div>

                            <audio id="notification-sound" style="display: none;">
                                <source src="{{ asset('assets/media/notif/1.mp3') }}" type="audio/mpeg">
                            </audio>
                        </div>
                        <div class="border-b border-b-gray-200">
                        </div>
                        <div class="grid grid-cols-2 p-5 gap-2.5" id="notifications_all_footer">
                            <button class="btn btn-sm btn-light justify-center" id="mark-notifications-read"
                                data-read-url="{{ route('notifications.read_all') }}"
                                data-list-url="{{ route('notifications.list') }}">
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
                                        {{ $currentUser?->nik ?? '' }} | {{ $currentUser?->branch?->name ?? '' }}
                                    </span>
                                </div>
                            </div>
                            <span class="badge badge-xs badge-primary badge-outline">
                                {{ $currentUser?->roles->first()?->name ?? '' }}
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
            let previousNotificationCount = 0;
            const notificationSound = document.getElementById('notification-sound');
            const markReadButton = document.getElementById('mark-notifications-read');
            const notificationList = document.getElementById('notifications-list');
            const notificationBadge = document.getElementById('notification-badge');
            const markReadUrl = markReadButton ? markReadButton.dataset.readUrl : null;
            const listUrl = markReadButton ? markReadButton.dataset.listUrl : null;

            if (previousNotificationCount > 0 && notificationSound) {
                const soundPlayed = localStorage.getItem('notification_sound_played');

                if (!soundPlayed) {
                    notificationSound.play().catch(error => {
                        console.error('Error playing notification sound:', error);
                    });

                    localStorage.setItem('notification_sound_played', 'true');

                    setTimeout(() => {
                        localStorage.removeItem('notification_sound_played');
                    }, 5 * 60 * 1000);
                }
            }

            function escapeHtml(str) {
                return String(str ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function renderBadge(count) {
                if (!notificationBadge) {
                    return;
                }

                if (count > 0) {
                    notificationBadge.textContent = String(count);
                    notificationBadge.classList.remove('hidden');
                } else {
                    notificationBadge.textContent = '';
                    notificationBadge.classList.add('hidden');
                }
            }

            function renderNotifications(items) {
                if (!notificationList) {
                    return;
                }

                if (!Array.isArray(items) || items.length === 0) {
                    notificationList.innerHTML = `
                        <div class="flex items-center justify-center px-5 text-sm text-gray-500">
                            Belum ada notifikasi baru.
                        </div>
                    `;
                    return;
                }

                const html = items.map((item, index) => {
                    const title = escapeHtml(item.title || 'Notifikasi');
                    const message = escapeHtml(item.message || 'Ada notifikasi baru.');
                    const createdHuman = escapeHtml(item.created_human || '-');
                    const divider = index < items.length - 1
                        ? '<div class="border-b border-b-gray-200"></div>'
                        : '';

                    return `
                        <div class="flex items-center grow gap-2.5 px-5">
                            <div class="flex items-center justify-center size-8 bg-success-light rounded-full border border-success-clarity">
                                <i class="ki-filled ki-check text-lg text-success"></i>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-2sm font-medium text-gray-700">
                                    ${title}<br>
                                    ${message}<br>
                                </span>
                                <span class="font-medium text-gray-500 text-2xs">${createdHuman}</span>
                            </div>
                        </div>
                        ${divider}
                    `;
                }).join('');

                notificationList.innerHTML = html;
            }

            function loadNotifications(playSound = false) {
                if (!listUrl) {
                    return Promise.resolve();
                }

                return fetch(listUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        const currentCount = Number(data.count || 0);
                        renderBadge(currentCount);
                        renderNotifications(Array.isArray(data.notifications) ? data.notifications : []);

                        if (playSound && currentCount > previousNotificationCount && notificationSound) {
                            notificationSound.play().catch(error => {
                                console.error('Error playing notification sound:', error);
                            });
                        }

                        previousNotificationCount = currentCount;
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                    });
            }

            loadNotifications(false);
            setInterval(() => loadNotifications(true), 15000);

            if (markReadButton && markReadUrl) {
                markReadButton.addEventListener('click', function() {
                    markReadButton.disabled = true;
                    fetch(markReadUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                        .then(response => response.json())
                        .then(() => {
                            previousNotificationCount = 0;
                            renderBadge(0);
                            renderNotifications([]);
                        })
                        .catch(error => {
                            console.error('Error marking notifications as read:', error);
                        })
                        .finally(() => {
                            markReadButton.disabled = false;
                        });
                });
            }
        });
    </script>
@endpush
