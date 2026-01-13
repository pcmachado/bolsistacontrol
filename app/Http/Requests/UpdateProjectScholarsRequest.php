<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectScholarsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $filtered = collect($this->input('scholarships', []))
            ->filter(fn ($s) =>
                !empty($s['position_id']) &&
                !empty($s['weekly_workload'])
            )
            ->values()
            ->all();

        $this->merge([
            'scholarships' => $filtered,
        ]);
    }

    public function rules(): array
    {
        return [
            'scholarships' => ['required', 'array', 'min:1'],

            'scholarships.*.scholarship_holder_id' => [
                'required',
                'exists:scholarship_holders,id',
            ],

            'scholarships.*.position_id' => [
                'required',
                'exists:positions,id',
            ],

            'scholarships.*.weekly_workload' => [
                'required',
                'integer',
                'min:1',
                'max:40',
            ],

            'scholarships.*.status' => [
                'required',
                'in:active,inactive',
            ],
        ];
    }
}
