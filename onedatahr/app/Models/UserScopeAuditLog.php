<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserScopeAuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'changed_by',
        'old_org_scope',
        'new_org_scope',
        'old_holding_id',
        'new_holding_id',
        'old_company_id',
        'new_company_id',
        'old_division_id',
        'new_division_id',
        'old_department_id',
        'new_department_id',
        'old_unit_id',
        'new_unit_id',
        'action',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function oldHolding()
    {
        return $this->belongsTo(Holding::class, 'old_holding_id');
    }

    public function newHolding()
    {
        return $this->belongsTo(Holding::class, 'new_holding_id');
    }

    public function oldCompany()
    {
        return $this->belongsTo(Company::class, 'old_company_id');
    }

    public function newCompany()
    {
        return $this->belongsTo(Company::class, 'new_company_id');
    }

    public function oldDivision()
    {
        return $this->belongsTo(Division::class, 'old_division_id');
    }

    public function newDivision()
    {
        return $this->belongsTo(Division::class, 'new_division_id');
    }
}
