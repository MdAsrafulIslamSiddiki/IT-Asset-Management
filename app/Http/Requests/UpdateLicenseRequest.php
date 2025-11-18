<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLicenseRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $licenseId = $this->route('license')->id;

        return [
            'name' => 'required|string|max:255',
            'vendor' => 'required|string|max:255',
            'license_key' => ['required', 'string', 'max:255', Rule::unique('licenses')->ignore($licenseId)],
            'license_type' => 'required|in:per-user,per-device,site-license',
            'total_quantity' => 'required|integer|min:1',
            'purchase_date' => 'required|date',
            'expiry_date' => 'required|date|after:purchase_date',
            'cost_per_license' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Software name is required',
            'vendor.required' => 'Vendor/Publisher is required',
            'license_key.required' => 'License key is required',
            'license_key.unique' => 'This license key already exists',
            'license_type.required' => 'License type is required',
            'total_quantity.required' => 'Total quantity is required',
            'total_quantity.min' => 'Total quantity must be at least 1',
            'purchase_date.required' => 'Purchase date is required',
            'purchase_date.date' => 'Purchase date must be a valid date',
            'expiry_date.required' => 'Expiry date is required',
            'expiry_date.date' => 'Expiry date must be a valid date',
            'expiry_date.after' => 'Expiry date must be after purchase date',
            'cost_per_license.required' => 'Cost per license is required',
            'cost_per_license.min' => 'Cost per license cannot be negative',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Convert date format from YYYY-MM-DD to m/d/Y for storage
        if ($this->purchase_date) {
            $this->merge([
                'purchase_date_formatted' => date('n/j/Y', strtotime($this->purchase_date)),
            ]);
        }

        if ($this->expiry_date) {
            $this->merge([
                'expiry_date_formatted' => date('n/j/Y', strtotime($this->expiry_date)),
            ]);
        }
    }
}
