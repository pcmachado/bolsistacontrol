<?php

use Carbon\Carbon;
use App\Models\Institution;

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

if (!function_exists('activeInstitution')) {
    function activeInstitution()
    {
        $id = session('institution_id');
        return $id ? Institution::find($id) : null;
    }
}