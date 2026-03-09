<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TempaKelompokRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Authorization akan dicek di Controller dengan Policy
    }

    public function rules()
    {
        return [
            'nama_kelompok' => 'required|string|max:100',
            'nama_mentor' => 'required|string|max:100',
            'tempat' => 'required|in:pusat,cabang',
            'keterangan_cabang' => 'nullable|string|max:255',
        ];
    }

    public function attributes()
    {
        return [
            'nama_kelompok' => 'Nama Kelompok',
            'nama_mentor' => 'Nama Mentor',
            'tempat' => 'Tempat',
            'keterangan_cabang' => 'Keterangan Cabang',
        ];
    }
}
