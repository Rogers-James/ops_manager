<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class WorkWeekProfile extends Model
{
    protected $fillable = ['company_id','name','weekend_days'];

    protected $table = 'work_week_profiles';
    protected $casts = ['weekend_days'=>'array'];
    public function company() { return $this->belongsTo(Company::class); }

}
