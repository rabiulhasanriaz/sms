<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LoadCampaign24 extends Model
{
    protected $fillable = [
    	'user_id', 'operator_id', 'campaign_id', 'targeted_number', 'package_id', 'campaign_price', 'status'
    ];
    public function package_info()
    {
    	return $this->belongsTo("App\Model\LoadPackage", 'package_id', 'id');
    }
}
