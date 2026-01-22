<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function divisions()
    {
        return $this->hasMany(Division::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
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
