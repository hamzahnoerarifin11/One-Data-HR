<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KpiAssessment;
use App\Models\Pekerjaan;
use App\Models\User;
use App\Models\KbiAssessment;

class Karyawan extends Model
{
    use HasFactory; // Trait dipasang di paling atas class

    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan'; // Sesuai database Anda
    public $incrementing = true;
    public $timestamps = false;

    // Gunakan guarded kosong agar semua kolom bisa diisi (lebih praktis)
    // protected $fillable = [ ... ]; // Ini bisa dihapus jika sudah pakai guarded
    protected $guarded = [];

    // =========================================================
    // PERBAIKAN UTAMA: RELASI KE PEKERJAAN (JABATAN)
    // =========================================================

    // Karena di tabel 'pekerjaan' ada kolom 'id_karyawan', 
    // kita pakai hasMany untuk history pekerjaan
    public function pekerjaan()
    {
        // hasOne(ModelTujuan, 'Foreign_Key_di_Tabel_Tujuan', 'Local_Key_di_Sini')
        return $this->hasMany(Pekerjaan::class, 'id_karyawan', 'id_karyawan')
            ->latest('id_pekerjaan'); // Opsional: Ambil yang paling baru diinput
    }

    // Ambil pekerjaan terkini/terbaru saja (single record)
    public function pekerjaanTerkini()
    {
        return $this->hasOne(Pekerjaan::class, 'id_karyawan', 'id_karyawan')
            ->latest('id_pekerjaan');
    }

    // =========================================================
    // RELASI LAINNYA
    // =========================================================

    public function pendidikan()
    {
        return $this->hasOne(Pendidikan::class, 'id_karyawan', 'id_karyawan');
    }

    public function kontrak()
    {
        return $this->hasOne(Kontrak::class, 'id_karyawan', 'id_karyawan');
    }

    public function keluarga()
    {
        return $this->hasOne(DataKeluarga::class, 'id_karyawan', 'id_karyawan');
    }

    public function bpjs()
    {
        return $this->hasOne(Bpjs::class, 'id_karyawan', 'id_karyawan');
    }

    public function perusahaan()
    {
        return $this->hasOne(Perusahaan::class, 'id_karyawan', 'id_karyawan');
    }

    public function status()
    {
        return $this->hasOne(StatusKaryawan::class, 'id_karyawan', 'id_karyawan');
    }

    public function kpiAssessment()
    {
        return $this->hasOne(KpiAssessment::class, 'karyawan_id', 'id_karyawan');
    }
    /**
     * Relasi ke KBI (Key Behavior Indicator / Perilaku)  
     * Satu karyawan punya banyak history penilaian perilaku
     */
    public function kbiAssessment()
    {
        return $this->hasMany(KbiAssessment::class, 'karyawan_id', 'id_karyawan');
    }

    // Struktur Organisasi (Atasan & Bawahan)
    public function atasan()
    {
        return $this->belongsTo(Karyawan::class, 'atasan_id', 'id_karyawan');
    }

    public function bawahan()
    {
        return $this->hasMany(Karyawan::class, 'atasan_id', 'id_karyawan');
    }

    // Relasi ke User Login (Opsional, jika ada kolom user_id)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    // Konversi kolom created_at dan updated_at ke format DateTime
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
