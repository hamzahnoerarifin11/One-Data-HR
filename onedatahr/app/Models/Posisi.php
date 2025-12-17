<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posisi extends Model
{
    use HasFactory;

    protected $table = 'posisi';
    protected $primaryKey = 'id_posisi';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nama_posisi',
    ];

    public function kandidat()
    {
        return $this->hasMany(Kandidat::class, 'posisi_id', 'id_posisi');
    }
}
