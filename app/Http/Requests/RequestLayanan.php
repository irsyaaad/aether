<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestLayanan extends FormRequest
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
        if ($this->getMethod() == 'POST') {
            return [
                'nm_layanan' => 'bail|required|min:3|max:40|unique:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.m_layanan,nm_layanan',
                'kode_layanan' => 'bail|required|max:2|unique:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.m_layanan,kode_layanan',
            ];
        }else{
            return [
                'nm_layanan' => 'bail|required|min:3|max:40',
                'kode_layanan' => 'bail|required|max:2',
            ];
        }
    }
}
