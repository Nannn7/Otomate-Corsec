<?php
    use Carbon\Carbon;

    if(!function_exists('formatTanggalWaktu')){
        function formatTanggalWaktu($tanggal, $time=false, $showDay=false, $locale = 'id_ID')
        {
            // Parse tanggal dan waktu
            $carbon = Carbon::parse($tanggal)->locale($locale);

            // Tentukan format berdasarkan parameter
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
