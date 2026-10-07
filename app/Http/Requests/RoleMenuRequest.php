<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleMenuRequest extends FormRequest
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
            'id_role' => 'bail|required|numeric',
            'id_menu'  => 'bail|required|numeric',
            'id_module'  => 'bail|required|numeric',
            'c_read' => 'bail|numeric|max:1',
            'c_insert' => 'bail|numeric|max:1',
            'c_update' => 'bail|numeric|max:1',
            'c_delete' => 'bail|numeric|max:1',
            'c_other' => 'bail|numeric|max:1',
        ];
    }
    
    public function messages()
    {  
        return[
            'required' => ':attribute Harus di isi',
            'numeric' => ':attribute Harus angka',
            'max' => ':attribute Harus valid',
        ];
    }
}
