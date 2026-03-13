<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class AssetAssignment extends Model
{
    protected $fillable = ['company_id','asset_id','employee_id','assigned_at','returned_at','notes'];

    protected $table = 'asset_assignments';
    protected $casts = ['assigned_at'=>'datetime','returned_at'=>'datetime'];
    public function company() { return $this->belongsTo(Company::class); }
    public function asset() { return $this->belongsTo(Asset::class); }
    public function employee() { return $this->belongsTo(Employee::class); }

}
