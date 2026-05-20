<?php

namespace App\Http\Controllers;

use App\Models\SystemRelease;
use Illuminate\Http\Request;

class SystemReleaseController extends Controller
{
    public function index()
    {
        // Lista as versões mais recentes primeiro
        $releases = SystemRelease::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.system_releases.index', compact('releases'));
    }

    public function create()
    {
        return view('admin.system_releases.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'version' => 'required|string|unique:system_releases,version',
            'release_notes' => 'required|string',
        ]);

        SystemRelease::create($validated);

        return redirect()->route('admin.system_releases.index')
            ->with('success', 'Notas da versão cadastradas com sucesso!');
    }

    public function edit(SystemRelease $systemRelease)
    {
        return view('admin.system_releases.edit', compact('systemRelease'));
    }

    public function update(Request $request, SystemRelease $systemRelease)
    {
        $validated = $request->validate([
            'version' => 'required|string|unique:system_releases,version,' . $systemRelease->id,
            'release_notes' => 'required|string',
        ]);

        $systemRelease->update($validated);

        return redirect()->route('admin.system_releases.index')
            ->with('success', 'Notas da versão atualizadas com sucesso!');
    }

    public function destroy(SystemRelease $systemRelease)
    {
        $systemRelease->delete();
        return redirect()->route('admin.system_releases.index')
            ->with('success', 'Registro de versão excluído.');
    }
}