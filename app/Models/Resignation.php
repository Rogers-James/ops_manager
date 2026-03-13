<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resignation extends Model
{
    protected $fillable = ['company_id', 'employee_id', 'resignation_date', 'last_working_day', 'reason', 'status'];

    protected $table = 'resignations';
    protected $casts = ['resignation_date' => 'date', 'last_working_day' => 'date'];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function finalSettlement()
    {
        return $this->hasOne(FinalSettlement::class);
    }
}
