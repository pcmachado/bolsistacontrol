<?php

namespace App\Console\Commands;

use App\Services\Release\GitReleaseService;
use App\Services\Release\ReleaseParserService;
use Illuminate\Console\Command;

class GenerateReleaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-release-command {version? : Versão a ser importada. Se omitida, usa version.txt ou a tag atual}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa ou atualiza uma release automaticamente a partir do Git';

    /**
     * Execute the console command.
     */
    public function handle(GitReleaseService $git, ReleaseParserService $parser)
    {
        $version = $this->argument('version');

        if (! $version) {
            $version = $this->anticipate(
                'Versão da release',
                array_filter([$git->currentVersion(), $git->latestTag()])
            );
        }

        try {
            $release = $git->importRelease($parser, $version);
        } catch (\RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Release {$release->version} importada com sucesso.");

        return self::SUCCESS;
    }
}
