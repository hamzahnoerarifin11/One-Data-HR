<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class RekrutmenDaily extends Model
{
    use HasFactory;

    protected $table = 'rekrutmen_daily';
    protected $fillable = [
        'posisi_id', 'date', 'count', 'total_pelamar', 'lolos_cv', 'lolos_psikotes', 'lolos_kompetensi', 'lolos_hr', 'lolos_user', 'notes', 'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'count' => 'integer',
        'total_pelamar' => 'integer',
        'lolos_cv' => 'integer',
        'lolos_psikotes' => 'integer',
        'lolos_kompetensi' => 'integer',
        'lolos_hr' => 'integer',
        'lolos_user' => 'integer',
    ];


    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'posisi_id', 'id_posisi');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function getYieldAttribute()
    {
        if ($this->total_pelamar == 0) {
            return 0;
        }

        return round(($this->lolos_user / $this->total_pelamar) * 100, 2);
    }
}
