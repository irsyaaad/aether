<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupVendorRequest extends FormRequest
{
    
    public function authorize()
    {
        return true;
    }

    public function rules()
    {   
        if ($this->getMethod() == 'POST') {

            return [
                'id_grup_ven' => 'required|min:1|max:5|unique:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.m_vendor_grup,id_grup_ven',
                'nm_grup_ven' => 'required|min:4|max:32|unique:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.m_vendor_grup,nm_grup_ven',
                'is_aktif'  => 'bail|nullable|numeric|max:1',
            ];

        }else{
            
            return [
                'id_grup_ven' => 'required|min:1|max:4',
                'nm_grup_ven' => 'required|min:4|max:32',
                'is_aktif'  => 'bail|nullable|numeric|max:1',
            ];

        }
    }
}
