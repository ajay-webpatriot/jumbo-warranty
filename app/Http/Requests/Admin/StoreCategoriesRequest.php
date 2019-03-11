<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoriesRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:categories,name,'.$this->route('category').',id,deleted_at,NULL',
            'service_charge' => 'required|numeric',
            'status' => 'required',
            // 'products.*.name' => 'required|unique:products,name,'.$this->route('product'),
            // 'products.*.price' => 'required',
        ];
    }
}
