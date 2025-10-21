<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateSubDomainRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'SUBDOMAIN' => 'required',
            'DB_DATABASE' => 'required',
            'DB_USERNAME' => 'required',
            'DB_PASSWORD' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'DB_DATABASE.required' => 'Database Name is required',
            'DB_USERNAME.required' => 'Database Username is required',
            'DB_PASSWORD.required' => 'Database Password is required',
        ];
    }
}
