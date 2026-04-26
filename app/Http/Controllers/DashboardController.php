<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ScholarshipHolderDashboardService;

class DashboardController extends Controller
{
    public function index(Request $request, ScholarshipHolderDashboardService $service)
    {
        $data = $service->data(Auth::user(), $request->only(['month', 'year']));

        return view('dashboard', $data);
    }
}
