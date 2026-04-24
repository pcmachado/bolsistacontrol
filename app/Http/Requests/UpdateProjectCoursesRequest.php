<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectCoursesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $filtered = collect($this->input('courses', []))
            ->filter(fn ($c) =>
                isset($c['active']) ||
                !empty($c['semester']) ||
                !empty($c['year'])
            )
            ->values()
            ->all();

        $this->merge([
            'courses' => $filtered,
        ]);
    }

    public function rules(): array
    {
        return [
            'courses' => ['required', 'array', 'min:1'],
            'courses.*.course_id' => ['required', 'exists:courses,id'],
            'courses.*.active' => ['required', 'boolean'],
            'courses.*.semester' => ['nullable', 'integer'],
            'courses.*.year' => ['nullable', 'integer'],
        ];
    }
}
