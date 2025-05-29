<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Model\SmsDesktopPending;
use App\Model\SmsDesktop24h;
use App\Model\SmsDesktop;
use Carbon\Carbon;
use Response;
use DB;


class SmsDesktopController extends Controller
{
    


    

    

    public function smsDesktopSms()
    {
        
         //for ($loopNo1 = 1; $loopNo1 <= 10; $loopNo1++) {

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
                            $retText = "Something was missing";
                        } elseif ($xml_response->status == '-4') {
                            // $retText = "Something Went Wrong to call robi non-masking api";
                            $retText = "content empty";
                        }elseif ($xml_response == 'blast') {
                            $retText = "something went wrong to call dynamic api";
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
                                        'sdt_status' => '0',
                                    );
                                    $countTSms++;
                                }
                            }
                            
                            try {
                                SmsDesktop24h::insert($blDataForInsert);
                                $blDataForInsert = array();

                                SmsDesktopPending::whereIn('id', $transferredSmsId)->delete();

                                $retText = "Working...". $smsLoop++;
                            } catch (\Exception $e) {
                                $retText = "something went wrong" . $e->getMessage();
                                return view('cron.sms-desktop', compact('retText'));
                            }

                        }
                     


                }
                return view('cron.sms-desktop', compact('retText'));

            } else {
                $retText = "no sms found";
                return view('cron.sms-desktop', compact('retText'));
            }
        //}
        //return view('cron.non-masking', compact('retText'));
        
        
        
        
        
        
        
        // //for ($loopNo1 = 1; $loopNo1 <= 10; $loopNo1++) {

        //     $getNonMaskingSmsCampaigns = SmsDesktopPending::
        //                                 where('sdp_target_time','<=', Carbon::now())
        //                                 ->whereIn('sdp_campaign_type', ['2','1'])
        //                                 ->where('sdp_campaign_status', 1)
        //                                 ->groupBy('sdp_message')
        //                                 ->take(10)
        //                                 ->orderBy('id', 'desc')
        //                                 ->get();

        //     // dd($getNonMaskingSmsCampaigns);
        //     if (count($getNonMaskingSmsCampaigns) > 0) {
        //         $smsLoop = 1;
        //         foreach ($getNonMaskingSmsCampaigns as $nonMaskingSmsCampaign) {

        //             $limitSms = 100;
        //             $sms = array();
        //             $transferredSmsId = array();
        //             $getSms50OfSameCampaignIds = SmsDesktopPending::where([
        //                 'campaign_id' => $nonMaskingSmsCampaign->campaign_id,
        //                 'sdp_campaign_status' => 1,
        //                 'sdp_message' => $nonMaskingSmsCampaign->sdp_message])
        //                 ->take($limitSms)
        //                 ->get();
        //             // dd($getSms50OfSameCampaignIds);
        //             $numbers = array();
        //             foreach ($getSms50OfSameCampaignIds as $sms50Details) {
        //                 // dd($sms50Details);
        //                 $numbers[] = $sms50Details->sdp_cell_no;
                        
        //                 $transferredSmsId[] = $sms50Details->id;
                    
        //             // dd($transferredSmsId);
        //             }

        //             $countTSms = 0;
        //             $userName = $nonMaskingSmsCampaign->api_user_name->routeDetail->user_name;
        //         // dd($userName);
                
        //             $password = $nonMaskingSmsCampaign->api_user_name->routeDetail->password;

                
                    
        //           $xml_response = \SmsHelper::send_desktop_sms($userName,$password,$numbers,$nonMaskingSmsCampaign->sdp_message);

                        
        //                 if ($xml_response->status == '-1') {
        //                     $retText = "Something was missing";
        //                 } elseif ($xml_response->status == '-4') {
        //                     // $retText = "Something Went Wrong to call robi non-masking api";
        //                     $retText = "content empty";
        //                 }elseif ($xml_response == 'blast') {
        //                     $retText = "something went wrong to call dynamic api";
        //                 } else {
        //                     $xmlResponseArray[] = $xml_response->array;
        //                     foreach($xmlResponseArray as $key => $array) {
        //                     foreach($array as $key1 => $value) {
        //                         $smsId = $array[$key1][1];
        //                         }
        //                     }
        //                     $blDataForInsert = array();
        //                     foreach($numbers as $number) {
        //                           //$xmlResponseArrayValue[] = ;
        //                           $checkedSms = SmsDesktopPending::where('id', $transferredSmsId[$countTSms])->first();
        //                         // dd($checkedSms);
        //                             $blDataForInsert[] = array(
        //                                 'user_id' => $checkedSms->user_id,
        //                                 // 'sender_id' => $checkedSms->sender_id,
        //                                 'campaign_id' => $checkedSms->campaign_id,
        //                                 'sdt_cell_no' => $checkedSms->sdp_cell_no,
        //                                 'sdt_message' => $checkedSms->sdp_message,
        //                                 'sdt_sms_cost' => $checkedSms->sdp_sms_cost,
        //                                 'operator_id' => $checkedSms->operator_id,
        //                                 'sdt_campaign_type' => $checkedSms->sdp_campaign_type,
        //                                 'sdt_deal_type' => $checkedSms->sdp_deal_type,
        //                                 'sdt_sms_type' => $checkedSms->sdp_sms_type,
        //                                 'sdt_sms_id' => $smsId,
        //                                 'sdt_sms_text_type' => $checkedSms->sdp_sms_text_type,
        //                                 'sdt_target_time' => $checkedSms->sdp_target_time,
        //                                 'created_at' => $checkedSms->created_at,
        //                                 'updated_at' => $checkedSms->updated_at,
        //                                 'sdt_delivery_report' => 'PENDING',
        //                                 'sdt_status' => '0',
        //                             );
        //                             $countTSms++;
                                
        //                     }
        //                     try {
        //                         SmsDesktop24h::insert($blDataForInsert);
        //                         $blDataForInsert = array();

        //                         SmsDesktopPending::whereIn('id', $transferredSmsId)->delete();

        //                         $retText = "Working...". $smsLoop++;
        //                     } catch (\Exception $e) {
        //                         $retText = "something went wrong" . $e->getMessage();
        //                         return view('cron.sms-desktop', compact('retText'));
        //                     }

        //                 }
                     


        //         }
        //         return view('cron.sms-desktop', compact('retText'));

        //     } else {
        //         $retText = "no sms found";
        //         return view('cron.sms-desktop', compact('retText'));
        //     }
        // //}
        // //return view('cron.non-masking', compact('retText'));
    }
    
    
    
   public function deliveryReportRoute2(){
        
                              // dd($tried);
        $getPendingData = SmsDesktop24h::where('sdt_sms_type',1)
                                       ->where('sdt_delivery_report','DELIVERED')
                                       ->where('sdt_tried','0')
                                       ->take(100)
                                       ->orderBy('id','desc')
                                       ->get();
                                       // dd($getPendingData);
        if (count($getPendingData) > 0) {
            foreach ($getPendingData as $data) {
                $smsId[] = $data->sdt_sms_id;
                $userName = $data->report_user_name->routeDetail->user_name;
                        
                    
                    
                $password = $data->report_user_name->routeDetail->password;
            }
            
            // dd($smsId);
    
            $jsonDeliveryReport = \SmsHelper::getRoute2DeliveryReport($smsId,$userName,$password);
            // dd($jsonDeliveryReport);
    
            if ($jsonDeliveryReport->status != '0') {
                $result = "Something Went Wrong!";
                
            }else {
                $delivryReport = $jsonDeliveryReport;
                $countCheckNumber = count($delivryReport->array);
                // dd($countCheckNumber);
                for ($i = 0; $i < $countCheckNumber; $i++) {
                    $loop = 1;
                    // dd($delivryReport->status);
                    if ($delivryReport->status != "1" || $delivryReport->status != "2") {
                        $smsId = $delivryReport->array[$i][0];
                        // dd($smsId);
                        $report = $delivryReport->array[$i][5];
                        $updReport = SmsDesktop24h::where('sdt_sms_id', $smsId)->first();
                        // dd($updReport);
                        if ($updReport) {
                            if ($report == '0') {
                                $updReport->sdt_delivery_report = "DELIVERED";
                                $updReport->sdt_tried = "0";
                            }elseif ($report == '1') {
                                $updReport->sdt_delivery_report = "DELIVERED";
                                $updReport->sdt_tried = "1";
                            } elseif ($report == '2') {
                                $updReport->sdt_delivery_report = "FAILED";
                                $updReport->sdt_tried = "2";
                            }elseif ($report == '3') {
                                $updReport->sdt_delivery_report = "DELIVERED";
                                $updReport->sdt_tried = "3";
                            }elseif ($report == '4') {
                                $updReport->sdt_delivery_report = "TIME OUT";
                                $updReport->sdt_tried = "4";
                            }elseif ($report == '5') {
                                $updReport->sdt_delivery_report = "OTHER";
                                $updReport->sdt_tried = "5";
                            }
                            
                            $updReport->save();
                            
                        }

                    } 
                }
                
            }
            $result = "Working....";
        }else {
            $result = "No Pending Data";   
        }
        $delivered = SmsDesktop24h::where('sdt_sms_type',1)
                                        ->where('sdt_tried',3)
                                        ->count();
        $failed = SmsDesktop24h::where('sdt_sms_type',1)
                                    ->where('sdt_tried',2)
                                    ->count();
        $sentButNotRecieve = SmsDesktop24h::where('sdt_sms_type',1)
                                    ->where('sdt_tried',1)
                                    ->count();
        $notEnableDeliver = SmsDesktop24h::where('sdt_sms_type',1)
                                    ->where('sdt_tried',0)
                                    ->count();
        
        
        return view('cron.route2-delivery',compact('result','delivered','failed','sentButNotRecieve','notEnableDeliver'));
    }

    public function deliveryReport()
    {
        $chengedNumber = 0;

        /*set offsetData in session if wasn't set previous*/
        if (!isset($_SESSION['offsetData'])) {
            $_SESSION['offsetData'] = 0;
        }

        /*set goToNullOffset in session if wasn't set previous*/
        if (!isset($_SESSION['goToNullOffset'])) {
            $_SESSION['goToNullOffset'] = 0;
        }

        for ($j = 0; $j < 10; $j++) {

            /*set offsetData variable based on session offsetData & goToNullOffset*/
            if ($_SESSION['goToNullOffset'] == 0) {
                $offsetData = $_SESSION['offsetData'];

            } else {
                $offsetData = 0;
                $_SESSION['offsetData'] = 0;
                $_SESSION['goToNullOffset'] = 0;
            }


            

            /*get undelivered numbers*/
            $pendingNumbers = SmsDesktop24h::select('sdt_sms_id','user_id')->where('sdt_sms_type', '1')
                ->whereIn('sdt_delivery_report',['PENDING','NOT ENABLE DELIVER','SENT BUT NOT RECIEVE DELIVER'])
                ->skip($offsetData)
                ->take(200)
                ->get();

            // dd($pendingNumbers);

            $undeliveredNumber = Null;
            if (count($pendingNumbers) < 200) {
                $_SESSION['goToNullOffset'] = 1;
            }
            if (count($pendingNumbers) > 0) {

                $numberOfRows = count($pendingNumbers);

                foreach ($pendingNumbers as $pendingNumber) {
                    if (!empty($pendingNumber['sdt_sms_id'])) {
                        if ($undeliveredNumber == null) {
                            $undeliveredNumber = $pendingNumber['sdt_sms_id'];
                        } else {
                            $undeliveredNumber = $undeliveredNumber . "," . $pendingNumber['sdt_sms_id'];
                        }
                    }
                    $userName = $pendingNumber->report_user_name->routeDetail->user_name;
                    
                
                
                    $password = $pendingNumber->report_user_name->routeDetail->password;
                    // dd($password);
                }


                $jsonDeliveryReport = \SmsHelper::getRoute2DeliveryReport($undeliveredNumber,$userName,$password);
               
                // dd($jsonDeliveryReport);
                
                if ($jsonDeliveryReport == '0150') {
                    $returnData['no_number'] = "something went wrong";
                } elseif ($jsonDeliveryReport == '0160') {
                    $returnData['no_number'] = "something went wrong";
                } else {
                    $delivryReport = $jsonDeliveryReport;
                    
                    
                    //dd($xmlResponseArrayValue);
                   
                    $countCheckNumber = count($delivryReport->array);
                    

                    $_SESSION['offsetData'] = $_SESSION['offsetData'] + ($numberOfRows - $countCheckNumber);
                    
                    for ($i = 0; $i < $countCheckNumber; $i++) {
                        if ($delivryReport->status != "1" || $delivryReport->status != "2") {
                            $smsId = $delivryReport->array[$i][0];
                            $report = $delivryReport->array[$i][5];
                            $updReport = SmsDesktop24h::where('sdt_sms_id', $smsId)->first();
                            // dd($updReport);
                            if ($updReport) {
                                if ($report == '0') {
                                    $updReport->sdt_delivery_report = "NOT ENABLE DELIVER";
                                }elseif ($report == '1') {
                                    $updReport->sdt_delivery_report = "SENT BUT NOT RECIEVE DELIVER";
                                } elseif ($report == '2') {
                                    $updReport->sdt_delivery_report = "FAILED";
                                }elseif ($report == '3') {
                                    $updReport->sdt_delivery_report = "DELIVERED";
                                }elseif ($report == '4') {
                                    $updReport->sdt_delivery_report = "TIME OUT";
                                }elseif ($report == '5') {
                                    $updReport->sdt_delivery_report = "OTHER";
                                }
                                
                                $updReport->save();
                                $chengedNumber++;
                            }

                        } else {
                            $_SESSION['offsetData']++;
                        }
                    }
                }
            } else {
                $returnData['no_number'] = "no number available for check report";
            }

            if ($_SESSION['goToNullOffset'] == 1) {
                break;
            }

        }

//        SmsCampaign_24h::where('sct_sms_type', '2')->update(['sct_delivery_report'=>'DELIVERED']);

        $returnData['still_pending'] = $_SESSION['offsetData'];
        $returnData['check_complete'] = $_SESSION['goToNullOffset'];
        $returnData['changed'] = $chengedNumber;

        // try {
        //     DB::transaction(function () {
        //         $moveDatasFromToday = SmsDesktop24h::where('sdt_target_time', '<=', Carbon::now()->subHours(24))->get();
        //         // dd($moveDatasFromToday);
        //         foreach ($moveDatasFromToday as $moveData) {
        //             SmsDesktop::create([
        //                 'user_id' => $moveData->user_id,
        //                 //'sender_id' => $moveData->sender_id,
        //                 'campaign_id' => $moveData->campaign_id,
        //                 'sd_cell_no' => $moveData->sdt_cell_no,
        //                 'sd_message' => $moveData->sdt_message,
        //                 'sd_customer_message' => $moveData->sdt_customer_message,
        //                 'sd_sms_cost' => $moveData->sdt_sms_cost,
        //                 'operator_id' => $moveData->operator_id,
        //                 'sd_campaign_type' => $moveData->sdt_campaign_type,
        //                 'sd_deal_type' => $moveData->sdt_deal_type,
        //                 'sd_sms_type' => $moveData->sdt_sms_type,
        //                 'sd_sms_id' => $moveData->sdt_sms_id,
        //                 'sd_sms_text_type' => $moveData->sdt_sms_text_type,
        //                 'sd_submitted_time' => $moveData->created_at,
        //                 'sd_targeted_time' => $moveData->sdt_target_time,
        //                 'sd_delivery_report' => $moveData->sdt_delivery_report,
        //                 'sd_status' => $moveData->sdt_status,
        //             ]);
        //         }

        //         SmsDesktop24h::where('sdt_target_time', '<=', Carbon::now()->subHours(24))->delete();

        //     });
        // } catch (\Exception $e) {

        // }

        return view('cron.sms-desktop-delivery', compact('returnData'));

    }
    
    
    public function nonMaskingSmsa()
    {
        //for ($loopNo1 = 1; $loopNo1 <= 10; $loopNo1++) {

            $retText = "no sms found";
       


        $getNonMaskingSmsCampaigns = SmsDesktop24h::
            where('sdt_target_time','<=', Carbon::now()->subHours(24))
            ->groupBy('sdt_message')
            ->take(10)
            ->orderBy('id', 'asc')
            ->get();

        // dd($getNonMaskingSmsCampaigns);
        if (count($getNonMaskingSmsCampaigns) > 0) {
            $smsLoop = 1;
            foreach ($getNonMaskingSmsCampaigns as $nonMaskingSmsCampaign) {

                $limitSms = 500;
                $sms = array();
                $transferredSmsId = array();
                $getSms50OfSameCampaignIds = SmsDesktop24h::where([
                    'campaign_id' => $nonMaskingSmsCampaign->campaign_id,
                    'sdt_message' => $nonMaskingSmsCampaign->sdt_message])
                    ->take($limitSms)
                    ->get();
                // dd($getSms50OfSameCampaignIds);
                $numbers = array();
                foreach ($getSms50OfSameCampaignIds as $sms50Details) {
                    // dd($sms50Details);
                    $numbers[] = $sms50Details->sdt_cell_no;
                    // dd($numbers);
                    $transferredSmsId[] = $sms50Details->id;
                
                // dd($transferredSmsId);
                }
                // dd($numbers);
                $countTSms = 0;
                
               
                   
                   
                
                            $blDataForInsert = array();
                            
                            foreach($numbers as $number) {
                                      $checkedSms = SmsDesktop24h::where('id', $transferredSmsId[$countTSms])->first();
                                // dd($checkedSms);
                                    $blDataForInsert[] = array(
                                        'user_id' => $checkedSms->user_id,
                                        //'sender_id' => $moveData->sender_id,
                                        'campaign_id' => $checkedSms->campaign_id,
                                        'sd_cell_no' => $checkedSms->sdt_cell_no,
                                        'sd_message' => $checkedSms->sdt_message,
                                        'sd_customer_message' => $checkedSms->sdt_customer_message,
                                        'sd_sms_cost' => $checkedSms->sdt_sms_cost,
                                        'operator_id' => $checkedSms->operator_id,
                                        'sd_campaign_type' => $checkedSms->sdt_campaign_type,
                                        'sd_deal_type' => $checkedSms->sdt_deal_type,
                                        'sd_sms_type' => $checkedSms->sdt_sms_type,
                                        'sd_sms_id' => $checkedSms->sdt_sms_id,
                                        'sd_sms_text_type' => $checkedSms->sdt_sms_text_type,
                                        'sd_submitted_time' => $checkedSms->created_at,
                                        'sd_targeted_time' => $checkedSms->sdt_target_time,
                                        'created_at' => Carbon::now(),
                                        'updated_at' => Carbon::now(),
                                        'sd_delivery_report' => $checkedSms->sdt_delivery_report,
                                        'sd_status' => $checkedSms->sdt_status,
                                    );
                                    $countTSms++;
                                
                            }            
                            // dd($blDataForInsert);
                          DB::beginTransaction();
                            try {
                                SmsDesktop::insert($blDataForInsert);
                                $blDataForInsert = array();

                                SmsDesktop24h::whereIn('id', $transferredSmsId)->delete();
                                DB::commit();
                                $retText = "Working...". $smsLoop++;
                                return view('cron.sms-desktop-delete', compact('retText'));
                                
                            } catch (\Exception $e) {
                                DB::rollback();
                                $retText = "something went wrong" . $e->getMessage();
                                return view('cron.sms-desktop-delete', compact('retText'));
                            }

                        // }
                
                
                    
                    
                    
                // }
            }
            

            
        }
        return view('cron.sms-desktop-delete', compact('retText'));

    }

