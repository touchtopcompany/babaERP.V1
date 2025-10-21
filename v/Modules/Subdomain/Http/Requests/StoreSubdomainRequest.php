<?php

namespace Modules\Subdomain\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Subdomain\Rules\WordCountRule;

class StoreSubdomainRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return string[]
     */
    public function rules()
    {
        return [
            'business_name' => 'required',
            'owner_name' => 'required',
            'owner_username' => ['required', 'unique:core_domain.subdomains,admin_username', new WordCountRule(expected: 1)],
            'owner_email' => ['required', 'email'],
            'owner_password' => ['required', new WordCountRule(expected: 1)],
            'subdomain' => ['required', new WordCountRule(expected: 1)],
            'database' => ['required', 'unique:core_domain.subdomains,db_name', new WordCountRule(expected: 1)],
            'username' => ['required', new WordCountRule(expected: 1)],
            'password' => ['required', new WordCountRule(expected: 1)],
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
            'database.required' => 'Database  is required',
            'database.unique' => 'Database already been used',
            'subdomain.unique' => 'Subdomain already created',
            'username.unique' => 'Username already been used',
            'username.required' => 'Database Username is required',
            'password.required' => 'Database Password is required',
        ];
    }
}
