<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsersRequest extends FormRequest
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
            // 'phone' => 'required|numeric|regex:/^[0-9]{10}$/',
            'phone' => 'required|min:11|max:11',
            'email' => 'required|email|unique:users,email,"",id,role_id,'.config('constants.ADMIN_ROLE_ID').',deleted_at,NULL',
            'password' => 'required|confirmed',
            'status' => 'required',
        ];
    }
}