// public function deliveryReport()
//     {
//         $chengedNumber = 0;

//         /*set offsetData in session if wasn't set previous*/
//         if (!isset($_SESSION['offsetData'])) {
//             $_SESSION['offsetData'] = 0;
//         }

//         /*set goToNullOffset in session if wasn't set previous*/
//         if (!isset($_SESSION['goToNullOffset'])) {
//             $_SESSION['goToNullOffset'] = 0;
//         }

//         // for ($j = 0; $j < 10; $j++) {

//         //     /*set offsetData variable based on session offsetData & goToNullOffset*/
//         //     if ($_SESSION['goToNullOffset'] == 0) {
//         //         $offsetData = $_SESSION['offsetData'];

//         //     } else {
//         //         $offsetData = 0;
//         //         $_SESSION['offsetData'] = 0;
//         //         $_SESSION['goToNullOffset'] = 0;
//         //     }

//         //     /*get undelivered numbers*/
//         //     $pendingNumbers = SmsCampaign_24h::select('id', 'sender_id', 'sct_sms_id', 'sct_sms_text_type')->where(['sct_sms_type' => '2', 'operator_id' => '3', 'sct_delivery_report' => 'PENDING'])->skip($offsetData)->take(50)->get();
//         //     $undeliveredNumber = Null;
//         //     if (count($pendingNumbers) < 50) {
//         //         $_SESSION['goToNullOffset'] = 1;
//         //     }
//         //     if (count($pendingNumbers) > 0) {

