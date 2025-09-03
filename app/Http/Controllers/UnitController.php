<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use Illuminate\View\View;
use App\Http\Controllers\Controller;

class UnitController extends Controller
{
    public function index(): View
    {
        $unidades = Unit::with('scholarshipHolders')->paginate(15);
        return view('units.index', compact('unidades'));
    }

    public function create(): View
    {
        return view('units.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        Unit::create($request->all());

        return redirect()->route('units.index')->with('success', 'Unidade cadastrada com sucesso!');
    }
}
