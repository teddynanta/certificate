<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CertificateUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'email' => [
                'sometimes',
                'email',
                'max:255',
            ],

            'code' => [
                'sometimes',
                'max:100',
                'unique:App\Models\Certificate,code',
                'regex:/^\d{1,3}\/DISKOMINFOTIKSAN\/[IVXCLDM]+(\.[IVXCLDM]+)?\/\d{4}$/'
            ],

            'issued_date' => [
                'sometimes',
                'date'
            ]
        ];
    }
}
