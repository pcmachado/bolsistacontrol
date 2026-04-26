<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectFundingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $filtered = collect($this->input('fundings', []))
            ->filter(fn ($f) =>
                !empty($f['allocated_amount'])
            )
            ->values()
            ->all();

        $this->merge([
            'fundings' => $filtered,
        ]);
    }

    public function rules(): array
    {
        return [
            'fundings' => ['required', 'array', 'min:1'],
            'fundings.*.funding_source_id' => [
                'required',
                'exists:funding_sources,id',
            ],
            'fundings.*.amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            'fundings.*.status' => [
                'nullable',
                'in:active,inactive',
            ],
        ];
    }
}
