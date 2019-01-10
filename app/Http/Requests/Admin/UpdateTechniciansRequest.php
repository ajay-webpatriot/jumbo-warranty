<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTechniciansRequest extends FormRequest
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
            'phone' => 'required|numeric|regex:/^[0-9]{10}$/',
            'address_1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => 'required',
            'email' => 'required|email|unique:users,email,'.$this->route('technician'),
            'password' => 'confirmed',
            'status' => 'required',
        ];
    }
}
