<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekrutmenDaily extends Model
{
    use HasFactory;

    protected $table = 'rekrutmen_daily';
    protected $fillable = [
        'posisi_id', 'date', 'count', 'notes', 'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'count' => 'integer',
    ];

    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'posisi_id', 'id_posisi');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
