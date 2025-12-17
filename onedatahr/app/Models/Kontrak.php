<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Kontrak extends Model
{
    protected $table = 'kontrak';
    protected $primaryKey = 'id_kontrak';
    public $timestamps = false;
    protected $fillable = [
        'id_karyawan','Tanggal_Mulai_Tugas','PKWT_Berakhir','Tanggal_Diangkat_Menjadi_Karyawan_Tetap',
        'Riwayat_Penempatan','Tanggal_Riwayat_Penempatan','Mutasi_Promosi_Demosi','Tanggal_Mutasi_Promosi_Demosi',
        'Masa_Kerja','NO_PKWT_PERTAMA','NO_SK_PERTAMA'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}
