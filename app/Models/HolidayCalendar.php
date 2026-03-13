<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class HolidayCalendar extends Model
{
    protected $fillable = ['company_id','name'];

    protected $table = 'holiday_calendars';
    public function company() { return $this->belongsTo(Company::class); }
    public function holidays() { return $this->hasMany(Holiday::class,'holiday_calendar_id'); }

}
