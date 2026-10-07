<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PerushArmadaReq extends FormRequest
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
        $valid = [
            'nm_perush' => 'required|max:100|min:4|max:100',
            'id_wil'  => 'nullable|numeric|exists:'.env('DB_CONNECTION').'.'.env('DB_DATABASE').'.m_wilayah,id_wil',
            'nm_pemilik' => 'nullable|bail|min:4|max:100',
            'alamat' =>  'required|min:4|max:255',
            'telp' => 'nullable|min:4|max:16',
            'no_hp' => 'required|min:4|max:16',
            'foto'  => 'bail|nullable|image|mimes:jpg,png,jpeg,svg,gif|max:2048',
            ];

            return $valid;
    }
}
