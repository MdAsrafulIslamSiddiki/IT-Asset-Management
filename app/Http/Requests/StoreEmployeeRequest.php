<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Store Employee Request
 * Handles validation for creating new employees
 */
class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     */
    public function authorize(): bool
    {
        // Change to check permissions: return auth()->user()->can('create-employee');
        return true;
    }

    /**
     * Get the validation rules that apply to the request
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:2',
            'iqama_id' => 'required|string|unique:employees,iqama_id|max:255',
            'email' => 'required|email|unique:employees,email|max:255',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'join_date' => 'required|string',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Employee name is required',
            'name.min' => 'Employee name must be at least 2 characters',
            'name.max' => 'Employee name cannot exceed 255 characters',

            'iqama_id.required' => 'Iqama ID is required',
            'iqama_id.unique' => 'This Iqama ID already exists',
            'iqama_id.max' => 'Iqama ID cannot exceed 255 characters',

            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'This email is already registered',
            'email.max' => 'Email cannot exceed 255 characters',

            'department.required' => 'Department is required',
            'department.max' => 'Department name cannot exceed 255 characters',

            'position.required' => 'Position is required',
            'position.max' => 'Position cannot exceed 255 characters',

            'join_date.required' => 'Join date is required',
        ];
    }

    /**
     * Prepare data for validation
     * Sanitize inputs before validation
     */
    protected function prepareForValidation()
    {
        $data = [];

        // Sanitize name
        if ($this->has('name')) {
            $data['name'] = trim($this->name);
        }

        // Sanitize iqama_id
        if ($this->has('iqama_id')) {
            $data['iqama_id'] = trim($this->iqama_id);
        }

        // Sanitize email
        if ($this->has('email')) {
            $data['email'] = strtolower(trim($this->email));
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }
}
