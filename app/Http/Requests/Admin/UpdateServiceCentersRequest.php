<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceCentersRequest extends FormRequest
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
            
            'name' => 'required',
            'address_1' => 'required',
            'location_address'=>'required|unique:service_centers,location_address,'.$this->route('service_center'),
            'location_latitude'=>'required|unique:service_centers,location_latitude,'.$this->route('service_center'),
            'location_longitude'=>'required|unique:service_centers,location_longitude,'.$this->route('service_center'),

            'commission' => 'max:2147483647|required|numeric',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => 'required',
            'status' => 'required',
        ];
    }
}
