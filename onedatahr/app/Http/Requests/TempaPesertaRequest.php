<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TempaPesertaRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Authorization akan dicek di Controller dengan Policy
    }

    public function rules()
    {
        $user = Auth::user();
        if ($user instanceof User) {
            $user->loadMissing('roles');
            $isKetuaTempa = $user->hasRole('ketua_tempa') && !$user->hasRole(['admin','superadmin']);
        } else {
            $isKetuaTempa = false;
        }

        $rules = [
            'nama_peserta' => 'required|string|max:150',
            'nik_karyawan' => 'required|string|max:50',
            'status_peserta' => 'required|in:0,1,2',
            'keterangan_pindah' => 'nullable|string',
            'kelompok_id' => 'required|exists:tempa_kelompok,id_kelompok',
        ];

        if ($isKetuaTempa) {
            // Pastikan kelompok milik ketua tempa
            $rules['kelompok_id'] .= '|exists:tempa_kelompok,id_kelompok,ketua_tempa_id,' . $user->id;
        }

        return $rules;
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
