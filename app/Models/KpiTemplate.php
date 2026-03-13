<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class KpiTemplate extends Model
{
    protected $fillable = ['company_id','name','items'];

    protected $table = 'kpi_templates';
    protected $casts = ['items'=>'array'];
    public function company() { return $this->belongsTo(Company::class); }

}
