<?php
    use Carbon\Carbon;

    if(!function_exists('dateFormat')){
        /**
         * Formats a date string according to specified parameters.
         *
         * This function uses Carbon to parse a date and format it according to the locale
         * and display preferences provided.
         *
         * @param string|DateTime $tanggal The date to be formatted
         * @param bool $time               Whether to include time in the formatted output (default: false)
         * @param bool $showDay            Whether to include the day name in the formatted output (default: false)
         * @param string $locale           The locale to use for formatting (default: 'id_ID')
         * @return string                  The formatted date string
         */
        function dateFormat($tanggal, $time=false, $showDay=false, $locale = 'id_ID')
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

    if(!function_exists('textTransform')){
        /**
         * Transforms text according to the specified case format.
         *
         * This function applies different text transformations based on the provided case parameter.
         *
         * @param string $text The input text to be transformed
         * @param int $case    The transformation type to apply:
         *                     0 = Default (no transformation)
         *                     1 = lowercase
         *                     2 = UPPERCASE
         *                     3 = Capitalize Each Word (ucwords)
         *                     4 = First letter capitalized (ucfirst)
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



    if(!function_exists('slug')){
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
         * @return string The formatted slug
         */
        function slug($title, $separator = '-', $textTransform = 0)
        {
            $slug = Str::slug($title, $separator);
            return textTransform($slug, $textTransform);
        }
    }

    if(!function_exists('slugToTitle')){
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
