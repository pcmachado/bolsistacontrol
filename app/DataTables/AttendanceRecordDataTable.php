<?php

namespace App\DataTables;

use App\Models\AttendanceRecord;
use App\Services\VisibilityService;
use Illuminate\Support\Facades\Auth;

class AttendanceRecordDataTable extends BaseAttendanceDataTable
{
    protected array $filters = [];

    public string $mode = 'admin';

    public function dataTable($query)
    {
        return $this->buildDataTable($query)
            ->addColumn('user', fn ($record) =>
                $record->scholarshipHolder?->user?->name ?? '-'
            );
    }

    public function setMode(string $mode): self
    {
        $this->mode = $mode;
        return $this;
    }

    public function query(AttendanceRecord $model)
    {
        $user = Auth::user();

        $query = $model->newQuery()
            ->with(['project', 'scholarshipHolder.user', 'submission']);

        $context = $this->mode === 'self' ? 'self' : 'admin';

        $query = app(VisibilityService::class)->apply($query, $user, $context);
        $query = $this->applyFilters($query);

        return $query->latest('date');
    }

    protected function getColumns(): array
    {
        return [
            ['data' => 'user', 'title' => 'Bolsista'],
            ...parent::getColumns(),
        ];
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('attendance-table')
            ->minifiedAjax()
            ->parameters($this->defaultParameters())
            ->columns($this->getColumns());
    }
}
