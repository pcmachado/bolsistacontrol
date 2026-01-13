<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundingSource;
use Illuminate\Http\Request;

class FundingSourceController extends Controller
{
    public function index()
    {
        $fundings = FundingSource::orderBy('name')->get();
        return view('admin.funding-sources.index', compact('fundings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:funding_sources,name',
            'description' => 'nullable|string|max:1000',
        ]);

        $funding = FundingSource::create($validated);

        if ($request->ajax()) {
            return response()->json($funding);
        }

        return redirect()->route('admin.funding-sources.index')
                         ->with('success', 'Fonte de fomento criada com sucesso!');
    }

    public function show(FundingSource $fundingSource)
    {
        return view('admin.funding-sources.show', compact('fundingSource'));
    }

    public function edit(FundingSource $fundingSource)
    {
        return view('admin.funding-sources.edit', compact('fundingSource'));
    }

    public function update(Request $request, FundingSource $fundingSource)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:funding_sources,name,' . $fundingSource->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $fundingSource->update($validated);

        return redirect()->route('admin.funding-sources.index')
                         ->with('success', 'Fonte de fomento atualizada com sucesso!');
    }

    public function destroy(FundingSource $fundingSource)
    {
        $fundingSource->delete();

        return redirect()->route('admin.funding-sources.index')
                         ->with('success', 'Fonte de fomento excluída com sucesso!');
    }

}
