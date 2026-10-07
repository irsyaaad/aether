<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TarifRequest extends FormRequest
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
            'id_tujuan' => 'bail|digits_between:1,100|required|exists:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.m_wilayah,id_wil',
            'id_asal' => 'bail|digits_between:1,100|required|exists:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.m_wilayah,id_wil',
            'id_layanan' => 'bail|digits_between:1,100|required|exists:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.m_layanan,id_layanan',
            'hrg_vol' => 'bail|required|numeric',
            'hrg_brt' => 'bail|required|numeric',
            'min_vol' => 'bail|required|numeric',
            'min_brt' => 'bail|required|numeric',
            'hrg_beli_kilo' => 'bail|nullable|numeric',
            'hrg_beli_vol' => 'bail|nullable|numeric',
            'hrg_beli_borongan' => 'bail|nullable|numeric',
            'estimasi' => 'bail|nullable|numeric',
            'info' => 'bail|max:100',
            'id_pelanggan' => 'bail|nullable|digits_between:1,100|exists:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.m_plgn,id_pelanggan',
            'is_aktif' => 'bail|nullable|digits:1|numeric',
            'is_standart' => 'bail|digits:1|nullable|numeric',
        ];
    }
    
    public function messages()
    {   
        return [
            'max'     => ':attribute Terlalu Panjang',
            'min'  => ':attribute Terlalu Pendek',
            'required' => ':attribute harus di isi',
            'digits_between' => ':attribute antara 1 dan 100 karakter',
            'digits' => ':attribute harus valid 1 karakter',
            'numeric' => ':attribute harus valid number',
        ];
    }
}
