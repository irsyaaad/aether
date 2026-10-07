<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Route;

class PerusahaanRequest extends FormRequest
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
        $route = explode(".", Route::currentRouteName());
        $valid = [];
        if($route[1]=="store"){
            $valid = [
                    'alamat' => 'required|max:100',
                    'telp' => 'required|min:4|max:50',
                    'fax' => 'nullable|min:4|max:50',
                    'npwp' => 'nullable|min:4|max:50',
                    'email' => 'required|email:rfc,dns|min:10|max:100',
                    'nm_dir' => 'nullable|min:4|max:50',
                    'nm_keu' => 'nullable|min:4|max:50',
                    'nm_cs' => 'nullable|min:4|max:50',
                    'logo'  => 'bail|nullable|image|mimes:jpg,png,jpeg,svg,gif|max:2048',
                    'id_region'  => 'nullable|numeric|exists:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.m_wilayah,id_wil',
                    'nm_perush' => 'required|min:4|max:100|unique:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.s_perusahaan,nm_perush',
                    'cabang' =>  'bail|nullable|max:100|exists:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.s_perusahaan,id_perush',
                    'id_perush' => 'bail|nullable|min:1|max:5|unique:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.s_perusahaan,kode_perush'
                    ];
        }else{
            $valid = [
                    'alamat' => 'required|max:100',
                    'telp' => 'required|min:4|max:50',
                    'fax' => 'nullable|min:4|max:50',
                    'npwp' => 'nullable|min:4|max:50',
                    //'email' => 'nullable|email:rfc,dns|min:10|max:100',
                    'logo'  => 'bail|nullable|image|mimes:jpg,png,jpeg,svg,gif|max:2048',
                    'nm_dir' => 'bail|nullable|min:4|max:50',
                    'nm_keu' => 'bail|nullable|min:4|max:50',
                    'nm_cs' => 'bail|nullable|min:4|max:50',
                    'id_region'  => 'bail|nullable|numeric|exists:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.m_wilayah,id_wil',
                    'nm_perush' => 'bail|required|min:4|max:100',
                    'id_perush' => 'bail|required|min:1|max:5',
                    'cabang' =>  'bail|nullable|max:100|exists:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.s_perusahaan,id_perush',
            ];
        }
        //dd($valid);
        return $valid;
    }

    public function messages()
    {   
        return [
            'unique' => ':attribute sudah ada, ganti yang lain',
            'digits_between' => ':attribute antar :min - :max Karakter',
            'numeric' => ':attribute Harus bilangan',
            'alpha_dash' => ':attribute Harus Huruf',
            'max'     => ':attribute maksimal :max Karakter',
            'required' => ':attribute harus di isi',
            'min'  => ':attribute minimal :min Karakter',
            'email' => ':attribute harus valid'
        ];
    }
}
