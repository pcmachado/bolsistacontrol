<?php

use Carbon\Carbon;

if (! function_exists('formatDate')) {
    function formatDate($date, $format = 'd/m/Y')
    {
        return $date ? Carbon::parse($date)->format($format) : null;
    }
}

if (! function_exists('formatDateTime')) {
    function formatDateTime($date, $format = 'd/m/Y H:i')
    {
        return $date ? Carbon::parse($date)->format($format) : null;
    }
}