//         //         foreach ($pendingNumbers as $pendingNumber) {
//         //             try{

//         //                 if (!empty($pendingNumber['sct_sms_id'])) {
//         //                     if ($pendingNumber['sct_sms_text_type'] == 'text') {
//         //                         $messageType = '1';
//         //                     } else {
//         //                         $messageType = '2';
//         //                     }
//         //                     $userName = $pendingNumber->sender->gp_virtual_number->sivn_api_user_name;
//         //                     $password = $pendingNumber->sender->gp_virtual_number->sivn_api_password;
//         //                     $cli = $pendingNumber->sender->sir_sender_id;

//         //                     $url = "https://gpcmp.grameenphone.com/gpcmpapi/messageplatform/controller.home?username=" . $userName . "&password=" . $password . "&apicode=4&msisdn=01700000000&countrycode=0&cli=" . $cli . "&messagetype=" . $messageType . "&message=0&messageid=" . $pendingNumber['sct_sms_id'];
//         //                     if($pendingNumber['sct_sms_id'] == '20190328-5453-553762327068-01708403276-02')
//         //                         dd($url);

//         //                     $client = new Client();

//         //                     $res = $client->request('GET', $url, ['verify' => false]);
//         //                     $ret = $res->getBody()->getContents();

//         //                     $explodeRet = explode(',', $ret);
//         //                     if ($explodeRet[0] == '200') {

