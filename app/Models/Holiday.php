<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Holiday extends Model
{
    protected $fillable = ['company_id','holiday_calendar_id','date','name'];

    public function company() { return $this->belongsTo(Company::class); }
    public function calendar() { return $this->belongsTo(HolidayCalendar::class,'holiday_calendar_id'); }

}
