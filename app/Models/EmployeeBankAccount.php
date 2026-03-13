<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class EmployeeBankAccount extends Model
{
    protected $fillable = ['company_id','employee_id','bank_name','account_title','account_number','iban'];

    public function company() { return $this->belongsTo(Company::class); }
    public function employee() { return $this->belongsTo(Employee::class); }

}
