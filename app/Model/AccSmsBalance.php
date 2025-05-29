<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class AccSmsBalance extends Model
{
    //
    protected $fillable = [
        'asb_paid_by',
        'asb_pay_to',
        'asb_pay_ref',
        'asb_credit',
        'asb_debit',
        'asb_submit_time',
        'asb_target_time',
        'asb_pay_mode',
        'asb_payment_status',
        'asb_deal_type',
        'credit_return_type',
    ];

    protected $dates = ['asb_submit_time', 'asb_target_time'];

    public static function getSmsCampaignIdDetails($ref_id)
    {
        return SmsCampaignId::where('sci_campaign_id', $ref_id)->first();
    }

    public static function getLoadCampaignIdDetails($ref_id)
    {
        return LoadCampaignId::where('campaign_id', $ref_id)->first();
    }

    public function smsCampaignId()
    {
        return $this->belongsTo(SmsCampaignId::class, 'asb_pay_ref', 'sci_campaign_id');
    }

    public function loadCampaignId()
    {
        return $this->belongsTo(LoadCampaignId::class, 'asb_pay_ref', 'campaign_id');
    }
}
