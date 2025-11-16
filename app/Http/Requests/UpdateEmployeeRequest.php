<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee')->id;

        return [
            'name' => 'required|string|max:255',
            'iqama_id' => ['required', 'string', Rule::unique('employees')->ignore($employeeId)],
            'email' => ['required', 'email', Rule::unique('employees')->ignore($employeeId)],
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'join_date' => 'required|string',
        ];
    }
}
