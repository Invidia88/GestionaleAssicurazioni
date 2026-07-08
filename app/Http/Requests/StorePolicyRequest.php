<?php

namespace App\Http\Requests;

use App\Models\Policy;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePolicyRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'insurance_company_id' => ['required', 'exists:insurance_companies,id'],
            'type' => ['required', 'string', Rule::in(Policy::TYPES)],
            'number' => ['required', 'string', 'max:255', 'unique:policies,number'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'annual_premium' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'status' => ['required', 'string', Rule::in(array_keys(Policy::STATUSES))],
            'notes' => ['nullable', 'string'],
        ];
    }
}
