<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompaniesRequest extends FormRequest
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
            'name' => 'required|unique:companies,name,'.$this->route('company'),
            'installation_charge' => 'required|numeric',
            'address_1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => 'required|min:6|max:6',
            'status' => 'required',
        ];
    }
}
