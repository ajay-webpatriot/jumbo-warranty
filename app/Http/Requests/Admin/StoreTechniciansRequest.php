<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTechniciansRequest extends FormRequest
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
            'service_center_id' => 'required',
            'name' => 'required',
            // 'phone' => 'required|numeric|regex:/^[0-9]{10}$/',
            'phone' => 'required|min:11|max:11',
            'address_1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => 'required|min:6|max:6',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'status' => 'required',
        ];
    }
}
