<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalSettlement extends Model
{
    protected $fillable = ['company_id', 'employee_id', 'resignation_id', 'payroll_run_id', 'amount', 'status'];

    protected $table = 'final_settlements';
    protected $casts = ['amount' => 'decimal:2'];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function resignation()
    {
        return $this->belongsTo(Resignation::class);
    }
    public function payrollRun()
    {
        return $this->belongsTo(PayrollRun::class);
    }
}
