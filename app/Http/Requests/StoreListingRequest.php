<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user() && $this->user()->isProvider();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50|max:5000',
            'category' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'suburb' => 'required|string|max:100',
            'pricing_type' => 'required|in:hourly,fixed',
            'price' => 'nullable|numeric|min:0|max:99999.99',
            'price_min' => 'nullable|numeric|min:0|max:99999.99',
            'price_max' => 'nullable|numeric|min:0|max:99999.99|gte:price_min',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'description.min' => 'Description must be at least 50 characters to provide adequate information.',
            'price_max.gte' => 'Maximum price must be greater than or equal to minimum price.',
        ];
    }
}

