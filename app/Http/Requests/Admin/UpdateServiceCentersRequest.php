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
            'location_latitude'=>'required',
            'location_longitude'=>'required',

            'commission' => 'max:2147483647|required|numeric',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => 'required|min:6|max:6',
            'supported_zipcode' => 'required',
            'status' => 'required',
        ];
    }
}
