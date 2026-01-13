<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('edit clients');
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit clients. Please contact your administrator if you need this access.'
            ], 403)
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $clientId = $this->route('client')->id ?? null;

        return [
            'company_name' => ['sometimes', 'required', 'string', 'max:255'],
            'contact_person' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('clients', 'email')->ignore($clientId)],
            'password' => ['nullable', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'agreed_weekly_hours' => ['nullable', 'numeric', 'min:0'],
            'agreed_monthly_hours' => ['nullable', 'numeric', 'min:0'],
            'billing_frequency' => ['nullable', 'in:weekly,monthly,bi-weekly,quarterly'],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'company_name.required' => 'The company name field is required.',
            'contact_person.required' => 'The contact person field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered as a client.',
            'billing_frequency.in' => 'Invalid billing frequency selected.',
            'lead_id.exists' => 'Selected lead does not exist.',
        ];
    }
}
