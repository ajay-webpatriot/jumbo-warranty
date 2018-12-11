<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompaniesRequest extends FormRequest
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
            'installation_charge' => 'required',
            'address_1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => 'required',
            'location' => 'required',
            'status' => 'required',
        ];
    }
}
