<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'serial_number' => 'required|string|unique:assets,serial_number',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'purchase_date' => 'required|string',
            'warranty_expiry' => 'required|string',
            'value' => 'required|numeric|min:0',
            'condition' => 'required|in:excellent,good,fair,poor',
            'notes' => 'nullable|string',
        ];
    }
}
