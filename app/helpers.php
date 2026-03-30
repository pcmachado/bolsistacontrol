<?php

use Carbon\Carbon;
use App\Models\Institution;
use App\Models\FinancialClosure;

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

if (!function_exists('is_financial_closed')) {
    function is_financial_closed($unitId, $month, $year) {
        return FinancialClosure::isClosed($unitId, $month, $year);
    }
}

if (!function_exists('hoursToTime')) {
    function hoursToTime($value)
    {
        $h = floor($value);
        $m = round(($value - $h) * 60);
        return sprintf('%02d:%02d', $h, $m);
    }
}

if (!function_exists('imageToBase64')) {
    function imageToBase64($path)
    {
        if (!file_exists($path)) return null;

        $type = pathinfo($path, PATHINFO_EXTENSION);
        return 'data:image/'.$type.';base64,'.base64_encode(file_get_contents($path));
    }
}