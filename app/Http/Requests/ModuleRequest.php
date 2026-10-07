<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuleRequest extends FormRequest
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
            'nm_module'   => 'required|min:4|max:40',
            'icon' => 'required|min:4|max:40',
            'color' => 'required|min:4|max:40',
        ];
    }

    public function messages()
    {   
        return [
            'required' => ':attribute harus di isi',
            'min' => ':attribute minimal 4 karakter',
            'max' => ':attribute max 40 karakter',
        ];
    }
}
