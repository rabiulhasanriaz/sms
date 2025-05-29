<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SmsCampaignId extends Model
{
    //
    protected $fillable = [
        'user_id',
        'sender_id',
        'sci_campaign_id',
        'sci_campaign_title',
        'sci_total_submitted',
        'sci_total_cost',
        'sci_campaign_type',
        'sci_deal_type',
        'sci_sms_type',
        'sci_dynamic_type',
        'sci_sender_operator',
        'sci_targeted_time',
        'sci_campaign_status',
        'sci_browser',
        'sci_mac_address',
        'sci_ip_address',
        'sci_from_api',

    ];

    protected $dates = ['sci_targeted_time'];

    public function pendingSmsData()
    {
        return $this->hasMany(SmsCamPending::class, 'campaign_id', 'id');
    }

    public function apiDetails()
    {
        return $this->belongsTo(SmsCampaign::class, 'campaign_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->belongsTo(SenderIdRegister::class, 'sender_id', 'id');
    }
}
