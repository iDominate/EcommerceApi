<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "product_name"=>'required|exists:products,name|distinct',
            "unit_price"=>"numeric|exists:products,unit_price",
            "product_unit_count"=>"numeric",
            "increment_or_decrement"=>"alpha|in:inc,dec",
            "by"=>"numeric"
        ];
    }

    public function messages()
    {
        return [
            "product_name.required"=>"Product name required",
            "product_name.exists"=>"Product is not valid",
            "product_name"=>"Product already exists",
            "product_unit_count"=>"Product quantity required"
        ];

    }
}
