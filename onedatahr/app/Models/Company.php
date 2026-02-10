<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Holding;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'holding_id', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(Company::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Company::class, 'parent_id');
    }

    public function divisions()
    {
        return $this->hasMany(Division::class);
    }

    public function holding()
    {
        return $this->belongsTo(Holding::class);
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
