<?php

namespace App\Exports;

use App\Model\SmsDesktop;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Auth;

class SmsDesktopExport implements FromCollection, WithHeadings
{
    protected $campaign_id;
    function __construct($campaign_id){
      $this->campaign_id = $campaign_id;
    }
    public function collection()
    {
      return SmsDesktop::select('sd_cell_no', 'sd_message' ,'sd_sms_cost', 'created_at', 'sd_delivery_report')
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
