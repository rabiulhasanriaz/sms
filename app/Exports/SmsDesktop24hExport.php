<?php

namespace App\Exports;

use App\Model\SmsDesktop24h;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Auth;

class SmsDesktop24hExport implements FromCollection, WithHeadings
{
    protected $canpaign_id;
    function __construct($campaign_id){
        $this->campaign_id = $campaign_id;
    }
    public function collection()
    {
        return SmsDesktop24h::select('sdt_cell_no', 'sdt_message' ,'sdt_message' , 'sdt_sms_cost', 'created_at', 'sdt_delivery_report')
            ->where(['user_id' => Auth::id(), 'campaign_id' => $this->campaign_id])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Mobile',
            'Message',
            'Cost',
            'Created At',
            'Report',
        ];
    }
}
