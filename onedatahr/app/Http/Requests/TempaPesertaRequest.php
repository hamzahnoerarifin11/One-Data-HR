<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TempaPesertaRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Authorization akan dicek di Controller dengan Policy
    }

    public function rules()
    {
        return [
            'id_tempa' => 'required|exists:tempa,id_tempa',
            'id_kelompok' => 'required|exists:tempa_kelompok,id_kelompok',
            'status_peserta' => 'required|integer|in:0,1,2',
            'nama_peserta' => 'required|string|max:255',
            'nik_karyawan' => 'nullable|string|max:50|unique:tempa_peserta,nik_karyawan' . ($this->route('peserta') ? ',' . $this->route('peserta') . ',id_peserta' : ''),
            'mentor_id' => 'required|exists:users,id',
            'unit' => 'nullable|string|max:100',
            'shift' => 'nullable|integer|min:1|max:3',
        ];
    }

    public function attributes()
    {
        return [
            'id_tempa' => 'TEMPA',
            'id_kelompok' => 'Kelompok',
            'status_peserta' => 'Status Peserta',
            'nama_peserta' => 'Nama Peserta',
            'nik_karyawan' => 'NIK Karyawan',
            'mentor_id' => 'Mentor',
            'unit' => 'Unit',
            'shift' => 'Shift',
        ];
    }
}
