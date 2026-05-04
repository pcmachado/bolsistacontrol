<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Arr;

class ProjectService
{
    /**
     * Cria um novo projeto.
     *
     * @param  array  $data  Dados do projeto (name, description)
     */
    public function createProject(array $data): Project
    {
        return Project::create([
            'name' => $data['name'],
            'description' => $data['description'],
        ]);
    }

    /**
     * Atualiza um projeto existente.
     */
    public function updateProject(Project $project, array $data): Project
    {
        $project->update([
            'name' => Arr::get($data, 'name', $project->name),
            'description' => Arr::get($data, 'description', $project->description),
        ]);

        return $project;
    }

    /**
     * Exclui um projeto existente.
     *
     * @var \App\Models\Project
     */
    public function deleteProject(Project $project): void
    {
        if ($project->classOfferings()->exists()) {
            throw new \Exception('Projeto possui turmas vinculadas.');
        }

        $project->update([
            'status' => Project::STATUS_INACTIVE,
        ]);

        $project->delete();
    }
}
