<?php

namespace App\Http\Requests;
use Request;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $id = Request::segment(2) != null ? Request::segment(2) : null;
        if ($this->getMethod() == 'POST') {
            return [
                'full_name' => 'bail|required|string|max:100',
                'email' => 'required|email|max:150|unique:users,email',
                'password' => 'required|string|min:6',
                'jenis_kelamin' => 'required|in:L,P',
                'tgl_lahir' => 'required|date|before:today',
                'captcha' => 'required|captcha',
            ];
        } else {
            return [
                'full_name' => 'bail|required',
                'email' => 'bail|required|unique:users,email,' . $id . ',id_user',
                // 'password' => [
                //     'required',
                //     'string',
                //     'min:12',
                //     'regex:/[A-Z]/', // Harus ada huruf besar
                //     'regex:/[a-z]/', // Harus ada huruf kecil
                //     'regex:/[0-9]/', // Harus ada angka
                //     'regex:/[!@#$%^&*()_+\-]/', // Harus ada karakter spesial
                // ],
            ];
        }
    }

    public function messages()
    {
        return [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain atau login.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid.',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tgl_lahir.date' => 'Format tanggal lahir tidak valid.',
            'tgl_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'captcha.required' => 'Kode keamanan captcha wajib diisi.',
            'captcha.captcha' => 'Kode captcha tidak cocok atau sudah kadaluarsa. Silakan refresh dan coba lagi.',
        ];
    }
}
