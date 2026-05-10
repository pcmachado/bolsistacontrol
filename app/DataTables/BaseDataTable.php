<?php

namespace App\DataTables;

use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Services\DataTable;

abstract class BaseDataTable extends DataTable
{
    protected array $filters = [];

    protected function defaultParameters(): array
    {
        return [

            'responsive' => true,
            'autoWidth' => false,

            'language' => [

                'processing' => __('datatables.processing'),
                'search' => __('datatables.search'),
                'lengthMenu' => __('datatables.lengthMenu'),
                'info' => __('datatables.info'),
                'infoEmpty' => __('datatables.infoEmpty'),
                'infoFiltered' => __('datatables.infoFiltered'),
                'loadingRecords' => __('datatables.loadingRecords'),
                'zeroRecords' => __('datatables.zeroRecords'),
                'emptyTable' => __('datatables.emptyTable'),

                'paginate' => [
                    'first' => __('datatables.paginate.first'),
                    'previous' => __('datatables.paginate.previous'),
                    'next' => __('datatables.paginate.next'),
                    'last' => __('datatables.paginate.last'),
                ],

                'aria' => [
                    'sortAscending' => __('datatables.aria.sortAscending'),
                    'sortDescending' => __('datatables.aria.sortDescending'),
                ],
            ],
        ];
    }

    public function setFilters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Aplica filtro de instituição automaticamente baseado no usuário logado
     */
    protected function applyInstitutionFilter($query, $user = null)
    {
        $user = $user ?? Auth::user();

        if (! $user) {
            return $query;
        }

        // Superadmin vê tudo
        if ($user->hasRole('superadmin')) {
            return $query;
        }

        // Filtrar por instituições acessíveis
        if ($user->isInstitutionScoped()) {
            $institutionIds = $user->activeInstitutionIds();

            if ($institutionIds->isNotEmpty()) {
                $query->where(function ($scoped) use ($institutionIds, $query) {
                    // Tentar diferentes campos dependendo do modelo
                    $model = $query->getModel();

                    // Se o modelo tem institution_id diretamente
                    if (\Schema::hasColumn($model->getTable(), 'institution_id')) {
                        $scoped->whereIn('institution_id', $institutionIds);
                    }

                    // Se tem relação com unit
                    if (method_exists($model, 'unit')) {
                        $scoped->orWhereHas('unit', function ($unitQuery) use ($institutionIds) {
                            $unitQuery->whereIn('institution_id', $institutionIds);
                        });
                    }

                    // Se tem relação com project
                    if (method_exists($model, 'project')) {
                        $scoped->orWhereHas('project', function ($projectQuery) use ($institutionIds) {
                            $projectQuery->whereIn('institution_id', $institutionIds);
                        });
                    }
                });
            }
        }

        // Filtrar por unidades visíveis
        elseif ($user->isUnitScoped()) {
            $unitIds = $user->visibleUnitIds();

            if ($unitIds->isNotEmpty()) {
                $query->where(function ($scoped) use ($unitIds, $query) {
                    $model = $query->getModel();

                    // Se o modelo tem unit_id diretamente
                    if (\Schema::hasColumn($model->getTable(), 'unit_id')) {
                        $scoped->whereIn('unit_id', $unitIds);
                    }

                    // Se tem relação com unit
                    if (method_exists($model, 'unit')) {
                        $scoped->orWhereHas('unit', function ($unitQuery) use ($unitIds) {
                            $unitQuery->whereIn('unit_id', $unitIds);
                        });
                    }
                });
            }
        }

        return $query;
    }

    /**
     * Aplica filtro de projeto se especificado
     */
    protected function applyProjectFilter($query, $projectId = null)
    {
        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        return $query;
    }

    /**
     * Aplica filtros customizados da requisição
     */
    protected function applyCustomFilters($query, array $allowedFilters = [])
    {
        foreach ($allowedFilters as $filter) {
            if (! empty($this->filters[$filter])) {
                $method = 'apply'.ucfirst($filter).'Filter';
                if (method_exists($this, $method)) {
                    $query = $this->$method($query, $this->filters[$filter]);
                }
            }
        }

        return $query;
    }
}
