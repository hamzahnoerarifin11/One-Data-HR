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
        $user = auth()->user();
        $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin','superadmin']);

        return [
            'nama_peserta' => 'required|string|max:150',
            'nik_karyawan' => 'required|string|max:50',
            'status_peserta' => 'required|in:0,1,2',
            'keterangan_pindah' => 'nullable|string',

            // KETUA TEMPA INPUT MANUAL
            'nama_kelompok' => $isKetuaTempa ? 'required|string|max:100' : 'nullable',
            'nama_mentor'   => $isKetuaTempa ? 'required|string|max:100' : 'nullable',

            // ADMIN PILIH KELOMPOK
            'kelompok_id' => !$isKetuaTempa ? 'required|exists:tempa_kelompok,id_kelompok' : 'nullable',
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
            'keterangan_pindah' => 'Keterangan Pindah',
            'unit' => 'Unit',
            'shift' => 'Shift',
        ];
    }
}
