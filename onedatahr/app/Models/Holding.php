<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holding extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function company()
    {
        return $this->hasMany(Company::class);
    }

    public function divisions()
    {
        return $this->hasMany(Division::class)->where('based_on', 'holding');
    }

    public function departments()
    {
        return $this->hasMany(Department::class)->where('based_on', 'holding');
    }

    public function units()
    {
        return $this->hasMany(Unit::class)->where('based_on', 'holding');
    }
}
