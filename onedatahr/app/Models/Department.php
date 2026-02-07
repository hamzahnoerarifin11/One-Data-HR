<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'holding_id', 'based_on', 'parent_id', 'division_id', 'name'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function holding()
    {
        return $this->belongsTo(\App\Models\Holding::class);
    }

    public function parent()
    {
        return $this->belongsTo(\App\Models\Department::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(\App\Models\Department::class, 'parent_id');
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
