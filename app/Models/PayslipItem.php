<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class PayslipItem extends Model
{
    protected $fillable = ['company_id','payslip_id','salary_component_id','amount'];

    protected $table = 'payslip_items';
    protected $casts = ['amount'=>'decimal:2'];
    public function company() { return $this->belongsTo(Company::class); }
    public function payslip() { return $this->belongsTo(Payslip::class); }
    public function component() { return $this->belongsTo(SalaryComponent::class,'salary_component_id'); }

}
