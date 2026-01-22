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
                    'message' => $data['subject'] ?? 'Ada update meeting.',
                ];
            }

            if (isset($data['workplan_id'])) {
                return [
                    'title' => 'Workplan',
                    'message' => $data['title'] ?? 'Ada update workplan.',
                ];
            }

            return [
                'title' => 'Notifikasi',
                'message' => 'Ada notifikasi baru.',
            ];
        }
    }

    include __DIR__ . '/hmac.php';
