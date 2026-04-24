<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Step4Request extends FormRequest
{
    public function authorize(): bool
    {
        // Aqui depois podemos ligar com Policy
        return true;
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

    /**
     * 🔥 Filtro CRÍTICO
     * Remove bolsistas não selecionados antes da validação
     */
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

    public function messages(): array
    {
        return [
            'scholarships.required' => 'Selecione ao menos um bolsista.',
            'scholarships.*.position_id.required' => 'Informe o cargo do bolsista.',
            'scholarships.*.weekly_workload.required' => 'Informe a carga horária.',
        ];
    }
}
