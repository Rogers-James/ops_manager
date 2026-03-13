<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollRunItem extends Model
{
    protected $fillable = ['company_id', 'payroll_run_id', 'employee_id', 'gross', 'deductions', 'net', 'status'];

    protected $table = 'payroll_run_items';
    protected $casts = ['gross' => 'decimal:2', 'deductions' => 'decimal:2', 'net' => 'decimal:2'];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function payrollRun()
    {
        return $this->belongsTo(PayrollRun::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function payslip()
    {
        return $this->hasOne(Payslip::class);
    }
}
