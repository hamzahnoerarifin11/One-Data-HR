<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pendidikan extends Model
{
    protected $table = 'pendidikan';
    protected $primaryKey = 'id_pendidikan';
    public $timestamps = false;
    protected $fillable = ['id_karyawan','Pendidikan_Terakhir','Nama_Lengkap_Tempat_Pendidikan_Terakhir','Jurusan'];
    protected $touches = ['karyawan'];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
