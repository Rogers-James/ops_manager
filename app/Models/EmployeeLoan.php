<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class EmployeeLoan extends Model
{
    protected $fillable = ['company_id','employee_id','amount','start_date','installment_amount','status'];

    protected $table = 'employee_loans';
    protected $casts = ['amount'=>'decimal:2','installment_amount'=>'decimal:2','start_date'=>'date'];
    public function company() { return $this->belongsTo(Company::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function repayments() { return $this->hasMany(LoanRepayment::class); }

}
