<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestRoleUser extends FormRequest
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
            'id_role' => 'bail|required|exists:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.role,id_role',
            'id_user'  => 'bail|required|exists:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.users,id_user',
            'id_perush'  => 'bail|required|exists:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.s_perusahaan,id_perush',
        ];
    }

    public function messages()
    {   
        return [
            'required' => ':attribute harus di isi',
            'min' => ':attribute minimal 4 karakter',
            'max' => ':attribute max 50 karakter',
            'alpha_num' => ':attribute Harus karakter valid',
        ];
    }
}
