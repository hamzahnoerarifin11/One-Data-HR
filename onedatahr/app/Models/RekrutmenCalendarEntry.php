<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekrutmenCalendarEntry extends Model
{
    protected $table = 'rekrutmen_calendar_entries';
    protected $fillable = ['posisi_id','kandidat_id','candidate_name','date','created_by'];

    public function kandidat()
    {
        return $this->belongsTo(Kandidat::class, 'kandidat_id');
    }

    public function posisi()
    {
        return $this->belongsTo(Posisi::class, 'posisi_id');
    }
}
