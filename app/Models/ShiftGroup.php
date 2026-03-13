<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftGroup extends Model
{
    protected $fillable = ['company_id', 'name'];

    protected $table = 'shift_groups';
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function assignments()
    {
        return $this->hasMany(ShiftGroupAssignment::class, 'shift_group_id');
    }

    public function defaultShiftType()
    {
        return $this->belongsTo(ShiftType::class, 'default_shift_type_id');
    }
}
