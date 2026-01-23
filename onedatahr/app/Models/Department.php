<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'division_id', 'name'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }
}
