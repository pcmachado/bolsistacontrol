<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use App\Models\Project;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;

class ProjectService
{
    /**
     * Cria um novo projeto.
     *
     * @param array $data Dados do projeto (name, description)
     * @return Project
     */
    public function createProject(array $data): Project
    {
        return Project::create([
            'name'        => $data['name'],
            'description' => $data['description'],
        ]);
    }
    /**
     * Atualiza um projeto existente.
     *
     * @param Project $project
     * @param array $data
     * @return Project
     */
    public function updateProject(Project $project, array $data): Project
    {
        $project->update([
            'name'        => Arr::get($data, 'name', $project->name),
            'description' => Arr::get($data, 'description', $project->description),
        ]);
        return $project;
    }

    /**
     * Exclui um projeto existente.
     *
     * @param Project $project
     * @return void
     */
    public function deleteProject(Project $project): void
    {
        $project->delete();
    }
}
