<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorRequest extends FormRequest
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
            'id_grup_ven' => 'bail|required|exists:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.m_vendor_grup,id_grup_ven',
            'id_wil'      => 'bail|required|min:1|max:11|exists:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.m_wilayah,id_wil',
            'nm_ven'      => 'bail|required|min:4|max:64|',
            'alm_ven'     => 'bail|required|min:6|max:128',
            'telp_ven'    => 'bail|required|digits_between:1,32',
            'npwp'        => 'bail|nullable|digits_between:1,16',
            'nm_pemilik'  => 'bail|nullable|min:4|max:32',
            'email_ven'   => 'bail|nullable|min:10|max:64',
            'kontak_ven'  => 'bail|nullable',
            'kontak_hp'   => 'bail|nullable|digits_between:1,32',
            'hari_inv'    => 'bail|nullable|digits_between:1,32',
            'is_aktif'    => 'bail|nullable|digits:1',
        ];
    }
}
