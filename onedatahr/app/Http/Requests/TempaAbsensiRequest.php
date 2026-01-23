<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TempaAbsensiRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->role === 'ketua_tempa';
    }

    public function rules()
    {
        return [
            'peserta_id' => 'required|exists:tempa_peserta,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'pertemuan_ke' => 'required|integer|min:1|max:5',
            'status_hadir' => 'required|in:0,1',
            'bukti_foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $exists = \App\Models\TempaAbsensi::where('peserta_id', $this->peserta_id)
                ->where('bulan', $this->bulan)
                ->where('tahun', $this->tahun)
                ->where('pertemuan_ke', $this->pertemuan_ke)
                ->exists();

            if ($exists) {
                $validator->errors()->add('pertemuan_ke', 'Absensi untuk peserta ini sudah ada pada bulan dan pertemuan tersebut.');
            }
        });
    }
}
