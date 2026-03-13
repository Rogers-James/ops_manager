<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class NotificationLog extends Model
{
    protected $fillable = ['company_id','user_id','channel','subject','body','status','meta'];

    protected $table = 'notification_logs';
    protected $casts = ['meta'=>'array'];
    public function company() { return $this->belongsTo(Company::class); }
    public function user() { return $this->belongsTo(User::class); }

}
