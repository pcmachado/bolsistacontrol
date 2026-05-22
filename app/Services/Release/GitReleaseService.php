<?php

namespace App\Services\Release;

use App\Models\SystemRelease;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\Process\Process;

class GitReleaseService
{
    public function importRelease(ReleaseParserService $parser, ?string $version = null): SystemRelease
    {
        if (! $this->isAvailable()) {
            throw new RuntimeException('O diretório atual não possui um repositório Git acessível.');
        }

        $resolvedVersion = SystemRelease::normalizeVersion(
            $version ?: $this->currentVersion()
        );

        $existingRelease = SystemRelease::query()
            ->where('version', $resolvedVersion)
            ->first();

        if ($existingRelease && ! $existingRelease->is_automatic) {
            throw new RuntimeException(
                "A versão {$resolvedVersion} já existe e foi editada manualmente. Edite-a pela tela administrativa para evitar sobrescrever o conteúdo."
            );
        }

        $latestTag = $this->latestTag();
        $changes = $parser->parse(
            $this->commitsForVersion($resolvedVersion, $latestTag)
        );

        return SystemRelease::updateOrCreate(
            ['version' => $resolvedVersion],
            [
                'git_tag' => $this->tagForVersion($resolvedVersion, $latestTag),
                'git_hash' => $this->currentHash(),
                'changes' => $changes,
                'release_notes' => view(
                    'admin.system_releases.partials.auto-notes',
                    ['changes' => $changes]
                )->render(),
                'is_visible' => true,
                'is_automatic' => true,
                'released_at' => now(),
            ]
        );
    }

    public function isAvailable(): bool
    {
        return $this->run([
            'git',
            'rev-parse',
            '--is-inside-work-tree',
        ]) === 'true';
    }

    public function currentVersion(): string
    {
        $versionFile = base_path('version.txt');

        if (File::exists($versionFile)) {
            return trim(File::get($versionFile));
        }

        return $this->latestTag()
            ?? ('build-' . ($this->currentHash() ?: 'local'));
    }

    public function latestTag(): ?string
    {
        return $this->run([
            'git',
            'describe',
            '--tags',
            '--abbrev=0',
        ]);
    }

    public function previousTag(): ?string
    {
        $tags = $this->listTags();

        return $tags[1] ?? null;
    }

    public function commitsSinceLastTag(): array
    {
        $tag = $this->latestTag();

        if (! $tag) {
            return $this->recentCommitSubjects();
        }

        return $this->logSubjects("{$tag}..HEAD");
    }

    public function currentHash(): string
    {
        return $this->run([
            'git',
            'rev-parse',
            '--short',
            'HEAD',
        ]) ?? '';
    }

    public function listTags(): array
    {
        $output = $this->run([
            'git',
            'tag',
            '--sort=-creatordate',
        ]);

        if (! $output) {
            return [];
        }

        return array_values(array_filter(
            preg_split('/\r\n|\r|\n/', $output)
        ));
    }

    protected function commitsForVersion(string $version, ?string $latestTag = null): array
    {
        $normalizedLatestTag = $latestTag
            ? SystemRelease::normalizeVersion($latestTag)
            : null;

        if ($latestTag && $normalizedLatestTag === $version) {
            $previousTag = $this->previousTag();

            if ($previousTag) {
                return $this->logSubjects("{$previousTag}..{$latestTag}");
            }
        }

        if ($latestTag && $normalizedLatestTag !== $version) {
            return $this->logSubjects("{$latestTag}..HEAD");
        }

        return $this->recentCommitSubjects();
    }

    protected function recentCommitSubjects(int $limit = 20): array
    {
        return $this->logSubjects(null, $limit);
    }

    protected function logSubjects(?string $range = null, ?int $limit = null): array
    {
        $command = ['git', 'log'];

        if ($limit) {
            $command[] = "-{$limit}";
        }

        if ($range) {
            $command[] = $range;
        }

        $command[] = '--pretty=format:%s';

        $output = $this->run($command);

        if (! $output) {
            return [];
        }

        return array_values(array_filter(
            preg_split('/\r\n|\r|\n/', $output)
        ));
    }

    protected function tagForVersion(string $version, ?string $latestTag = null): ?string
    {
        if (! $latestTag) {
            return null;
        }

        return SystemRelease::normalizeVersion($latestTag) === $version
            ? $latestTag
            : null;
    }

    protected function run(array $command): ?string
    {
        $process = new Process($command, base_path());
        $process->run();

        if (! $process->isSuccessful()) {
            return null;
        }

        $output = trim($process->getOutput());

        return $output !== '' ? $output : null;
    }
}
