<?php

namespace App\Services;

use App\Models\Institution;
use App\Models\Project;
use App\Models\ScholarshipHolder;
use App\Models\User;

class SuperAdminDashboardService
{
    public function getStats(): array
    {
        return [
            'institutions' => Institution::count('id'),
            'users' => User::count('id'),
            'projects' => Project::count('id'),
            'holders' => ScholarshipHolder::count('id'),
        ];
    }

    public function institutions()
    {
        return Institution::withCount([
            'projects',
            'units',
            'users',
        ])
            ->latest()
            ->get();
    }

    public function recentUsers()
    {
        return User::latest()->take(10)->get();
    }
}
