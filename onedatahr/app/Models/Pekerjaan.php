<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pekerjaan extends Model
{
    protected $table = 'pekerjaan';
    protected $primaryKey = 'id_pekerjaan';
    public $timestamps = false;
    protected $fillable = [
        'id_karyawan','Jabatan','Bagian','Departement','Divisi','Unit','Jenis_Kontrak','Perjanjian','Lokasi_Kerja'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}
