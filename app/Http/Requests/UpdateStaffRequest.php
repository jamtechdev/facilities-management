<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('edit staff');
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit staff members. Please contact your administrator if you need this access.'
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
        $staffId = $this->route('staff')->id ?? null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('staff', 'email')->ignore($staffId)],
            'password' => ['nullable', 'string', 'min:8'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'assigned_weekly_hours' => ['nullable', 'numeric', 'min:0'],
            'assigned_monthly_hours' => ['nullable', 'numeric', 'min:0'],
            'client_id' => ['nullable', 'exists:clients,id'],
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
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered as staff.',
            'hourly_rate.numeric' => 'Hourly rate must be a number.',
            'hourly_rate.min' => 'Hourly rate must be at least 0.',
        ];
    }
}
