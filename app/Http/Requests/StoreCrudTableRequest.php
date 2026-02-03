<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreCrudTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],

            'columns' => ['required', 'array', 'min:1'],
            'columns.*.column_name' => ['required', 'string', 'max:255'],
            'columns.*.label' => ['required', 'string', 'max:255'],
            'columns.*.description' => ['nullable', 'string', 'max:1000'],
            'columns.*.field_type' => ['required', 'in:text,select,image'],
            // options virá como string JSON na view; validamos como string aqui
            'columns.*.options' => ['nullable', 'string'],
            'columns.*.css_class' => ['nullable', 'string', 'max:255'],
            'columns.*.icon' => ['nullable', 'string', 'max:255'],
            'columns.*.position' => ['nullable', 'integer', 'min:0'],
            'columns.*.is_required' => ['nullable', 'boolean'],
            'columns.*.is_visible' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('name') && !$this->filled('slug')) {
            $this->merge([
                'slug' => Str::slug($this->input('name')),
            ]);
        }
    }
}
