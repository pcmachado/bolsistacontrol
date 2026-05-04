<?php

namespace App\Http\Controllers\Admin;

use App\Models\Assignment;

class AssignmentController
{
    public function index()
    {
        $assignments = Assignment::with(['user', 'project', 'course', 'unit'])->get();

        return view('admin.assignments.index', compact('assignments'));
    }
}
