<?php

namespace App\Services\Release;

class ReleaseParserService
{
    public function parse(array $commits): array
    {
        return collect($commits)
            ->map(function ($commit) {
                $normalizedCommit = strtolower($commit);

                if (str_starts_with($normalizedCommit, 'feat:')) {
                    return [
                        'type' => 'feature',
                        'message' => trim(
                            str_replace('feat:', '', $commit)
                        ),
                    ];
                }

                if (str_starts_with($normalizedCommit, 'fix:')) {
                    return [
                        'type' => 'fix',
                        'message' => trim(
                            str_replace('fix:', '', $commit)
                        ),
                    ];
                }

                if (str_starts_with($normalizedCommit, 'refactor:')) {
                    return [
                        'type' => 'refactor',
                        'message' => trim(
                            str_replace('refactor:', '', $commit)
                        ),
                    ];
                }

                if (
                    str_starts_with($normalizedCommit, 'docs:')
                    || str_starts_with($normalizedCommit, 'chore:')
                    || str_starts_with($normalizedCommit, 'test:')
                    || str_starts_with($normalizedCommit, 'perf:')
                ) {
                    return [
                        'type' => 'maintenance',
                        'message' => trim(
                            (string) preg_replace('/^[a-z]+:/i', '', $commit)
                        ),
                    ];
                }

                return [
                    'type' => 'other',
                    'message' => $commit,
                ];
            })
            ->values()
            ->toArray();
    }
}
