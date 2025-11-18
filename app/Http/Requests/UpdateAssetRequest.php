<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
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
        // $assetId = $this->route('asset')->id;

        $assetId = $this->route()->parameters()['asset_management'] ?? null;

        // Fallback: if parameter name is different
        if (!$assetId) {
            $assetId = $this->route('asset_management');
        }

        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:Laptop,Phone,Monitor,Tablet,Printer,Keyboard,Mouse,Headset,Other',
            'serial_number' => ['required', 'string', 'max:255', Rule::unique('assets')->ignore($assetId)],
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'warranty_expiry' => 'required|date|after:purchase_date',
            'value' => 'required|numeric|min:0|max:999999.99',
            'condition' => 'required|in:excellent,good,fair,poor',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Asset name is required',
            'name.max' => 'Asset name cannot exceed 255 characters',
            'type.required' => 'Asset type is required',
            'type.in' => 'Invalid asset type selected',
            'serial_number.required' => 'Serial number is required',
            'serial_number.unique' => 'This serial number already exists',
            'brand.required' => 'Brand is required',
            'model.required' => 'Model is required',
            'purchase_date.required' => 'Purchase date is required',
            'purchase_date.date' => 'Purchase date must be a valid date',
            'warranty_expiry.required' => 'Warranty expiry date is required',
            'warranty_expiry.date' => 'Warranty expiry must be a valid date',
            'warranty_expiry.after' => 'Warranty expiry must be after purchase date',
            'value.required' => 'Asset value is required',
            'value.numeric' => 'Asset value must be a number',
            'value.min' => 'Asset value cannot be negative',
            'value.max' => 'Asset value is too large',
            'condition.required' => 'Asset condition is required',
            'condition.in' => 'Invalid condition selected',
            'notes.max' => 'Notes cannot exceed 2000 characters',
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

        if ($this->warranty_expiry) {
            $this->merge([
                'warranty_expiry_formatted' => date('n/j/Y', strtotime($this->warranty_expiry)),
            ]);
        }
    }
}
