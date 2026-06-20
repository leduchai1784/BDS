<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchPropertyRequest extends FormRequest
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
            'keyword' => 'nullable|string|max:255',
            'search' => 'nullable|string|max:255',
            'property_type' => 'nullable',
            'type' => 'nullable',
            'purpose' => 'nullable|string|in:rent,sale',
            'transaction_type' => 'nullable|string|in:rent,sale',
            'price' => 'nullable|string',
            'area' => 'nullable|string',
            'bedrooms' => 'nullable|string',
            'bedroom' => 'nullable|string',
            'bathrooms' => 'nullable|string',
            'bathroom' => 'nullable|string',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'district' => 'nullable',
            'ward' => 'nullable|string|max:255',
            'furniture' => 'nullable|string|max:255',
            'direction' => 'nullable|string|max:255',
            'sort' => 'nullable|string|in:latest,price_asc,price_desc,area_asc,area_desc',
        ];
    }
}