//         //                         $explodeText = explode('#', $explodeRet[1]);
//         //                         $retText = strtoupper($explodeText[1]);
//         //                         try {
//         //                             SmsCampaign_24h::where('id', $pendingNumber['id'])->update(['sct_delivery_report' => $retText]);
//         //                             $returnData['success'] = 'working...';
//         //                         } catch (\Exception $e) {
//         //                             $returnData['error'] = "something went wrong.";
//         //                         }

//         //                     } else {
//         //                         $_SESSION['offsetData']++;
//         //                     }
//         //                 }

//         //             } catch(\Exception $e) {
//         //                 $returnData['error'] = "something went wrong.".$e->getMessage;
//         //             }
//         //         }


//         //     } else {
//         //         $returnData['no_number'] = "no number available for check report";
//         //     }

//         //     if ($_SESSION['goToNullOffset'] == 1) {
//         //         break;
//         //     }

//         // }

//         // SmsCampaign_24h::where('sct_sms_type', '2')->where('operator_id', '!=', '3')->where('sct_delivery_report', 'PENDING')->update(['sct_delivery_report' => 'DELIVERED']);
//         SmsDesktop24h::where(['sdt_sms_type' => '1', 'sdt_delivery_report' => 'PENDING'])->update(['sdt_delivery_report' => 'DELIVERED']);

