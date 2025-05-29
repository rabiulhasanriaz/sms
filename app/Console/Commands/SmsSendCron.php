<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\SmsDesktopPending;
use App\Model\SmsDesktop24h;
use Carbon\Carbon;

class SmsSendCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smsSend:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $getNonMaskingSmsCampaigns = SmsDesktopPending::
                                        where('sdp_target_time','<=', Carbon::now())
                                        ->whereIn('sdp_campaign_type', ['2','1'])
                                        ->where('sdp_campaign_status', 1)
                                        ->groupBy('sdp_message')
                                        ->take(10)
                                        ->orderBy('id', 'desc')
                                        ->get();

            // dd($getNonMaskingSmsCampaigns);
            if (count($getNonMaskingSmsCampaigns) > 0) {
                $smsLoop = 1;
                foreach ($getNonMaskingSmsCampaigns as $nonMaskingSmsCampaign) {

                    $limitSms = 1000;
                    $sms = array();
                    $transferredSmsId = array();
                    $getSms50OfSameCampaignIds = SmsDesktopPending::where([
                        'campaign_id' => $nonMaskingSmsCampaign->campaign_id,
                        'sdp_campaign_status' => 1,
                        'sdp_message' => $nonMaskingSmsCampaign->sdp_message])
                        ->take($limitSms)
                        ->get();
                    // dd($getSms50OfSameCampaignIds);
                    $numbers = array();
                    foreach ($getSms50OfSameCampaignIds as $sms50Details) {
                        // dd($sms50Details);
                        $numbers[] = $sms50Details->sdp_cell_no;
                        
                        $transferredSmsId[] = $sms50Details->id;
                    
                    // dd($transferredSmsId);
                    }
                    // dd($numbers);
                    $countTSms = 0;
                    $userName = $nonMaskingSmsCampaign->api_user_name->routeDetail->user_name;
                // dd($userName);
                
                    $password = $nonMaskingSmsCampaign->api_user_name->routeDetail->password;

                
                    
                   $xml_response = \SmsHelper::send_desktop_sms($userName,$password,$numbers,$nonMaskingSmsCampaign->sdp_message);
                //   $xmlResponseArray = $xml_response->array; 
                   
                //   for ($i=0; $i < count($xmlResponseArray); $i++) { 
                //       $value[] = $xmlResponseArray[$i][1];
                //   }
                        
                        if ($xml_response->status == '-1') {
                            // $retText = "Something was missing";
                            \Log::info('Something was missing');
                        } elseif ($xml_response->status == '-4') {
                            // $retText = "Something Went Wrong to call robi non-masking api";
                            // $retText = "content empty";
                            \Log::info('content empty');
                        }elseif ($xml_response == 'blast') {
                            // $retText = "something went wrong to call dynamic api";
                            \Log::info('something went wrong to call dynamic api');
                        } else {
                            
                            $blDataForInsert = array();
                            $xmlResponseArray = array();
                            $xmlResponseArray[] = $xml_response->array;
                            foreach($xmlResponseArray as $key => $array){
                                foreach($array as $key1 => $value){
                                    $checkedSms = SmsDesktopPending::where('id', $transferredSmsId[$countTSms])->first();
                                // dd($checkedSms);
                                    $blDataForInsert[] = array(
                                        'user_id' => $checkedSms->user_id,
                                        // 'sender_id' => $checkedSms->sender_id,
                                        'campaign_id' => $checkedSms->campaign_id,
                                        'sdt_cell_no' => $checkedSms->sdp_cell_no,
                                        'sdt_message' => $checkedSms->sdp_message,
                                        'sdt_customer_message' => $checkedSms->sdp_customer_message,
                                        'sdt_sms_cost' => $checkedSms->sdp_sms_cost,
                                        'operator_id' => $checkedSms->operator_id,
                                        'sdt_campaign_type' => $checkedSms->sdp_campaign_type,
                                        'sdt_deal_type' => $checkedSms->sdp_deal_type,
                                        'sdt_sms_type' => $checkedSms->sdp_sms_type,
                                        'sdt_sms_id' => $array[$key1][1],
                                        'sdt_sms_text_type' => $checkedSms->sdp_sms_text_type,
                                        'sdt_target_time' => $checkedSms->sdp_target_time,
                                        'created_at' => $checkedSms->created_at,
                                        'updated_at' => $checkedSms->updated_at,
                                        'sdt_delivery_report' => 'PENDING',
                                        'sdt_status' => $checkedSms->sdp_status,
                                    );
                                    $countTSms++;
                                }
                            }
                            
                            try {
                                SmsDesktop24h::insert($blDataForInsert);
                                $blDataForInsert = array();

                                SmsDesktopPending::whereIn('id', $transferredSmsId)->delete();

                                // $retText = "Working...". $smsLoop++;
                                \Log::info('Working...' . $smsLoop++);
                            } catch (\Exception $e) {
                                // $retText = "something went wrong" . $e->getMessage();
                                 \Log::info('something went wrong' . $e->getMessage());
                                // return view('cron.sms-desktop', compact('retText'));
                            }

                        }
                     


                }
                // return view('cron.sms-desktop', compact('retText'));
                // \Log::info($retText);

            } else {
                // $retText = "no sms found";
                // \Log::info('no sms found');
                // return view('cron.sms-desktop', compact('retText'));
            }

    }
}
