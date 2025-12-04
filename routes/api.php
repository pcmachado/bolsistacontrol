<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/api/notifications/unread-count', function () {
    return ['count' => auth()->user()->unreadNotifications()->count()];
});