//         $returnData['still_pending'] = $_SESSION['offsetData'];
//         $returnData['check_complete'] = $_SESSION['goToNullOffset'];


//         return view('cron.sms-desktop-delivery', compact('returnData'));

//     }

    public function delete_data(){
        if (!isset($_SESSION['offsetData'])) {
            $_SESSION['offsetData'] = 0;
        }

        /*set goToNullOffset in session if wasn't set previous*/
        if (!isset($_SESSION['goToNullOffset'])) {
            $_SESSION['goToNullOffset'] = 0;
        }
        try {
            DB::transaction(function () {
                $moveDatasFromToday = SmsDesktop24h::where('sdt_target_time', '<=', Carbon::now()->subHours(24))->get();
                foreach ($moveDatasFromToday as $moveData) {
                    SmsDesktop::create([
                        'user_id' => $moveData->user_id,
                        'campaign_id' => $moveData->campaign_id,
                        'sd_cell_no' => $moveData->sdt_cell_no,
                        'sd_message' => $moveData->sdt_message,
                        'sd_sms_cost' => $moveData->sdt_sms_cost,
                        'operator_id' => $moveData->operator_id,
                        'sd_campaign_type' => $moveData->sdt_campaign_type,
                        'sd_deal_type' => $moveData->sdt_deal_type,
                        'sd_sms_type' => $moveData->sdt_sms_type,
                        'sd_sms_id' => $moveData->sdt_sms_id,
                        'sd_sms_text_type' => $moveData->sdt_sms_text_type,
                        'sd_submitted_time' => $moveData->created_at,
                        'sd_targeted_time' => $moveData->sdt_target_time,
                        'sd_delivery_report' => $moveData->sdt_delivery_report,
                        'sd_status' => $moveData->sdt_status,
                    ]);
                }

                SmsDesktop24h::where('sdt_target_time', '<=', Carbon::now()->subHours(24))->delete();

            });
        } catch (\Exception $e) {

        }
        $returnData['still_pending'] = $_SESSION['offsetData'];
        $returnData['check_complete'] = $_SESSION['goToNullOffset'];
        return view('cron.sms-desktop-delete', compact('returnData'));

    }


    


}
