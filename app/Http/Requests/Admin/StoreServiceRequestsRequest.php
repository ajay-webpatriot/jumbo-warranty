<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequestsRequest extends FormRequest
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
            'company_id' => 'required',
            'service_type' => 'required',
            'call_type' => 'required',
            'call_location' => 'required',
            'priority' => 'required',
            'product_id' => 'required',
            // 'is_item_in_warrenty' => 'required',
            'completion_date' => 'required|date_format:'.config('app.date_format'),
            'parts.*' => 'exists:product_parts,id',
            'status' => 'required',
            'additional_charges_title' => 'required_with:additional_charges',
            'additional_charges' => 'required_with:additional_charges_title|min:0|not_in:0'
        ];
    }
}
