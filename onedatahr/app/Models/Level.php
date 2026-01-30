<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Level extends Model
{
    use HasFactory;

    protected $table = 'levels';

    protected $fillable = [
        'name',
        'level_order',
    ];

    /**
     * Relasi: 1 Level memiliki banyak Pekerjaan
     */
    public function pekerjaan()
    {
        return $this->hasMany(Pekerjaan::class, 'level_id');
    }

    /**
     * Scope helper untuk ambil level berdasarkan urutan (optional tapi berguna)
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('level_order');
    }
}
