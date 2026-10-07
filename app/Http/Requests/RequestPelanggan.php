<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestPelanggan extends FormRequest
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
        $id = app('request')->segment(2)?app('request')->segment(2):0;
        return [
            'nm_pelanggan' => 'bail|required',
            'alamat' => 'bail|required|max:250',
            'id_wil' => 'bail|nullable|numeric|exists:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.m_wilayah,id_wil',
            'telp'  => 'bail|required|digits_between:6,14',
            'email'  => 'bail|nullable|email:rfc,dns|max:60',
            'fax'  => 'bail|nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:7',
            'nm_kontak'  => 'bail|nullable|min:4|max:14',
            'no_kontak'  => 'bail|nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:7',
            'npwp'  => 'bail|nullable|numeric|digits_between:11,20',
            'isaktif'  => 'bail|nullable|numeric|max:1',
        ];
    }
    
    public function messages()
    {   
        return [
            'regex' => 'Nama Hanya Karakter Huruf dan Space',
            'email' => ':attribute Format Email Harus Benar',
            'max'     => ':attribute Terlalu Panjang',
            'min'  => ':attribute Terlalu Pendek',
            'required' => ':attribute harus di isi',
            'numeric' => ':attribute harus format angka benar'
        ];
    }
}
