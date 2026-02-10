<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'holding_id', 'based_on', 'parent_id', 'division_id', 'department_id', 'name'];

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
        return $this->belongsTo(\App\Models\Unit::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(\App\Models\Unit::class, 'parent_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }
}
