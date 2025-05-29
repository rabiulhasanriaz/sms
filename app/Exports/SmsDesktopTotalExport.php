<?php

namespace App\Exports;

use App\Model\SmsDesktop;
use App\Model\SmsDesktopCampaignId;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Auth;

class SmsDesktopTotalExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $start_date;
    protected $end_date;
    
    function __construct($start_date,$end_date){
        // $this->q_start_date = $q_start_date;
        // $this->q_end_date = $q_end_date;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }
    public function collection()
    {
        // dd($this->q_end_date);
        // return SmsDesktop::select('sd_cell_no','sd_message','sd_sms_cost','created_at','sd_delivery_report')
        //                   ->where('user_id',Auth::id())
        //                   ->where('created_at','>=',$this->q_start_date)
        //                   ->where('created_at','<=',$this->q_end_date)
        //                   ->get();
        // $archived_campaign = SmsDesktopCampaignId::where('user_id', Auth::id())
        //     ->where('sdci_deal_type', '1')
        //     ->where('sdci_targeted_time', '>=', $this->start_date)
        //     ->where('sdci_targeted_time', '<=', $this->end_date)
        //     ->where('sdci_from_api',4)
        //     ->orderBy('id', 'desc')
        //     ->pluck('id')->toArray();
        // dd($archived_campaign);
        
        
        $ab =  SmsDesktop::select('sd_cell_no','sd_message','sd_sms_cost','created_at','sd_delivery_report')
                            
                            
                            ->where('sd_targeted_time','>=', $this->start_date)
                            ->where('sd_targeted_time', '<=', $this->end_date)
                            
                            ->get();
        // dd($ab);   
        return $ab;     
        // dd($ab);                  
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
