<?php

namespace App\Http\Controllers;

use App\Models\SystemRelease;
use App\Services\Release\GitReleaseService;
use App\Services\Release\ReleaseParserService;
use Illuminate\Http\Request;
use RuntimeException;

class SystemReleaseController extends Controller
{
    public function index(GitReleaseService $git)
    {
        $releases = SystemRelease::orderBy(
            'created_at',
            'desc'
        )->paginate(10);

        $currentVersion = $git->currentVersion()
            ?: SystemRelease::latest()->value('version')
            ?: 'dev';
        $currentVersionSource = $git->currentVersionSource();
        $gitAvailable = $git->isAvailable();

        return view(
            'admin.system_releases.index',
            compact(
                'releases',
                'currentVersion',
                'currentVersionSource',
                'gitAvailable'
            )
        );
    }

    public function create()
    {
        return view('admin.system_releases.create');
    }

    public function importFromGit(GitReleaseService $git, ReleaseParserService $parser)
    {
        try {
            $release = $git->importRelease($parser);
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.system_releases.index')
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.system_releases.index')
            ->with('success', "Versão {$release->version} importada do Git com sucesso.");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'version' => 'required|string|unique:system_releases,version',
            'release_notes' => 'required|string',
        ]);

        $validated['version'] = SystemRelease::normalizeVersion($validated['version']);
        $validated['release_notes'] = strip_tags(
            $validated['release_notes'],
            '<p><br><ul><ol><li><strong><b><em><i><u><a><h4><h5><h6><code>'
        );
        $validated['is_automatic'] = false;
        $validated['released_at'] = now();

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

        $validated['version'] = SystemRelease::normalizeVersion($validated['version']);
        $validated['release_notes'] = strip_tags(
            $validated['release_notes'],
            '<p><br><ul><ol><li><strong><b><em><i><u><a><h4><h5><h6><code>'
        );
        $validated['is_automatic'] = false;

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
