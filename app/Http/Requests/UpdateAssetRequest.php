<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $assetId = $this->route('asset')->id;

        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'serial_number' => ['required', 'string', Rule::unique('assets')->ignore($assetId)],
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
