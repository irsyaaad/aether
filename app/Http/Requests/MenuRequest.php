<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
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
            'nm_menu' => 'bail|required|min:3|max:40',
            'icon' => 'bail|required|min:6|max:40',
            'id_module' => 'bail|numeric|required',
            'parent' => 'bail|numeric|required',
            'tampil' => 'bail|numeric|max:1',
            'route' => 'bail|max:40',
            'controller' => 'bail|max:40',
        ];
    }

    public function messages()
    {   
        return [
            'alpha' => ':attribute Harus karakter huruf',
            'min' => ':attribute Minimal 3 karakter',
            'max' => ':attribute Maksimal 20 karakter',
            'numeric' => ':attribute Hanya boleh di isi angka',
            'required' => ':attribute harus di isi'
        ];
    }
}
