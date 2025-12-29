<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StatusKaryawan extends Model
{
    protected $table = 'status_karyawan';
    protected $primaryKey = 'id_status';
    public $timestamps = false;
    protected $fillable = ['id_karyawan','Tanggal_Non_Aktif','Alasan_Non_Aktif','Ijazah_Dikembalikan','Bulan'];
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
