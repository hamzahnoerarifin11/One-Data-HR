<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Bpjs extends Model
{
    protected $table = 'bpjs';
    protected $primaryKey = 'id_bpjs';
    public $timestamps = false;
    protected $fillable = ['id_karyawan','Status_BPJS_KT','Status_BPJS_KS'];
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
