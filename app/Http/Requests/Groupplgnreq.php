<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Groupplgnreq extends FormRequest
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
            'id_plgn_group' => 'bail|alpha_num|required|min:2|max:16',
            'nm_group' => 'bail|required|min:4|max:64',
            'is_umum' => 'bail|numeric|required|min:1|max:1',
        ];
    }
    
    public function messages()
    {   
        return [
            'required' => ':attribute Harus di isi',
            'min' => ':attribute Minimal :min karakter',
            'max' => ':attribute Minimal :max karakter',
            'numeric' => ':attribute harus valid number karakter',
        ];
    }
}
