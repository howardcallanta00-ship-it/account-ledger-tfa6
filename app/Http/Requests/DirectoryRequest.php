<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class DirectoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson()) {
            parent::failedValidation($validator);
        }
        throw new HttpResponseException(
            response()->view('errors.validation', ['messages' => $validator->errors()->all()], 422)
        );
    }

    public function rules(): array
    {
        $columns = $this->routeIs('customers.index')
            ? ['id', 'full_name', 'email', 'phone'] : ['id', 'username', 'full_name'];

        return [
            'q' => ['nullable', 'string', 'max:100'],
            'sort' => ['sometimes', Rule::in($columns)],
            'direction' => ['sometimes', Rule::in(['asc', 'desc'])],
        ];
    }
}
