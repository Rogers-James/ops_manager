<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class PerformanceReview extends Model
{
    protected $fillable = ['company_id','employee_id','reviewer_employee_id','performance_cycle_id','rating','comments'];

    protected $table = 'performance_reviews';
    protected $casts = ['rating'=>'decimal:2'];
    public function company() { return $this->belongsTo(Company::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function reviewer() { return $this->belongsTo(Employee::class,'reviewer_employee_id'); }
    public function cycle() { return $this->belongsTo(PerformanceCycle::class,'performance_cycle_id'); }

}
