<?php

    use Illuminate\Support\Str;
    use Carbon\Carbon;

    if (!function_exists('dateFormat')) {
        /**
         * Formats a date string according to specified parameters.
         *
         * This function uses Carbon to parse a date and format it according to the locale
         * and display preferences provided.
         *
         * @param string|DateTime $tanggal The date to be formatted
         * @param bool            $time    Whether to include time in the formatted output (default: false)
         * @param bool            $showDay Whether to include the day name in the formatted output (default: false)
         * @param string          $locale  The locale to use for formatting (default: 'id_ID')
         *
         * @return string                  The formatted date string
         */
        function dateFormat($tanggal, $time = false, $showDay = false, $locale = 'id_ID')
        {
            $carbon = Carbon::parse($tanggal)->locale($locale);

            if ($showDay && $time) {
                return $carbon->isoFormat('dddd, LL HH:mm:ss');
            } elseif ($showDay) {
                return $carbon->isoFormat('dddd, LL');
            } elseif ($time) {
                return $carbon->isoFormat('LL HH:mm:ss');
            } else {
                return $carbon->isoFormat('LL');
            }
        }
    }

    if (!function_exists('textTransform')) {
        /**
         * Transforms text according to the specified case format.
         *
         * This function applies different text transformations based on the provided case parameter.
         *
         * @param string $text The input text to be transformed
         * @param int    $case The transformation type to apply:
         *                     0 = Default (no transformation)
         *                     1 = lowercase
         *                     2 = UPPERCASE
         *                     3 = Capitalize Each Word (ucwords)
         *                     4 = First letter capitalized (ucfirst)
         *
         * @return string      The transformed text
         */
        function textTransform($text, $case = 0)
        {
            switch ($case) {
                case 1:
                    return strtolower($text);
                case 2:
                    return strtoupper($text);
                case 3:
                    return ucwords($text);
                case 4:
                    return ucfirst($text);
                default:
                    return $text;
            }
        }
    }


    if (!function_exists('slug')) {
        /**
         * Converts a string into a URL-friendly slug.
         *
         * This function creates a slug from a given title using Laravel's Str::slug
         * and then applies optional text transformation.
         *
         * @param string $title         The string to be converted into a slug
         * @param string $separator     The separator to use between words (default: '-')
         * @param int    $textTransform The text transformation to apply:
         *                              0 = Default (no transformation)
         *                              1 = lowercase
         *                              2 = UPPERCASE
         *                              3 = Capitalize (ucwords)
         *                              4 = First letter capitalized (ucfirst)
         *
         * @return string The formatted slug
         */
        function slug($title, $separator = '-', $textTransform = 0)
        {
            $slug = Str::slug($title, $separator);
            return textTransform($slug, $textTransform);
        }
    }

    if (!function_exists('slugToTitle')) {
        /**
         * Converts a URL-friendly slug back into a readable title.
         *
         * This function transforms a slug into a title by replacing separators with spaces
         * and applying optional text transformation to each word.
         *
         * @param string $slug          The slug to be converted into a title
         * @param string $separator     The separator used between words in the slug (default: '-')
         * @param int    $textTransform The text transformation to apply:
         *                              0 = Default (no transformation)
         *                              1 = lowercase
         *                              2 = UPPERCASE
         *                              3 = Capitalize (ucwords)
         *                              4 = First letter capitalized (ucfirst)
         *
         * @return string The formatted title
         */
        function slugToTitle($slug, $separator = '-', $textTransform = 0)
        {
            $title = Str::of($slug)
                        ->explode($separator)
                        ->map(function ($word) use ($textTransform) {
                            return textTransform($word, $textTransform);
                        })
                        ->implode(' ');

            return $title;
        }
    }


    if (!function_exists('convertToNumberOrdinal')) {
        /**
         * Converts a number to its ordinal representation in English.
         *
         * This function takes a numeric value and returns its ordinal form
         * (e.g., 1 becomes "1st", 2 becomes "2nd", etc.) following English language rules.
         *
         * @param int $number The number to convert to its ordinal form
         *
         * @return string|int The ordinal representation of the number as a string,
         *                    or 0 if the input is 0
         */
        function convertToNumberOrdinal($number)
        {
            if ($number === 0) {
                return 0;
            }

            if (!in_array(($number % 100), [11, 12, 13])) {
                switch ($number % 10) {
                    case 1:
                        return $number . 'st';
                    case 2:
                        return $number . 'nd';
                    case 3:
                        return $number . 'rd';
                }
            }

            return $number . 'th';
        }
    }

    if (!function_exists('convertToRoman')) {
        /**
         * Converts an integer to its Roman numeral representation.
         *
         * This function takes an integer and converts it to a Roman numeral string
         * using the standard Roman numeral notation system. It handles numbers by
         * progressively subtracting the largest possible Roman numeral values.
         *
         * @param int $number The integer to convert to Roman numerals
         *
         * @return string The Roman numeral representation of the input number
         */
        function convertToRoman($number)
        {
            $roman_number = '';

            while ($number >= 1000) {
                $roman_number .= 'M';
                $number       -= 1000;
            }

            while ($number >= 900) {
                $roman_number .= 'CM';
                $number       -= 900;
            }

            while ($number >= 500) {
                $roman_number .= 'D';
                $number       -= 500;
            }

            while ($number >= 400) {
                $roman_number .= 'CD';
                $number       -= 400;
            }

            while ($number >= 100) {
                $roman_number .= 'C';
                $number       -= 100;
            }

            while ($number >= 90) {
                $roman_number .= 'XC';
                $number       -= 90;
            }

            while ($number >= 50) {
                $roman_number .= 'L';
                $number       -= 50;
            }

            while ($number >= 40) {
                $roman_number .= 'XL';
                $number       -= 40;
            }

            while ($number >= 10) {
                $roman_number .= 'X';
                $number       -= 10;
            }

            while ($number >= 9) {
                $roman_number .= 'IX';
                $number       -= 9;
            }

            while ($number >= 5) {
                $roman_number .= 'V';
                $number       -= 5;
            }

            while ($number >= 4) {
                $roman_number .= 'IV';
                $number       -= 4;
            }

            while ($number >= 1) {
                $roman_number .= 'I';
                $number       -= 1;
            }

            return $roman_number;
        }
    }

    if (!function_exists('formatNotifikasi')) {
        /**
         * Format notification payload for UI rendering.
         *
         * @param \Illuminate\Notifications\DatabaseNotification $notification
         * @return array{title:string,message:string}
         */
        function formatNotifikasi($notification)
        {
            $data = is_array($notification->data) ? $notification->data : [];
            $title = (string) ($data['title'] ?? '');
            $message = (string) ($data['message'] ?? '');

            if ($title !== '' || $message !== '') {
                return [
                    'title' => $title !== '' ? $title : 'Notifikasi',
                    'message' => $message !== '' ? $message : 'Ada notifikasi baru.',
                ];
            }

            if (isset($data['incoming_letter_id'])) {
                $subject = $data['subject'] ?? $data['registration_no'] ?? 'Surat masuk';
                return [
                    'title' => 'Surat masuk',
                    'message' => 'Perlu tindak lanjut: ' . $subject,
                ];
            }

            if (isset($data['outgoing_letter_id'])) {
                $subject = $data['subject'] ?? $data['number'] ?? 'Surat keluar';
                return [
                    'title' => 'Surat keluar',
                    'message' => 'Update proses: ' . $subject,
                ];
            }

            if (isset($data['meeting_id'])) {
                return [
                    'title' => 'Meeting',
                    'message' => $data['meeting_title'] ?? $data['subject'] ?? 'Ada update meeting.',
                ];
            }

            if (isset($data['work_program_id']) || isset($data['workplan_id'])) {
                return [
                    'title' => 'Workplan',
                    'message' => $data['work_program_title'] ?? $data['title'] ?? 'Ada update workplan.',
                ];
            }

            return [
                'title' => 'Notifikasi',
                'message' => 'Ada notifikasi baru.',
            ];
        }
    }

    if (!function_exists('corsecNotificationData')) {
        /**
         * Normalize notification data payload.
         *
         * @param mixed $notification
         * @return array
         */
        function corsecNotificationData($notification)
        {
            $data = $notification->data ?? [];
            if (is_array($data)) {
                return $data;
            }

            if (is_string($data) && $data !== '') {
                $decoded = json_decode($data, true);
                return is_array($decoded) ? $decoded : [];
            }

            return [];
        }
    }

    if (!function_exists('corsecNotificationStatusMaps')) {
        /**
         * Build status maps for all related models inside notification collection.
         *
         * @param \Illuminate\Support\Collection $notifications
         * @return array
         */
        function corsecNotificationStatusMaps($notifications)
        {
            $incomingIds = $notifications
                ->map(function ($notification) {
                    $data = corsecNotificationData($notification);
                    return $data['incoming_letter_id'] ?? null;
                })
                ->filter()
                ->unique()
                ->values();

            $outgoingIds = $notifications
                ->map(function ($notification) {
                    $data = corsecNotificationData($notification);
                    return $data['outgoing_letter_id'] ?? null;
                })
                ->filter()
                ->unique()
                ->values();

            $workplanIds = $notifications
                ->map(function ($notification) {
                    $data = corsecNotificationData($notification);
                    return $data['work_program_id'] ?? $data['workplan_id'] ?? null;
                })
                ->filter()
                ->unique()
                ->values();

            $meetingIds = $notifications
                ->map(function ($notification) {
                    $data = corsecNotificationData($notification);
                    return $data['meeting_id'] ?? null;
                })
                ->filter()
                ->unique()
                ->values();

            $incomingStatusMap = $incomingIds->isEmpty()
                ? collect()
                : \Modules\Corsec\Models\IncomingLetter::query()
                    ->whereIn('id', $incomingIds)
                    ->pluck('status', 'id');

            $outgoingRows = $outgoingIds->isEmpty()
                ? collect()
                : \Modules\Corsec\Models\OutgoingLetter::query()
                    ->select(['id', 'status', 'authorized_status', 'cancelled_at'])
                    ->whereIn('id', $outgoingIds)
                    ->get();

            $outgoingStatusMap = $outgoingRows->pluck('status', 'id');
            $outgoingAuthorizedStatusMap = $outgoingRows->pluck('authorized_status', 'id');
            $outgoingCancelledAtMap = $outgoingRows->mapWithKeys(function ($row) {
                return [(string) $row->id => !empty($row->cancelled_at)];
            });

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

            $outgoingDirCheckerApprovedMap = collect();
            $outgoingComplianceCheckerApprovedMap = collect();
            if ($outgoingIds->isNotEmpty()) {
                $pendingByOutgoingId = \Modules\Corsec\Models\Approval::query()
                    ->select(['approvable_id', 'created_at'])
                    ->where('approvable_type', \Modules\Corsec\Models\OutgoingLetter::class)
                    ->whereIn('approvable_id', $outgoingIds->all())
                    ->where('status', 'pending')
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->get()
                    ->groupBy(function ($row) {
                        return (string) $row->approvable_id;
                    })
                    ->map(function ($rows) {
                        return optional($rows->first())->created_at;
                    });

                $approvedCheckerRows = \Modules\Corsec\Models\Approval::query()
                    ->select(['approvable_id', 'note', 'created_at'])
                    ->where('approvable_type', \Modules\Corsec\Models\OutgoingLetter::class)
                    ->whereIn('approvable_id', $outgoingIds->all())
                    ->where('status', 'approved')
                    ->where(function ($query) {
                        $query->where('note', 'ilike', 'EO Direktorat Approved%')
                            ->orWhere('note', 'ilike', 'EO Kepatuhan Approved%');
                    })
                    ->orderBy('created_at')
                    ->get()
                    ->groupBy(function ($row) {
                        return (string) $row->approvable_id;
                    });

                foreach ($outgoingIds as $outgoingId) {
                    $key = (string) $outgoingId;
                    $pendingAt = $pendingByOutgoingId->get($key);
                    $rows = $approvedCheckerRows->get($key, collect());

                    if ($pendingAt) {
                        $pendingAtCarbon = \Illuminate\Support\Carbon::parse($pendingAt);
                        $rows = $rows->filter(function ($row) use ($pendingAtCarbon) {
                            return \Illuminate\Support\Carbon::parse($row->created_at)->gte($pendingAtCarbon);
                        });
                    }

                    $outgoingDirCheckerApprovedMap->put($key, $rows->contains(function ($row) {
                        return \Illuminate\Support\Str::startsWith((string) $row->note, 'EO Direktorat Approved');
                    }));

                    $outgoingComplianceCheckerApprovedMap->put($key, $rows->contains(function ($row) {
                        return \Illuminate\Support\Str::startsWith((string) $row->note, 'EO Kepatuhan Approved');
                    }));
                }
            }

            $workplanRequiresCheckerMap = collect();
            $workplanCheckerApprovedMap = collect();
            if ($workplanIds->isNotEmpty()) {
                $pendingByWorkplanId = \Modules\Corsec\Models\Approval::query()
                    ->select(['approvable_id', 'note', 'created_at'])
                    ->where('approvable_type', \Modules\Corsec\Models\WorkProgram::class)
                    ->whereIn('approvable_id', $workplanIds->all())
                    ->where('status', 'pending')
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->get()
                    ->groupBy(function ($row) {
                        return (string) $row->approvable_id;
                    })
                    ->map(function ($rows) {
                        return $rows->first();
                    });

                $checkerApprovedRows = \Modules\Corsec\Models\Approval::query()
                    ->select(['approvable_id', 'note', 'created_at'])
                    ->where('approvable_type', \Modules\Corsec\Models\WorkProgram::class)
                    ->whereIn('approvable_id', $workplanIds->all())
                    ->where('status', 'approved')
                    ->where('note', 'ilike', 'EO Direktorat Approved%')
                    ->orderBy('created_at')
                    ->get()
                    ->groupBy(function ($row) {
                        return (string) $row->approvable_id;
                    });

                foreach ($workplanIds as $workplanId) {
                    $key = (string) $workplanId;
                    $pending = $pendingByWorkplanId->get($key);
                    $requiresChecker = true;
                    $rows = $checkerApprovedRows->get($key, collect());

                    if ($pending) {
                        $pendingNote = \Illuminate\Support\Str::lower((string) ($pending->note ?? ''));
                        $requiresChecker = \Illuminate\Support\Str::contains($pendingNote, 'eo dan dd direktorat');
                        $pendingAtCarbon = \Illuminate\Support\Carbon::parse($pending->created_at);
                        $rows = $rows->filter(function ($row) use ($pendingAtCarbon) {
                            return \Illuminate\Support\Carbon::parse($row->created_at)->gte($pendingAtCarbon);
                        });
                    }

                    $workplanRequiresCheckerMap->put($key, $requiresChecker);
                    $workplanCheckerApprovedMap->put($key, $rows->isNotEmpty());
                }
            }

            $meetingDirCheckerApprovedMap = collect();
            if ($meetingIds->isNotEmpty()) {
                $pendingByMeetingId = \Modules\Corsec\Models\Approval::query()
                    ->select(['approvable_id', 'created_at'])
                    ->where('approvable_type', \Modules\Corsec\Models\Meeting::class)
                    ->whereIn('approvable_id', $meetingIds->all())
                    ->where('status', 'pending')
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->get()
                    ->groupBy(function ($row) {
                        return (string) $row->approvable_id;
                    })
                    ->map(function ($rows) {
                        return optional($rows->first())->created_at;
                    });

                $checkerApprovedRows = \Modules\Corsec\Models\Approval::query()
                    ->select(['approvable_id', 'note', 'created_at'])
                    ->where('approvable_type', \Modules\Corsec\Models\Meeting::class)
                    ->whereIn('approvable_id', $meetingIds->all())
                    ->where('status', 'approved')
                    ->where('note', 'ilike', 'EO Direktorat Approved%')
                    ->orderBy('created_at')
                    ->get()
                    ->groupBy(function ($row) {
                        return (string) $row->approvable_id;
                    });

                foreach ($meetingIds as $meetingId) {
                    $key = (string) $meetingId;
                    $pendingAt = $pendingByMeetingId->get($key);
                    $rows = $checkerApprovedRows->get($key, collect());

                    if ($pendingAt) {
                        $pendingAtCarbon = \Illuminate\Support\Carbon::parse($pendingAt);
                        $rows = $rows->filter(function ($row) use ($pendingAtCarbon) {
                            return \Illuminate\Support\Carbon::parse($row->created_at)->gte($pendingAtCarbon);
                        });
                    }

                    $meetingDirCheckerApprovedMap->put($key, $rows->isNotEmpty());
                }
            }

            return [
                'incoming' => $incomingStatusMap,
                'outgoing' => $outgoingStatusMap,
                'outgoing_authorized_status' => $outgoingAuthorizedStatusMap,
                'outgoing_cancelled_at' => $outgoingCancelledAtMap,
                'workplan' => $workplanStatusMap,
                'meeting' => $meetingStatusMap,
                'outgoing_dir_checker_approved' => $outgoingDirCheckerApprovedMap,
                'outgoing_compliance_checker_approved' => $outgoingComplianceCheckerApprovedMap,
                'workplan_requires_checker' => $workplanRequiresCheckerMap,
                'workplan_checker_approved' => $workplanCheckerApprovedMap,
                'meeting_dir_checker_approved' => $meetingDirCheckerApprovedMap,
            ];
        }
    }

    if (!function_exists('corsecIsNotificationActionable')) {
        /**
         * Decide whether notification should still be visible/unread.
         *
         * @param mixed $notification
         * @param array $statusMaps
         * @return bool
         */
        function corsecIsNotificationActionable($notification, array $statusMaps)
        {
            $data = corsecNotificationData($notification);
            $type = (string) ($data['notification_type'] ?? '');
            $snapshotStatus = trim((string) ($data['status'] ?? ''));

            $matchesSnapshotStatus = static function (string $currentStatus) use ($snapshotStatus): bool {
                if ($snapshotStatus === '') {
                    return true;
                }

                return $snapshotStatus === $currentStatus;
            };

            $incomingId = $data['incoming_letter_id'] ?? null;
            if ($incomingId) {
                $status = (string) ($statusMaps['incoming']->get((string) $incomingId) ?? '');
                if ($status === '') {
                    return false;
                }

                $incomingTerminalStatuses = [
                    \Modules\Corsec\Models\IncomingLetter::STATUS_VERIFIED,
                    \Modules\Corsec\Models\IncomingLetter::STATUS_REJECTED,
                ];

                switch ($type) {
                    case 'incoming_letter_dir_approval':
                        return $status === \Modules\Corsec\Models\IncomingLetter::STATUS_WAITING_DIR_APPROVAL;
                    case 'incoming_letter_eo_corp_affair':
                        return in_array($status, [
                            \Modules\Corsec\Models\IncomingLetter::STATUS_ON_APPROVAL,
                            \Modules\Corsec\Models\IncomingLetter::STATUS_DISPATCHED,
                        ], true);
                    case 'incoming_letter_dir_circulation':
                        return !in_array($status, [
                            \Modules\Corsec\Models\IncomingLetter::STATUS_VERIFIED,
                            \Modules\Corsec\Models\IncomingLetter::STATUS_REJECTED,
                        ], true) && $matchesSnapshotStatus($status);
                    case 'incoming_letter_action':
                        return !in_array($status, $incomingTerminalStatuses, true) && $matchesSnapshotStatus($status);
                    default:
                        return !in_array($status, $incomingTerminalStatuses, true) && $matchesSnapshotStatus($status);
                }
            }

            $outgoingId = $data['outgoing_letter_id'] ?? null;
            if ($outgoingId) {
                $status = (string) ($statusMaps['outgoing']->get((string) $outgoingId) ?? '');
                if ($status === '') {
                    return false;
                }

                $authorizedStatus = \Illuminate\Support\Str::lower((string) (($statusMaps['outgoing_authorized_status'] ?? collect())->get((string) $outgoingId) ?? ''));
                $hasCancelledAt = (bool) (($statusMaps['outgoing_cancelled_at'] ?? collect())->get((string) $outgoingId) ?? false);

                $isCancelled = $status === \Modules\Corsec\Models\OutgoingLetter::STATUS_CANCELLED
                    || $authorizedStatus === 'cancelled'
                    || $hasCancelledAt;
                if ($isCancelled) {
                    return false;
                }

                $outgoingTerminalStatuses = [
                    \Modules\Corsec\Models\OutgoingLetter::STATUS_VERIFIED,
                    \Modules\Corsec\Models\OutgoingLetter::STATUS_CANCELLED,
                    'done',
                    'completed',
                    'sent',
                ];

                $message = \Illuminate\Support\Str::lower((string) ($data['message'] ?? ''));
                $title = \Illuminate\Support\Str::lower((string) ($data['title'] ?? ''));

                switch ($type) {
                    case 'outgoing_letter_dir_approval':
                        if ($status !== \Modules\Corsec\Models\OutgoingLetter::STATUS_WAITING_DIR_APPROVAL) {
                            return false;
                        }
                        $checkerApproved = (bool) (($statusMaps['outgoing_dir_checker_approved'] ?? collect())->get((string) $outgoingId) ?? false);
                        $isDdStage = \Illuminate\Support\Str::contains($message, 'dd direktorat')
                            || \Illuminate\Support\Str::contains($title, 'dd direktorat');
                        return $isDdStage ? $checkerApproved : !$checkerApproved;
                    case 'outgoing_letter_cancel_approval':
                        return $status === \Modules\Corsec\Models\OutgoingLetter::STATUS_WAITING_CANCEL_APPROVAL;
                    case 'outgoing_letter_compliance_review':
                        return $status === \Modules\Corsec\Models\OutgoingLetter::STATUS_COMPLIANCE_REVIEW;
                    case 'outgoing_letter_compliance_approval':
                        if ($status !== \Modules\Corsec\Models\OutgoingLetter::STATUS_WAITING_COMPLIANCE_APPROVAL) {
                            return false;
                        }
                        $checkerApproved = (bool) (($statusMaps['outgoing_compliance_checker_approved'] ?? collect())->get((string) $outgoingId) ?? false);
                        $isDdStage = \Illuminate\Support\Str::contains($message, 'dd kepatuhan')
                            || \Illuminate\Support\Str::contains($title, 'dd kepatuhan');
                        return $isDdStage ? $checkerApproved : !$checkerApproved;
                    case 'outgoing_letter_corpsec_approval':
                        return $status === \Modules\Corsec\Models\OutgoingLetter::STATUS_WAITING_VERIFICATION;
                    case 'outgoing_letter_final_upload':
                        return $status === \Modules\Corsec\Models\OutgoingLetter::STATUS_WAITING_FINAL_UPLOAD
                            || $status === 'final_uploaded';
                    case 'outgoing_letter_action':
                        return !in_array($status, $outgoingTerminalStatuses, true) && $matchesSnapshotStatus($status);
                    default:
                        return !in_array($status, $outgoingTerminalStatuses, true) && $matchesSnapshotStatus($status);
                }
            }

            $workplanId = $data['work_program_id'] ?? $data['workplan_id'] ?? null;
            if ($workplanId) {
                $status = (string) ($statusMaps['workplan']->get((string) $workplanId) ?? '');
                if ($status === '') {
                    return false;
                }

                switch ($type) {
                    case 'workplan_dir_approval':
                    case 'workplan_update_dir_approval':
                        if ($status !== \Modules\Corsec\Models\WorkProgram::STATUS_WAITING_DIR_APPROVAL) {
                            return false;
                        }
                        $requiresChecker = (bool) (($statusMaps['workplan_requires_checker'] ?? collect())->get((string) $workplanId) ?? true);
                        $checkerApproved = (bool) (($statusMaps['workplan_checker_approved'] ?? collect())->get((string) $workplanId) ?? false);
                        return $requiresChecker && !$checkerApproved;
                    case 'workplan_dd_approval':
                    case 'workplan_update_dd_approval':
                        if ($status !== \Modules\Corsec\Models\WorkProgram::STATUS_WAITING_DIR_APPROVAL) {
                            return false;
                        }
                        $requiresChecker = (bool) (($statusMaps['workplan_requires_checker'] ?? collect())->get((string) $workplanId) ?? true);
                        $checkerApproved = (bool) (($statusMaps['workplan_checker_approved'] ?? collect())->get((string) $workplanId) ?? false);
                        return !$requiresChecker || $checkerApproved;
                    case 'workplan_action':
                        return $status === \Modules\Corsec\Models\WorkProgram::STATUS_RETURNED && $matchesSnapshotStatus($status);
                    default:
                        return in_array($status, [
                            \Modules\Corsec\Models\WorkProgram::STATUS_WAITING_DIR_APPROVAL,
                            \Modules\Corsec\Models\WorkProgram::STATUS_RETURNED,
                        ], true) && $matchesSnapshotStatus($status);
                }
            }

            $meetingId = $data['meeting_id'] ?? null;
            if ($meetingId) {
                $status = (string) ($statusMaps['meeting']->get((string) $meetingId) ?? '');
                if ($status === '') {
                    return false;
                }

                $meetingTerminalStatuses = [
                    \Modules\Corsec\Models\Meeting::STATUS_DONE_TINDAKLANJUT_HASIL_RAPAT,
                    'done',
                    'completed',
                    'closed',
                    'verified',
                ];

                $message = \Illuminate\Support\Str::lower((string) ($data['message'] ?? ''));
                $title = \Illuminate\Support\Str::lower((string) ($data['title'] ?? ''));

                switch ($type) {
                    case 'meeting_corsec_approval':
                        return $status === \Modules\Corsec\Models\Meeting::STATUS_WAITING_CORSEC_APPROVAL;
                    case 'meeting_directorate_approval':
                        if ($status !== \Modules\Corsec\Models\Meeting::STATUS_WAITING_DIREKTORAT_APPROVAL) {
                            return false;
                        }
                        $checkerApproved = (bool) (($statusMaps['meeting_dir_checker_approved'] ?? collect())->get((string) $meetingId) ?? false);
                        $isDdStage = \Illuminate\Support\Str::contains($message, 'dd direktorat')
                            || \Illuminate\Support\Str::contains($title, 'dd direktorat');
                        return $isDdStage ? $checkerApproved : !$checkerApproved;
                    case 'meeting_minutes_final':
                        return $status === \Modules\Corsec\Models\Meeting::STATUS_NOTULEN_FINAL;
                    case 'meeting_followup_done':
                        return false;
                    case 'meeting_corsec_action':
                    case 'meeting_directorate_action':
                    default:
                        return !in_array($status, $meetingTerminalStatuses, true) && $matchesSnapshotStatus($status);
                }
            }

            return true;
        }
    }

    if (!function_exists('corsecFilterActionableNotifications')) {
        /**
         * Filter unread notifications to active/actionable items only.
         *
         * @param \Illuminate\Support\Collection|array $notifications
         * @return \Illuminate\Support\Collection
         */
        function corsecFilterActionableNotifications($notifications)
        {
            $collection = $notifications instanceof \Illuminate\Support\Collection
                ? $notifications
                : collect($notifications);

            if ($collection->isEmpty()) {
                return collect();
            }

            $statusMaps = corsecNotificationStatusMaps($collection);

            return $collection
                ->filter(function ($notification) use ($statusMaps) {
                    return corsecIsNotificationActionable($notification, $statusMaps);
                })
                ->values();
        }
    }

    if (!function_exists('corsecAutoReadResolvedNotifications')) {
        /**
         * Mark resolved unread notifications as read, so they disappear automatically.
         *
         * @param \Illuminate\Support\Collection|array $notifications
         * @return int
         */
        function corsecAutoReadResolvedNotifications($notifications)
        {
            $collection = $notifications instanceof \Illuminate\Support\Collection
                ? $notifications
                : collect($notifications);

            if ($collection->isEmpty()) {
                return 0;
            }

            $statusMaps = corsecNotificationStatusMaps($collection);

            $resolvedIds = $collection
                ->filter(function ($notification) use ($statusMaps) {
                    return empty($notification->read_at) && !corsecIsNotificationActionable($notification, $statusMaps);
                })
                ->pluck('id')
                ->filter()
                ->values()
                ->all();

            if (empty($resolvedIds)) {
                return 0;
            }

            \Illuminate\Support\Facades\DB::table('notifications')
                ->whereIn('id', $resolvedIds)
                ->whereNull('read_at')
                ->update([
                    'read_at' => now(),
                    'updated_at' => now(),
                ]);

            return count($resolvedIds);
        }
    }

    include __DIR__ . '/hmac.php';
