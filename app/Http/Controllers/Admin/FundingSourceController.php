<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundingSource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FundingSourceController extends Controller
{
    public function index()
    {
        $fundingSources = FundingSource::query()
            ->when(request('name'), fn ($query, $name) => $query->where('name', 'like', "%{$name}%"))
            ->orderBy('name')
            ->paginate(15);

        return view('admin.funding-sources.index', compact('fundingSources'));
    }

    public function create()
    {
        return view('admin.funding-sources.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateFundingSource($request);

        $funding = FundingSource::create($validated);

        if ($request->ajax()) {
            return response()->json($funding);
        }

        return $this->redirectAfterSave($request)
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
        $validated = $this->validateFundingSource($request, $fundingSource);

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

    protected function validateFundingSource(Request $request, ?FundingSource $fundingSource = null): array
    {
        $id = $fundingSource?->id;

        return $request->validate([
            'name' => 'required|string|max:255|unique:funding_sources,name'.($id ? ",{$id}" : ''),
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:internal,external',
            'description' => 'nullable|string|max:1000',
            'contact_info' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:1000',
            'total_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'active' => 'nullable|boolean',
        ]);
    }

    protected function redirectAfterSave(Request $request)
    {
        $redirectTo = $request->input('redirect_to');

        if ($redirectTo && Str::startsWith($redirectTo, url('/'))) {
            return redirect()->to($redirectTo);
        }

        return redirect()->route('admin.funding-sources.index');
    }
}
