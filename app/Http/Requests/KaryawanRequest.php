<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KaryawanRequest extends FormRequest
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
                'no_hp' => 'bail|required|numeric|digits_between:8,16',
                'id_finger' => 'bail|nullable|numeric|',
                'npwp' => 'bail|nullable|numeric|digits_between:6,24',
                'nm_karyawan' => 'required|min:4|max:64|regex:/^[a-zA-Z\s]+$/',
                'id_jenis'  => 'bail|required|exists:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.m_jenis_karyawan,id_jenis',
                'id_perush'  => 'bail|nullable|exists:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.s_perusahaan,id_perush',
                'is_aktif'  => 'bail|nullable|numeric|max:1',
                'jenis_kelamin'  => 'bail|required|alpha|min:1|max:1',
                'no_ktp' => 'bail|nullable|numeric|digits_between:8,16',
                'no_rekening' => 'bail|nullable|numeric|digits_between:8,24',
                'tgl_masuk' => 'bail|nullable|date',
            ];
    }
}
