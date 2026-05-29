<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;

class DashboardResolverController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $query = request()->getQueryString();
        $target = RouteServiceProvider::home();

        return redirect()->to($query ? $target.'?'.$query : $target);
    }
}
