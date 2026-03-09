<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KbiItem extends Model
{
    use HasFactory;

    protected $table = 'kbi_items';
    protected $primaryKey = 'id_kbi_item';

    protected $fillable = [
        'kategori',
        'perilaku',
    ];

    // Relasi: Satu butir soal bisa memiliki banyak nilai (di berbagai sesi penilaian)
    public function scores()
    {
        return $this->hasMany(KbiScore::class, 'kbi_item_id', 'id_kbi_item');
    }
}