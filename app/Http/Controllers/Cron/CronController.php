<?php

namespace App\Http\Controllers\Cron;

use App\Model\User;
use App\Model\ErrorNotification;
use App\Model\SenderIdRegister;
use App\Model\SenderIdVirtualNumber;
use App\Model\SmsCampaign;
use App\Model\SmsCampaign_24h;
use App\Model\SmsCamPending;
use App\Model\UserDetail;
use App\Model\SmsDesktopCampaignId;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class CronController extends Controller
{
    /*send non masking sender id*/
    public function nonMaskingSms()
    {
        //for ($loopNo1 = 1; $loopNo1 <= 10; $loopNo1++) {

            $getNonMaskingSmsCampaigns = SmsCamPending::where('scp_sms_type', '1')
                ->where('scp_target_time', '<=', Carbon::now())
                ->where('scp_campaign_status', 1)
                ->groupBy('sender_id')
                ->groupBy('scp_message')
                ->take(10)
                ->orderBy('id', 'asc')
                ->get();


            if (count($getNonMaskingSmsCampaigns) > 0) {
                $smsLoop = 1;
                foreach ($getNonMaskingSmsCampaigns as $nonMaskingSmsCampaign) {

                    $limitSms = 300;
                    $sms = array();
                    $transferredSmsId = array();
                    $getSms50OfSameCampaignIds = SmsCamPending::where([
                        'campaign_id' => $nonMaskingSmsCampaign->campaign_id,
                        'scp_campaign_status' => 1,
                        'scp_message' => $nonMaskingSmsCampaign->scp_message,
                        'sender_id' => $nonMaskingSmsCampaign->sender_id])
                        ->take($limitSms)
                        ->get();

                    $numbers = array();
                    foreach ($getSms50OfSameCampaignIds as $sms50Details) {
                        $numbers[] = $sms50Details->scp_cell_no;
                        $transferredSmsId[] = $sms50Details->id;
                    }

                    $countTSms = 0;
                    $sender_id = $nonMaskingSmsCampaign->sender->sir_sender_id;

                    if (substr($sender_id, 0, 5) == '88018') {

                        $senderIdDet = SenderIdRegister::with('robi_virtual_number')->where('sir_sender_id', $sender_id)->first();
                        if (empty($senderIdDet)) {
                            $retText = "Something was missing1";
                            continue;
                        }


                        $xml_response = \SmsHelper::send_masking_mobireach_sms('iglweb_longcode', 'Robi@445932', $nonMaskingSmsCampaign->scp_message, $numbers, $sender_id);
                        // dd($xml_response);
                        if ($xml_response == '0150') {
                            $retText = "Something was missing";
                        } elseif ($xml_response == '0160') {
                            // $retText = "Something Went Wrong to call robi non-masking api";
                            $retText = "Something Went Wrong to call robi non-masking api";
                        } else {
                            $dataForInsert = array();

                            foreach ($xml_response as $smsReport) {

                                $checkedSms = SmsCamPending::where('id', $transferredSmsId[$countTSms])->first();
                                $dataForInsert[] = array(
                                    'user_id' => $checkedSms->user_id,
                                    'sender_id' => $checkedSms->sender_id,
                                    'campaign_id' => $checkedSms->campaign_id,
                                    'sct_cell_no' => $checkedSms->scp_cell_no,
                                    'sct_message' => $checkedSms->scp_message,
                                    'sct_sms_cost' => $checkedSms->scp_sms_cost,
                                    'operator_id' => $checkedSms->operator_id,
                                    'sct_campaign_type' => $checkedSms->scp_campaign_type,
                                    'sct_deal_type' => $checkedSms->scp_deal_type,
                                    'sct_sms_type' => $checkedSms->scp_sms_type,
                                    'sct_sms_id' => $smsReport->MessageId,
                                    'sct_sms_text_type' => $checkedSms->scp_sms_text_type,
                                    'sct_target_time' => $checkedSms->scp_target_time,
                                    'created_at' => $checkedSms->created_at,
                                    'updated_at' => $checkedSms->updated_at,
                                    'sct_delivery_report' => 'PENDING',
                                    'sct_status' => $smsReport->Status,
                                );

                                $countTSms++;
                            }
                            try {
                                SmsCampaign_24h::insert($dataForInsert);
                                $dataForInsert = array();

                                SmsCamPending::whereIn('id', $transferredSmsId)->delete();
                                $retText = "Working..." . $smsLoop++;
                            } catch (\Exception $e) {
                                $retText = "something went wrong";
                                return view('cron.non-masking', compact('retText'));
                            }

                        }
                    } elseif ((substr($sender_id, 0, 5) == '88017') || (substr($sender_id, 0, 5) == '88013')) {

                        $senderIdDet = SenderIdRegister::with('gp_virtual_number')->where('sir_sender_id', $sender_id)->first();
                        if (empty($senderIdDet)) {
                            $retText = "Something was missing1";
                            continue;
                        }


                        $user_name = "gp_user_name";
                        $password = "gp_user_password";
                        $xml_response = \SmsHelper::update_send_masking_gp_sms($user_name, $password, $sender_id, $nonMaskingSmsCampaign->scp_message, $numbers);
                        $gpDataForInsert = array();



                        $response_array = explode("\n", $xml_response);
                        $status_code = explode(',', $response_array[0]);

                        // var_dump($user_name);
                        // var_dump($password);
                        // var_dump($sender);
                        // var_dump($status_code);
                        // Trying to send from virtual number in the case of The CLI invalid
                        if ( $status_code[0] == 208 ){

                            $returnError['gpError'] = "Sender id doesn't registered. Sender ID : <b>".$sender_id."</b>";

                        } elseif ( $status_code[0] == 201 || $status_code[0] == 216 || $status_code[0] == 203 || $status_code[0] == 203 || $status_code[0] == 204 || $status_code[0] == 231  || $status_code[1] == 231 ){

                            if ($status_code[0] == 216) {
                                $check_v_n = $sender_id;
                                // dd($check_v_n);
                                $exists_load = SenderIdVirtualNumber::where('sivn_number', $check_v_n)
                                    ->where(function ($query) {
                                        $query->where('last_load_time' , '>', Carbon::now()->subMinutes(20) );

                                    })
                                    ->first();
                                // dd($exists_load);
                                if ( !$exists_load ) {
                                    $load = SenderIdVirtualNumber::where('sivn_number', $check_v_n)
                                        ->first();
                                    if (!empty($load)) {
                                        if ($load->sivn_load_amount >= 10) {
                                            try {
                                                $load_number = $check_v_n;

                                                $flexi_api_key = "445015812203613491581220361";
                                                $pin = "3799";
                                                $amount = $load->sivn_load_amount;
                                                $number_type = "2";
                                                $operator = "gp";
                                                $url = "http://127.0.0.1/api/v1/send-load?api_key=$flexi_api_key&pin=$pin&number=$load_number&amount=$amount&number_type=$number_type&operator=$operator";

                                                $client = new Client();
                                                $res = $client->request('GET', $url);
                                                $ret = $res->getBody();

                                                $load->last_load_time = Carbon::now();
                                                $load->save();
                                                dump("laod requested");
                                            } catch (\Exception $e) {
                                                dump('Auto Load Problem. ' . $e->getMessage());
                                            }
                                        }
                                    }
                                }
                            }


                            $returnError['gpError'] = "something went wrong to send gp sms. Status Code : ".$status_code[0]." Sender ID : ".$sender_id;

                        }else{
                            foreach ($response_array as $oneReport){
                                $report_details = explode(',', $oneReport);
                                // $report_details[0] = number, $report_details[1] = status, $report_details[2] = message ID,

                                $aaa = isset($report_details[2])?@$report_details[2]:@$report_details[1];
                                if($aaa == "TPS limit Exceeded")
                                    continue;

                                if (isset($report_details[1])) {
                                    $gpSmsDetail = SmsCamPending::where('id', $transferredSmsId[$countTSms])->first();
                                    $gpTransferredForDelete[] = $gpSmsDetail->id;

                                    $gpDataForInsert[] = array(
                                        'user_id' => $gpSmsDetail->user_id,
                                        'sender_id' => $gpSmsDetail->sender_id,
                                        'campaign_id' => $gpSmsDetail->campaign_id,
                                        'sct_cell_no' => $gpSmsDetail->scp_cell_no,
                                        'sct_message' => $gpSmsDetail->scp_message,
                                        'sct_sms_cost' => $gpSmsDetail->scp_sms_cost,
                                        'operator_id' => $gpSmsDetail->operator_id,
                                        'sct_campaign_type' => $gpSmsDetail->scp_campaign_type,
                                        'sct_deal_type' => $gpSmsDetail->scp_deal_type,
                                        'sct_sms_type' => $gpSmsDetail->scp_sms_type,
                                        'sct_sms_id' => isset($report_details[2])?@$report_details[2]:@$report_details[1],
                                        'sct_sms_text_type' => $gpSmsDetail->scp_sms_text_type,
                                        'sct_target_time' => $gpSmsDetail->scp_target_time,
                                        'created_at' => $gpSmsDetail->created_at,
                                        'updated_at' => $gpSmsDetail->updated_at,
                                        'sct_delivery_report' => 'PENDING',
                                        'sct_status' => isset($report_details[2])?@$report_details[1]:@$report_details[0],
                                    );
                                }
                                $countTSms++;
                            }
                            try {
                                SmsCampaign_24h::insert($gpDataForInsert);
                                $gpDataForInsert = array();

                                SmsCamPending::whereIn('id', $gpTransferredForDelete)->delete();
                                $returnData['gp'] = "GP is Working...";
                            } catch (\Exception $e) {
                                $returnError['gpError'] = "something went wrong" . $e->getMessage();
                            }
                        }
                    }  elseif ((substr($sender_id, 0, 5) == '88019') || (substr($sender_id, 0, 5) == '88014')) {


                        $senderIdDet = SenderIdRegister::with('banglalink_virtual_number')->where('sir_sender_id', $sender_id)->first();
                        if (empty($senderIdDet)) {
                            $retText = "Something was missing1";
                            continue;
                        }


                        $user_name = "bl_user_name";
                        $password = "bl_user_password";
                        $xml_response = \SmsHelper::send_masking_banglalink_sms($user_name, $password, $nonMaskingSmsCampaign->scp_message, $numbers, $sender_id);
                        if ($xml_response == '0150') {
                            $returnError['blError'] = "something was missing for banglalink masking sms";
                        } elseif ($xml_response == 'bl_error') {
                            $returnError['blError'] = "something went wrong to send banglalink masking sms";
                        } else {
                            $blDataForInsert = array();
                            foreach ($numbers as $blNumber) {

                                $checkedSms = SmsCamPending::where('id', $transferredSmsId[$countTSms])->first();
                                $blTransferred[] = $checkedSms->id;


                                $blDataForInsert[] = array(
                                    'user_id' => $checkedSms->user_id,
                                    'sender_id' => $checkedSms->sender_id,
                                    'campaign_id' => $checkedSms->campaign_id,
                                    'sct_cell_no' => $checkedSms->scp_cell_no,
                                    'sct_message' => $checkedSms->scp_message,
                                    'sct_sms_cost' => $checkedSms->scp_sms_cost,
                                    'operator_id' => $checkedSms->operator_id,
                                    'sct_campaign_type' => $checkedSms->scp_campaign_type,
                                    'sct_deal_type' => $checkedSms->scp_deal_type,
                                    'sct_sms_type' => $checkedSms->scp_sms_type,
                                    'sct_sms_id' => '0',
                                    'sct_sms_text_type' => $checkedSms->scp_sms_text_type,
                                    'sct_target_time' => $checkedSms->scp_target_time,
                                    'created_at' => $checkedSms->created_at,
                                    'updated_at' => $checkedSms->updated_at,
                                    'sct_delivery_report' => 'PENDING',
                                    'sct_status' => '0',
                                );

                                $countTSms++;
                            }
                            try {
                                SmsCampaign_24h::insert($blDataForInsert);
                                $blDataForInsert = array();

                                SmsCamPending::whereIn('id', $blTransferred)->delete();

                                $returnData['banglalink'] = "Banglalink Non Masking Working...";

                            } catch (\Exception $e) {
                                $returnError['blError'] = "something went wrong" . $e->getMessage();
                            }

                        }
                    } else {
                        $xml_response = \SmsHelper::send_non_masking_single_sms('IGLWEBLTD', 'igl2web1com', $nonMaskingSmsCampaign->scp_message, $numbers, $sender_id);

                        if ($xml_response == '0150') {
                            $retText = "Something was missing";
                            return view('cron.non-masking', compact('retText'));
                        } else {
                            $dataForInsert = array();

                            foreach ($xml_response as $smsReport) {

                                /*echo "message id: " . $smsReport->messageid . "<br>";
                                echo "status: " . $smsReport->status . "<br>";
                                echo "destination: " . $smsReport->destination . "<br>";*/
                                $checkedSms = SmsCamPending::where('id', $transferredSmsId[$countTSms])->first();
                                $dataForInsert[] = array(
                                    'user_id' => $checkedSms->user_id,
                                    'sender_id' => $checkedSms->sender_id,
                                    'campaign_id' => $checkedSms->campaign_id,
                                    'sct_cell_no' => $smsReport->destination,
                                    'sct_message' => $checkedSms->scp_message,
                                    'sct_sms_cost' => $checkedSms->scp_sms_cost,
                                    'operator_id' => $checkedSms->operator_id,
                                    'sct_campaign_type' => $checkedSms->scp_campaign_type,
                                    'sct_deal_type' => $checkedSms->scp_deal_type,
                                    'sct_sms_type' => $checkedSms->scp_sms_type,
                                    'sct_sms_id' => $smsReport->messageid,
                                    'sct_sms_text_type' => $checkedSms->scp_sms_text_type,
                                    'sct_target_time' => $checkedSms->scp_target_time,
                                    'created_at' => $checkedSms->created_at,
                                    'updated_at' => $checkedSms->updated_at,
                                    'sct_delivery_report' => 'PENDING',
                                    'sct_status' => $smsReport->status,
                                );

                                $countTSms++;
                            }

                            try {
                                SmsCampaign_24h::insert($dataForInsert);
                                $dataForInsert = array();

                                SmsCamPending::whereIn('id', $transferredSmsId)->delete();

                                $retText = "Working..." . $smsLoop++;

                            } catch (\Exception $e) {
                                $retText = "something went wrong";
                                return view('cron.non-masking', compact('retText'));
                            }

                        }
                    }


                }
                return view('cron.non-masking', compact('retText'));

            } else {
                $retText = "no sms found";
                return view('cron.non-masking', compact('retText'));
            }
        //}
        //return view('cron.non-masking', compact('retText'));

    }


    public function maskingSms()
    {
        for ($loopNo = 1; $loopNo <= 25; $loopNo++) {

            $getMaskingSmsCampaigns = SmsCamPending::where('scp_sms_type', '2')
                ->where('scp_target_time', '<=', Carbon::now())
                ->where('scp_campaign_status', 1)
                ->groupBy('sender_id')
                ->groupBy('scp_message')
                ->groupBy('campaign_id')
                ->orderBy('id', 'desc')
                ->take(20)
                ->get();

            // dd($getMaskingSmsCampaigns);

            if (count($getMaskingSmsCampaigns) > 0) {

                $limitSms = 50;
                $returnError = array();
                $returnData = array();

                foreach ($getMaskingSmsCampaigns as $maskingSmsCampaign) {
                    $gpNumbers = array();
                    $blNumbers = array();
                    $raNumbers = array();
                    $ttNumbers = array();
                    $gpTransferred = array();
                    $blTransferred = array();
                    $raTransferred = array();
                    $ttTransferred = array();
                    $virtualNumber = SenderIdRegister::with('robi_virtual_number', 'airtel_virtual_number', 'banglalink_virtual_number', 'teletalk_virtual_number', 'gp_virtual_number')
                        ->where('id', $maskingSmsCampaign->sender_id)
                        ->first();
                    // dd($virtualNumber);

                    /*
                     * -----------------
                     * start send gp sms
                     * -----------------
                     * */

                    try {
                        $gpSmsOfThisCampaigns = SmsCamPending::where([
                            'campaign_id' => $maskingSmsCampaign->campaign_id,
                            'scp_campaign_status' => 1,
                            'scp_message' => $maskingSmsCampaign->scp_message,
                            'sender_id' => $maskingSmsCampaign->sender_id,
                            'operator_id' => '3'])
                            ->orderBy('id', 'desc')
                            ->take(50)
                            ->get();/*gp = 3*/
                            // dd($gpSmsOfThisCampaigns);


                        if (count($gpSmsOfThisCampaigns) > 0) {

                            /*Editin zone start*/
                            /*-----------------------------*/
                            /*-----------------------------*/
                            /*-----------------------------*/
                            foreach ($gpSmsOfThisCampaigns as $gpSmsDetails) {

                                $gpNumbers[] = $gpSmsDetails->scp_cell_no;
                                $gpTransferred[] = $gpSmsDetails->id;

                            }

                            $countTSms = 0;

                            $user_name = $virtualNumber->gp_virtual_number->sivn_api_user_name;
                            $v_n = substr($virtualNumber->gp_virtual_number->sivn_number, 2);
                            $password = $virtualNumber->gp_virtual_number->sivn_api_password;
                            $sender = $maskingSmsCampaign->sender->sir_sender_id;
                            $sms_text = $maskingSmsCampaign->scp_message;

                            $xml_response = \SmsHelper::update_send_masking_gp_sms($user_name, $password, $sender, $sms_text, $gpNumbers);
                            $gpDataForInsert = array();



                            $response_array = explode("\n", $xml_response);
                            $status_code = explode(',', $response_array[0]);

                            // var_dump($user_name);
                            // var_dump($password);
                            // var_dump($sender);
                            // var_dump($status_code);
                            // Trying to send from virtual number in the case of The CLI invalid
                            if ( $status_code[0] == 208 ){

                                $xml_response = \SmsHelper::update_send_masking_gp_sms($user_name, $password, $v_n, $sms_text, $gpNumbers);
                                $gpDataForInsert = array();
                                $response_array = explode("\n", $xml_response);
                                $status_code = explode(',', $response_array[0]);

                            }
                            // var_dump($status_code);

                            if ( $status_code[0] == 201 || $status_code[0] == 216 || $status_code[0] == 203 || $status_code[0] == 203 || $status_code[0] == 204 || $status_code[0] == 231  || $status_code[1] == 231 ){

                                if ($status_code[0] == 216) {
                                    $check_v_n = "88".$v_n;
                                    // dd($check_v_n);
                                    $exists_load = SenderIdVirtualNumber::where('sivn_number', $check_v_n)
                                        ->where(function ($query) {
                                            $query->where('last_load_time' , '>', Carbon::now()->subMinutes(20) );

                                        })
                                        ->first();
                                    // dd($exists_load);
                                    if ( !$exists_load ) {
                                        $load = SenderIdVirtualNumber::where('sivn_number', $check_v_n)
                                            ->first();
                                        if (!empty($load)) {
                                            if ($load->sivn_load_amount >= 10) {
                                                try {
                                                    $load_number = $check_v_n;

                                                    $flexi_api_key = "445015812203613491581220361";
                                                    $pin = "3799";
                                                    $amount = $load->sivn_load_amount;
                                                    $number_type = "2";
                                                    $operator = "gp";
                                                    $url = "http://127.0.0.1/api/v1/send-load?api_key=$flexi_api_key&pin=$pin&number=$load_number&amount=$amount&number_type=$number_type&operator=$operator";

                                                    $client = new Client();
                                                    $res = $client->request('GET', $url);
                                                    $ret = $res->getBody();

                                                    $load->last_load_time = Carbon::now();
                                                    $load->save();
                                                    dump("laod requested");
                                                } catch (\Exception $e) {
                                                    dump('Auto Load Problem. ' . $e->getMessage());
                                                }
                                            }
                                        }
                                    }
                                }

                                $exists = ErrorNotification::where('user_id', $gpSmsOfThisCampaigns[0]->user_id)
                                    ->where('operator_id', 3)
                                    ->where('created_at' , '>', Carbon::now()->subHours(6) )
                                    ->first();
                                if ( !$exists ) {
                                    try {
                                        $sender_id_name = SenderIdRegister::where('id', $gpSmsOfThisCampaigns[0]->sender_id)->pluck('sir_sender_id');
                                        $sms_text = "Error Message ! \nUser ID : ".$gpSmsOfThisCampaigns[0]->user_id.".\nSender ID : ".$sender_id_name[0].".\nCampaign ID: ".$gpSmsOfThisCampaigns[0]->campaign_id.".\nMessage : ".$status_code[1];
                                        $sms_text = urlencode($sms_text);
                                        $number = "01823037726";
                                        $sender = "8801844532630";
                                        $api_key = UserDetail::where('user_id', 6)->pluck('api_key');
                                        $url = "127.0.0.1/api/v1/send?api_key=$api_key[0]&contacts=$number&senderid=$sender&msg=$sms_text";

                                        $client = new Client();
                                        $res = $client->request('GET', $url);
                                        $ret = $res->getBody();

                                        $error = new ErrorNotification();
                                        $error->user_id = $gpSmsOfThisCampaigns[0]->user_id;
                                        $error->sender_id = $gpSmsOfThisCampaigns[0]->sender_id;
                                        $error->operator_id = 3; // GP operator ID
                                        $error->campaign_id = $gpSmsOfThisCampaigns[0]->campaign_id;
                                        $error->error_message = $status_code[1];
                                        $error->save();
                                    } catch (\Exception $e) {
                                        dump('tut tut tut' . $e);
                                    }
                                }

                                $returnError['gpError'] = "something went wrong to send gp sms. Status Code : ".$status_code[0]." Sender ID : ".$sender;

                            }else{
                                foreach ($response_array as $oneReport){
                                    $report_details = explode(',', $oneReport);
                                    // $report_details[0] = number, $report_details[1] = status, $report_details[2] = message ID,

                                    $aaa = isset($report_details[2])?@$report_details[2]:@$report_details[1];
                                    if($aaa == "TPS limit Exceeded")
                                        continue;

                                    if (isset($report_details[1])) {
                                            $gpSmsDetail = SmsCamPending::where('id', $gpTransferred[$countTSms])->first();
                                            $gpTransferredForDelete[] = $gpSmsDetail->id;

                                            $gpDataForInsert[] = array(
                                                'user_id' => $gpSmsDetail->user_id,
                                                'sender_id' => $gpSmsDetail->sender_id,
                                                'campaign_id' => $gpSmsDetail->campaign_id,
                                                'sct_cell_no' => $gpSmsDetail->scp_cell_no,
                                                'sct_message' => $gpSmsDetail->scp_message,
                                                'sct_sms_cost' => $gpSmsDetail->scp_sms_cost,
                                                'operator_id' => $gpSmsDetail->operator_id,
                                                'sct_campaign_type' => $gpSmsDetail->scp_campaign_type,
                                                'sct_deal_type' => $gpSmsDetail->scp_deal_type,
                                                'sct_sms_type' => $gpSmsDetail->scp_sms_type,
                                                'sct_sms_id' => isset($report_details[2])?@$report_details[2]:@$report_details[1],
                                                'sct_sms_text_type' => $gpSmsDetail->scp_sms_text_type,
                                                'sct_target_time' => $gpSmsDetail->scp_target_time,
                                                'created_at' => $gpSmsDetail->created_at,
                                                'updated_at' => $gpSmsDetail->updated_at,
                                                'sct_delivery_report' => 'PENDING',
                                                'sct_status' => isset($report_details[2])?@$report_details[1]:@$report_details[0],
                                            );
                                    }
                                    $countTSms++;
                                }
                                try {
                                    SmsCampaign_24h::insert($gpDataForInsert);
                                    $gpDataForInsert = array();

                                    SmsCamPending::whereIn('id', $gpTransferredForDelete)->delete();
                                    $returnData['gp'] = "GP is Working...";
                                } catch (\Exception $e) {
                                    $returnError['gpError'] = "something went wrong" . $e->getMessage();
                                }
                            }

                            /* ----------------------------------- */
                            /* ----------------------------------- */
                            /* ----------------------------------- */
                            /*Editing End*/
                        }
                    }catch (\Exception $e) {
                        $returnError['gpError'] = "something went wrong" . $e->getMessage();
                    }
                    /*
                     * ---------------
                     * end send gp sms
                     * ---------------
                     * */


                    /*
                     * -----------------
                     * start send bl sms
                     * -----------------
                     * */
                    $blSmsOfThisCampaigns = SmsCamPending::where([
                        'campaign_id' => $maskingSmsCampaign->campaign_id,
                        'scp_campaign_status' => 1,
                        'scp_message' => $maskingSmsCampaign->scp_message,
                        'sender_id' => $maskingSmsCampaign->sender_id,
                        'operator_id' => '2'])
                        ->orderBy('id', 'desc')
                        ->take($limitSms)
                        ->get();/*bl = 2*/
                    if (count($blSmsOfThisCampaigns) > 0) {
                        foreach ($blSmsOfThisCampaigns as $blSmsDetails) {
                            $blNumbers[] = $blSmsDetails->scp_cell_no;
                            $blTransferred[] = $blSmsDetails->id;
                        }

                        $countTSms = 0;
                        $xml_response = \SmsHelper::send_masking_banglalink_sms($virtualNumber->banglalink_virtual_number->sivn_api_user_name, $virtualNumber->banglalink_virtual_number->sivn_api_password, $maskingSmsCampaign->scp_message, $blNumbers, $maskingSmsCampaign->sender->sir_sender_id);
                        if ($xml_response == '0150') {
                            $returnError['blError'] = "something was missing for banglalink masking sms";
                        } elseif ($xml_response == 'bl_error') {
                            $returnError['blError'] = "something went wrong to send banglalink masking sms";
                        } else {
                            $blDataForInsert = array();
                            foreach ($blNumbers as $blNumber) {

                                $checkedSms = SmsCamPending::where('id', $blTransferred[$countTSms])->first();
                                $blDataForInsert[] = array(
                                    'user_id' => $checkedSms->user_id,
                                    'sender_id' => $checkedSms->sender_id,
                                    'campaign_id' => $checkedSms->campaign_id,
                                    'sct_cell_no' => $checkedSms->scp_cell_no,
                                    'sct_message' => $checkedSms->scp_message,
                                    'sct_sms_cost' => $checkedSms->scp_sms_cost,
                                    'operator_id' => $checkedSms->operator_id,
                                    'sct_campaign_type' => $checkedSms->scp_campaign_type,
                                    'sct_deal_type' => $checkedSms->scp_deal_type,
                                    'sct_sms_type' => $checkedSms->scp_sms_type,
                                    'sct_sms_id' => '0',
                                    'sct_sms_text_type' => $checkedSms->scp_sms_text_type,
                                    'sct_target_time' => $checkedSms->scp_target_time,
                                    'created_at' => $checkedSms->created_at,
                                    'updated_at' => $checkedSms->updated_at,
                                    'sct_delivery_report' => 'PENDING',
                                    'sct_status' => '0',
                                );

                                $countTSms++;
                            }
                            try {
                                SmsCampaign_24h::insert($blDataForInsert);
                                $blDataForInsert = array();

                                SmsCamPending::whereIn('id', $blTransferred)->delete();

                                $returnData['banglalink'] = "Banglalink Working...";

                            } catch (\Exception $e) {
                                $returnError['blError'] = "something went wrong" . $e->getMessage();
                            }

                        }
                    }
                    /*
                     * ---------------
                     * end send bl sms
                     * ---------------
                     * */


                    /*
                     * --------------------------
                     * start send robi airtel sms
                     * --------------------------
                     * */
                    $robiAirtelSmsOfThisCampaigns = SmsCamPending::where([
                        'campaign_id' => $maskingSmsCampaign->campaign_id,
                        'scp_message' => $maskingSmsCampaign->scp_message,
                        'scp_campaign_status' => 1,
                        'sender_id' => $maskingSmsCampaign->sender_id])
                        ->whereIn('operator_id', ['1', '4'])
                        ->orderBy('id', 'desc')
                        ->take($limitSms)
                        ->get();/*airtel = 1, robi = 4*/

                    if (count($robiAirtelSmsOfThisCampaigns) > 0) {
                        foreach ($robiAirtelSmsOfThisCampaigns as $raSmsDetails) {
                            $raNumbers[] = $raSmsDetails->scp_cell_no;
                            $raTransferred[] = $raSmsDetails->id;
                        }

                        $countTSms = 0;
                        $xml_response = \SmsHelper::send_masking_mobireach_sms($virtualNumber->robi_virtual_number->sivn_api_user_name, $virtualNumber->robi_virtual_number->sivn_api_password, $maskingSmsCampaign->scp_message, $raNumbers, $maskingSmsCampaign->sender->sir_sender_id);

                        if ($xml_response == '0150') {
                                $exists = ErrorNotification::where('user_id', $raSmsDetails->user_id)
                                    ->where('operator_id', 4)
                                    ->where('created_at', '>', Carbon::now()->subHours(6) )
                                    ->first();

                                if ( !$exists ){
                                    try{
                                        $error_msg = "Robi masking sms is not sending.";
                                        $sms_text = "Error Message ! \nUser ID : ".$raSmsDetails->user_id.".\nSender ID : ".$raSmsDetails->sender->sir_sender_id.".\nCampaign ID: ".$raSmsDetails->campaign_id.".\nMessage : ".$error_msg;
                                        $sms_text = urlencode($sms_text);
                                        $number = "01823037726";
                                        $sender = "8801844532630";
                                        $api_key = UserDetail::where('user_id', 6)->pluck('api_key');
                                        $url = "http://sms.iglweb.com/api/v1/send?api_key=$api_key[0]&contacts=$number&senderid=$sender&msg=$sms_text";
                                        $client = new Client();
                                        $res = $client->request('GET', $url);
                                        $ret = $res->getBody();

                                        $error = new ErrorNotification();
                                        $error->user_id = $raSmsDetails->user_id;
                                        $error->sender_id = $raSmsDetails->sender_id;
                                        $error->operator_id = 4;
                                        $error->campaign_id = $raSmsDetails->campaign_id;
                                        $error->error_message = "Robi masking sms is not sending.";
                                        $error->save();
                                    }catch(\Exception $e){
                                        dump('tut tut tut'.$e);
                                    }
                                }
                            $returnError['robiAirtelError'] = "something was missing for robi masking sms";
                        } elseif ($xml_response == '0160') {
                            $exists = ErrorNotification::where('user_id', $raSmsDetails->user_id)
                                ->where('operator_id', 4)
                                ->where('created_at', '>', Carbon::now()->subHours(6) )
                                ->first();

                            if ( !$exists ){
                                try{
                                    $error_msg = "Robi masking sms is not sending.";
                                    $sms_text = "Error Message ! \nUser ID : ".$raSmsDetails->user_id.".\nSender ID : ".$raSmsDetails->sender->sir_sender_id.".\nCampaign ID: ".$raSmsDetails->campaign_id.".\nMessage : ".$error_msg;
                                    $sms_text = urlencode($sms_text);
                                    $number = "01823037726";
                                    $sender = "8801844532630";
                                    $api_key = UserDetail::where('user_id', 6)->pluck('api_key');
                                    $url = "http://sms.iglweb.com/api/v1/send?api_key=$api_key[0]&contacts=$number&senderid=$sender&msg=$sms_text";
                                    $client = new Client();
                                    $res = $client->request('GET', $url);
                                    $ret = $res->getBody();

                                    $error = new ErrorNotification();
                                    $error->user_id = $raSmsDetails->user_id;
                                    $error->sender_id = $raSmsDetails->sender_id;
                                    $error->operator_id = 4;
                                    $error->campaign_id = $raSmsDetails->campaign_id;
                                    $error->error_message = "Robi masking sms is not sending.";
                                    $error->save();
                                }catch(\Exception $e){
                                    dump('tut tut tut'.$e);
                                }
                            }
                            $returnError['robiAirtelError'] = "something went wrong to call robi masking api";
                        } else {
                            $raDataForInsert = array();
                            foreach ($xml_response as $smsReport) {

                                $checkedSms = SmsCamPending::where('id', $raTransferred[$countTSms])->first();
                                $raDataForInsert[] = array(
                                    'user_id' => $checkedSms->user_id,
                                    'sender_id' => $checkedSms->sender_id,
                                    'campaign_id' => $checkedSms->campaign_id,
                                    'sct_cell_no' => $checkedSms->scp_cell_no,
                                    'sct_message' => $checkedSms->scp_message,
                                    'sct_sms_cost' => $checkedSms->scp_sms_cost,
                                    'operator_id' => $checkedSms->operator_id,
                                    'sct_campaign_type' => $checkedSms->scp_campaign_type,
                                    'sct_deal_type' => $checkedSms->scp_deal_type,
                                    'sct_sms_type' => $checkedSms->scp_sms_type,
                                    'sct_sms_id' => $smsReport->MessageId,
                                    'sct_sms_text_type' => $checkedSms->scp_sms_text_type,
                                    'sct_target_time' => $checkedSms->scp_target_time,
                                    'created_at' => $checkedSms->created_at,
                                    'updated_at' => $checkedSms->updated_at,
                                    'sct_delivery_report' => 'PENDING',
                                    'sct_status' => $smsReport->Status,
                                );

                                $countTSms++;
                            }
                            try {
                                SmsCampaign_24h::insert($raDataForInsert);
                                $raDataForInsert = array();

                                SmsCamPending::whereIn('id', $raTransferred)->delete();
                                $returnData['robi_airtel'] = "Robi Airtel Working...";
                            } catch (\Exception $e) {
                                $returnError['robiAirtelError'] = "something went wrong" . $e->getMessage();
                            }

                        }
                    }
                    /*
                     * ------------------------
                     * end send robi airtel sms
                     * ------------------------
                     * */


                    /*
                     * -----------------------
                     * start send teletalk sms
                     * -----------------------
                     * */
                    $teletalkSmsOfThisCampaigns = SmsCamPending::where([
                        'campaign_id' => $maskingSmsCampaign->campaign_id,
                        'scp_message' => $maskingSmsCampaign->scp_message,
                        'scp_campaign_status' => 1,
                        'sender_id' => $maskingSmsCampaign->sender_id,
                        'operator_id' => '5'])
                        ->orderBy('id', 'desc')
                        ->take($limitSms)
                        ->get();/*bl = 5*/


                    if (count($teletalkSmsOfThisCampaigns) > 0) {
                        foreach ($teletalkSmsOfThisCampaigns as $ttSmsDetails) {
                            $ttSmsDetails->scp_cell_no;
                            $ttTransferred[] = $ttSmsDetails->id;
                            $countTSms = 0;

                            $senderIdDetails = SenderIdRegister::find($ttSmsDetails->sender_id);

                            $xml_response = \SmsHelper::send_masking_teletalk_sms(
                                $senderIdDetails->sir_teletalk_user_name,
                                $senderIdDetails->sir_teletalk_user_password,
                                $maskingSmsCampaign->scp_message,
                                $ttSmsDetails->scp_cell_no,
                                $maskingSmsCampaign->sender->sir_sender_id);

                            if ($xml_response == '0150') {
                                $returnError['ttError'] = "something was missing for teletalk masking sms";

                                $sms_ret_id = "";
                                $sms_status = "SOMETHING MISSING";
                                $sms_report = "PENDING";

                                $ttDataForInsert = array();
                                // $checkedSms = SmsCamPending::where('id', $ttTransferred[$countTSms])->first();
                                $ttDataForInsert[] = array(
                                    'user_id' => $ttSmsDetails->user_id,
                                    'sender_id' => $ttSmsDetails->sender_id,
                                    'campaign_id' => $ttSmsDetails->campaign_id,
                                    'sct_cell_no' => $ttSmsDetails->scp_cell_no,
                                    'sct_message' => $ttSmsDetails->scp_message,
                                    'sct_sms_cost' => $ttSmsDetails->scp_sms_cost,
                                    'operator_id' => $ttSmsDetails->operator_id,
                                    'sct_campaign_type' => $ttSmsDetails->scp_campaign_type,
                                    'sct_deal_type' => $ttSmsDetails->scp_deal_type,
                                    'sct_sms_type' => $ttSmsDetails->scp_sms_type,
                                    'sct_sms_id' => $sms_ret_id,
                                    'sct_sms_text_type' => $ttSmsDetails->scp_sms_text_type,
                                    'sct_target_time' => $ttSmsDetails->scp_target_time,
                                    'created_at' => $ttSmsDetails->created_at,
                                    'updated_at' => $ttSmsDetails->updated_at,
                                    'sct_delivery_report' => $sms_report,
                                    'sct_status' => $sms_status
                                );

                                $countTSms++;

                                try {
                                    SmsCampaign_24h::insert($ttDataForInsert);
                                    $ttDataForInsert = array();

                                    SmsCamPending::whereIn('id', $ttTransferred)->delete();
                                    $returnData['teletalk'] = "Teletalk Working...";
                                } catch (\Exception $e) {
                                    $returnError['ttError'] = "something went wrong" . $e->getMessage();
                                }

                            } elseif ($xml_response == '0160') {
                                $returnError['ttError'] = "something went wrong to send teletalk number";
                            } else {


                                preg_match_all('/>(.*?)</', $xml_response, $matches);
                                $full_ret_text = $matches[1][0];
                                $exp_ret_text = explode(',', $full_ret_text);

                                if ($exp_ret_text[0] == "SUCCESS") {
                                    $exp_sms_id = explode('=', $exp_ret_text[1]);
                                    $sms_ret_id = $exp_sms_id[1];
                                    $sms_status = "SUCCESS";
                                    $sms_report = "PENDING";
                                } else {
                                    $sms_ret_id = 0;
                                    $sms_status = $exp_ret_text[0];
                                    $sms_report = $exp_ret_text[1];
                                }

                                $ttDataForInsert = array();
                                // $checkedSms = SmsCamPending::where('id', $ttTransferred[$countTSms])->first();
                                $ttDataForInsert[] = array(
                                    'user_id' => $ttSmsDetails->user_id,
                                    'sender_id' => $ttSmsDetails->sender_id,
                                    'campaign_id' => $ttSmsDetails->campaign_id,
                                    'sct_cell_no' => $ttSmsDetails->scp_cell_no,
                                    'sct_message' => $ttSmsDetails->scp_message,
                                    'sct_sms_cost' => $ttSmsDetails->scp_sms_cost,
                                    'operator_id' => $ttSmsDetails->operator_id,
                                    'sct_campaign_type' => $ttSmsDetails->scp_campaign_type,
                                    'sct_deal_type' => $ttSmsDetails->scp_deal_type,
                                    'sct_sms_type' => $ttSmsDetails->scp_sms_type,
                                    'sct_sms_id' => $sms_ret_id,
                                    'sct_sms_text_type' => $ttSmsDetails->scp_sms_text_type,
                                    'sct_target_time' => $ttSmsDetails->scp_target_time,
                                    'created_at' => $ttSmsDetails->created_at,
                                    'updated_at' => $ttSmsDetails->updated_at,
                                    'sct_delivery_report' => $sms_report,
                                    'sct_status' => $sms_status
                                );

                                $countTSms++;

                                try {
                                    SmsCampaign_24h::insert($ttDataForInsert);
                                    $ttDataForInsert = array();

                                    SmsCamPending::whereIn('id', $ttTransferred)->delete();
                                    $returnData['teletalk'] = "Teletalk Working...";
                                } catch (\Exception $e) {
                                    $returnError['ttError'] = "something went wrong" . $e->getMessage();
                                }

                            }
                        }
                    }
                    /*
                     * ---------------------
                     * end send teletalk sms
                     * ---------------------
                     * */


                    /*var_dump($gpNumbers);
                    echo "<br><br><br>";
                    var_dump($blNumbers);
                    echo "<br><br><br>";
                    var_dump($raNumbers);
                    echo "<br><br><br>";
                    var_dump($ttNumbers);
                    echo "<br><br><br>";
                    var_dump($gpTransferred);
                    echo "<br><br><br>";
                    var_dump($blTransferred);
                    echo "<br><br><br>";
                    var_dump($raTransferred);
                    echo "<br><br><br>";
                    var_dump($ttTransferred);
                    echo "<br><br><br>";*/
                }

            } else {
                $returnData['message'] = "no sms found";
                return view('cron.masking', compact('returnData'));
            }
        }
        return view('cron.masking', compact('returnData', 'returnError'));
    }


    /*get non masking sms delivery report cron*/
    public function nonMaskingDeliveryReport()
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


            /* For Robi sender id */
            $robi = SenderIdRegister::select('id')->where('sir_sender_id', 'LIKE', "88018%")->get();
            $robi_ids = array();
            foreach ($robi as $value) {
                $robi_ids[] = $value->id;
            }

            $pendingNumbersRobi = SmsCampaign_24h::where(['sct_sms_type' => '1', 'sct_delivery_report' => 'PENDING'])
                ->whereIn('sender_id', $robi_ids)
                ->update(['sct_delivery_report' => 'DELIVERED']);


            /* Dtermine SMS is sended from robi sender id */
            $rankSteel = '88044';
            $not_robi = SenderIdRegister::select('id')->where('sir_sender_id', 'LIKE', "%$rankSteel%")->get();

            $non_robi_ids = array();
            foreach ($not_robi as $value) {
                $non_robi_ids[] = $value->id;
            }

            /*get undelivered numbers*/
            $pendingNumbers = SmsCampaign_24h::select('sct_sms_id')->where(['sct_sms_type' => '1', 'sct_delivery_report' => 'PENDING'])->whereIn('sender_id', $non_robi_ids)->skip($offsetData)->take(50)->get();


            $undeliveredNumber = Null;
            if (count($pendingNumbers) < 50) {
                $_SESSION['goToNullOffset'] = 1;
            }
            if (count($pendingNumbers) > 0) {

                $numberOfRows = count($pendingNumbers);

                foreach ($pendingNumbers as $pendingNumber) {
                    if (!empty($pendingNumber['sct_sms_id'])) {
                        if ($undeliveredNumber == null) {
                            $undeliveredNumber = $pendingNumber['sct_sms_id'];
                        } else {
                            $undeliveredNumber = $undeliveredNumber . "," . $pendingNumber['sct_sms_id'];
                        }
                    }
                }


                $jsonDeliveryReport = \SmsHelper::getNonMaskingDeliveryReport($undeliveredNumber);
                if ($jsonDeliveryReport == '0150') {
                    $returnData['no_number'] = "something went wrong";
                } elseif ($jsonDeliveryReport == '0160') {
                    $returnData['no_number'] = "something went wrong";
                } else {
                    $delivryReport = json_decode($jsonDeliveryReport);

                    $countCheckNumber = count($delivryReport->results);

                    $_SESSION['offsetData'] = $_SESSION['offsetData'] + ($numberOfRows - $countCheckNumber);

                    for ($i = 0; $i < $countCheckNumber; $i++) {
                        if ($delivryReport->results[$i]->status->groupName != "PENDING") {
                            $smsId = $delivryReport->results[$i]->messageId;
                            $report = $delivryReport->results[$i]->status->groupName;
                            $updReport = SmsCampaign_24h::where('sct_sms_id', $smsId)->first();
                            if ($updReport) {
                                $updReport->sct_delivery_report = $report;
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

        try {
            DB::transaction(function () {
                $moveDatasFromToday = SmsCampaign_24h::where('sct_target_time', '<=', Carbon::now()->subHours(24))->get();
                foreach ($moveDatasFromToday as $moveData) {
                    SmsCampaign::create([
                        'user_id' => $moveData->user_id,
                        'sender_id' => $moveData->sender_id,
                        'campaign_id' => $moveData->campaign_id,
                        'sc_cell_no' => $moveData->sct_cell_no,
                        'sc_message' => $moveData->sct_message,
                        'sc_sms_cost' => $moveData->sct_sms_cost,
                        'operator_id' => $moveData->operator_id,
                        'sc_campaign_type' => $moveData->sct_campaign_type,
                        'sc_deal_type' => $moveData->sct_deal_type,
                        'sc_sms_type' => $moveData->sct_sms_type,
                        'sc_sms_id' => $moveData->sct_sms_id,
                        'sc_sms_text_type' => $moveData->sct_sms_text_type,
                        'sc_submitted_time' => $moveData->created_at,
                        'sc_targeted_time' => $moveData->sct_target_time,
                        'sc_delivery_report' => $moveData->sct_delivery_report,
                        'sc_status' => $moveData->sct_status,
                    ]);
                }

                SmsCampaign_24h::where('sct_target_time', '<=', Carbon::now()->subHours(24))->delete();

            });
        } catch (\Exception $e) {

        }

        return view('cron.non-masking-delivery-report', compact('returnData'));

    }


    /*get non masking sms delivery report cron*/
    public function gpDeliveryReport()
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

        // for ($j = 0; $j < 10; $j++) {

        //     /*set offsetData variable based on session offsetData & goToNullOffset*/
        //     if ($_SESSION['goToNullOffset'] == 0) {
        //         $offsetData = $_SESSION['offsetData'];

        //     } else {
        //         $offsetData = 0;
        //         $_SESSION['offsetData'] = 0;
        //         $_SESSION['goToNullOffset'] = 0;
        //     }

        //     /*get undelivered numbers*/
        //     $pendingNumbers = SmsCampaign_24h::select('id', 'sender_id', 'sct_sms_id', 'sct_sms_text_type')->where(['sct_sms_type' => '2', 'operator_id' => '3', 'sct_delivery_report' => 'PENDING'])->skip($offsetData)->take(50)->get();
        //     $undeliveredNumber = Null;
        //     if (count($pendingNumbers) < 50) {
        //         $_SESSION['goToNullOffset'] = 1;
        //     }
        //     if (count($pendingNumbers) > 0) {

        //         foreach ($pendingNumbers as $pendingNumber) {
        //             try{

        //                 if (!empty($pendingNumber['sct_sms_id'])) {
        //                     if ($pendingNumber['sct_sms_text_type'] == 'text') {
        //                         $messageType = '1';
        //                     } else {
        //                         $messageType = '2';
        //                     }
        //                     $userName = $pendingNumber->sender->gp_virtual_number->sivn_api_user_name;
        //                     $password = $pendingNumber->sender->gp_virtual_number->sivn_api_password;
        //                     $cli = $pendingNumber->sender->sir_sender_id;

        //                     $url = "https://gpcmp.grameenphone.com/gpcmpapi/messageplatform/controller.home?username=" . $userName . "&password=" . $password . "&apicode=4&msisdn=01700000000&countrycode=0&cli=" . $cli . "&messagetype=" . $messageType . "&message=0&messageid=" . $pendingNumber['sct_sms_id'];
        //                     if($pendingNumber['sct_sms_id'] == '20190328-5453-553762327068-01708403276-02')
        //                         dd($url);

        //                     $client = new Client();

        //                     $res = $client->request('GET', $url, ['verify' => false]);
        //                     $ret = $res->getBody()->getContents();

        //                     $explodeRet = explode(',', $ret);
        //                     if ($explodeRet[0] == '200') {

        //                         $explodeText = explode('#', $explodeRet[1]);
        //                         $retText = strtoupper($explodeText[1]);
        //                         try {
        //                             SmsCampaign_24h::where('id', $pendingNumber['id'])->update(['sct_delivery_report' => $retText]);
        //                             $returnData['success'] = 'working...';
        //                         } catch (\Exception $e) {
        //                             $returnData['error'] = "something went wrong.";
        //                         }

        //                     } else {
        //                         $_SESSION['offsetData']++;
        //                     }
        //                 }

        //             } catch(\Exception $e) {
        //                 $returnData['error'] = "something went wrong.".$e->getMessage;
        //             }
        //         }


        //     } else {
        //         $returnData['no_number'] = "no number available for check report";
        //     }

        //     if ($_SESSION['goToNullOffset'] == 1) {
        //         break;
        //     }

        // }

        // SmsCampaign_24h::where('sct_sms_type', '2')->where('operator_id', '!=', '3')->where('sct_delivery_report', 'PENDING')->update(['sct_delivery_report' => 'DELIVERED']);
        SmsCampaign_24h::where(['sct_sms_type' => '2', 'sct_delivery_report' => 'PENDING'])->update(['sct_delivery_report' => 'DELIVERED']);

        $returnData['still_pending'] = $_SESSION['offsetData'];
        $returnData['check_complete'] = $_SESSION['goToNullOffset'];


        return view('cron.masking-delivery-report', compact('returnData'));

    }


    public function sendMaskingNonMaskingSms()
    {

        $returnError['gpError'] = "";
        $returnData['gp'] = "";
        $returnError['blError'] = "";
        $returnData['banglalink'] = "";
        $returnError['robiAirtelError'] = "";
        $returnData['robi_airtel'] = "";
        $returnData['teletalk'] = "";
        $returnError['ttError'] = "";
        $returnData['message'] = "";
        $returnError['errorNotify'] = "";

        $non_masking_return_text = "";
        $retTextRobi = "no sms found";
        $retTextRankstel = "no sms found";
        $retTextGp = "no sms found";
        $retTextBl = "no sms found";


        $getNonMaskingSmsCampaigns = SmsCamPending::where('scp_sms_type', '1')
            ->where('scp_target_time', '<=', Carbon::now())
            ->where('scp_campaign_status', 1)
            ->groupBy('sender_id')
            ->groupBy('scp_message')
            ->take(10)
            ->orderBy('id', 'asc')
            ->get();


        if (count($getNonMaskingSmsCampaigns) > 0) {
            $smsLoop = 1;
            foreach ($getNonMaskingSmsCampaigns as $nonMaskingSmsCampaign) {

                $limitSms = 100;
                $sms = array();
                $transferredSmsId = array();
                $getSms50OfSameCampaignIds = SmsCamPending::where([
                    'campaign_id' => $nonMaskingSmsCampaign->campaign_id,
                    'scp_campaign_status' => 1,
                    'scp_message' => $nonMaskingSmsCampaign->scp_message,
                    'sender_id' => $nonMaskingSmsCampaign->sender_id])
                    ->take($limitSms)
                    ->get();
                    // dd($getSms50OfSameCampaignIds);
                $numbers = array();
                foreach ($getSms50OfSameCampaignIds as $sms50Details) {
                    $numbers[] = $sms50Details->scp_cell_no;
                    $transferredSmsId[] = $sms50Details->id;
                }
                // dd($numbers);
                $countTSms = 0;
                $sender_id = $nonMaskingSmsCampaign->sender->sir_sender_id;

                if (substr($sender_id, 0, 5) == '88018') {

                    $senderIdDet = SenderIdRegister::with('robi_virtual_number')->where('sir_sender_id', $sender_id)->first();
                    if (empty($senderIdDet)) {
                        $retText = "Something was missing1";
                        continue;
                    }
                    //Robi@!445932

                    $xml_response = \SmsHelper::send_masking_mobireach_sms('iglweb_longcode', 'Dhaka@2020', $nonMaskingSmsCampaign->scp_message, $numbers, $sender_id);
                    // dd($xml_response);
                    if ($xml_response == '0150') {
                        $retText = "Something was missing";
                    } elseif ($xml_response == '0160') {
                        // $retText = "Something Went Wrong to call robi non-masking api";
                        $retText = "Something Went Wrong to call robi non-masking api";
                    } else {
                        $dataForInsert = array();
                        // dd($xml_response);
                        foreach ($xml_response as $smsReport) {
                            $checkedSms = SmsCamPending::where('id', $transferredSmsId[$countTSms])->first();
                            $dataForInsert[] = array(
                                'user_id' => $checkedSms->user_id,
                                'sender_id' => $checkedSms->sender_id,
                                'campaign_id' => $checkedSms->campaign_id,
                                'sct_cell_no' => $checkedSms->scp_cell_no,
                                'sct_message' => $checkedSms->scp_message,
                                'sct_sms_cost' => $checkedSms->scp_sms_cost,
                                'operator_id' => $checkedSms->operator_id,
                                'sct_campaign_type' => $checkedSms->scp_campaign_type,
                                'sct_deal_type' => $checkedSms->scp_deal_type,
                                'sct_sms_type' => $checkedSms->scp_sms_type,
                                'sct_sms_id' => $smsReport->MessageId,
                                'sct_sms_text_type' => $checkedSms->scp_sms_text_type,
                                'sct_target_time' => $checkedSms->scp_target_time,
                                'created_at' => $checkedSms->created_at,
                                'updated_at' => $checkedSms->updated_at,
                                'sct_delivery_report' => 'PENDING',
                                'sct_status' => $smsReport->Status,
                            );

                            $countTSms++;
                        }
                        try {
                            SmsCampaign_24h::insert($dataForInsert);
                            $dataForInsert = array();

                            SmsCamPending::whereIn('id', $transferredSmsId)->delete();
                            $retText = "Working..." . $smsLoop++;
                        } catch (\Exception $e) {
                            $retText = "something went wrong";
//                            return view('cron.non-masking', compact('retText'));
                        }

                    }

                    $retTextRobi = $retText;

                } elseif ((substr($sender_id, 0, 5) == '88017') || (substr($sender_id, 0, 5) == '88013')) {

                    $senderIdDet = SenderIdRegister::with('gp_virtual_number')->where('sir_sender_id', $sender_id)->first();
                    if (empty($senderIdDet)) {
                        $retTextGp = "Something was missing1";
                        continue;
                    }


                    $user_name = "IGLWAdmin";
                    $password = "qazXSW11!!";
                    $xml_response = \SmsHelper::update_send_masking_gp_sms($user_name, $password, $sender_id, $nonMaskingSmsCampaign->scp_message, $numbers);
                    $gpDataForInsert = array();



                    $response_array = explode("\n", $xml_response);
                    $status_code = explode(',', $response_array[0]);

                    // var_dump($user_name);
                    // var_dump($password);
                    // var_dump($sender);
                    // var_dump($status_code);
                    // Trying to send from virtual number in the case of The CLI invalid
                    if ( $status_code[0] == 208 ){

                        $retTextGp = "Sender id doesn't registered. Sender ID : <b>".$sender_id."</b>";

                    } elseif ( $status_code[0] == 201 || $status_code[0] == 216 || $status_code[0] == 203 || $status_code[0] == 203 || $status_code[0] == 204 || $status_code[0] == 231  || $status_code[1] == 231 ){

                        if ($status_code[0] == 216) {
                            $check_v_n = $sender_id;
                            // dd($check_v_n);
                            $exists_load = SenderIdVirtualNumber::where('sivn_number', $check_v_n)
                                ->where(function ($query) {
                                    $query->where('last_load_time' , '>', Carbon::now()->subMinutes(20) );

                                })
                                ->first();
                            // dd($exists_load);
                            if ( !$exists_load ) {
                                $load = SenderIdVirtualNumber::where('sivn_number', $check_v_n)
                                    ->first();
                                if (!empty($load)) {
                                    if ($load->sivn_load_amount >= 10) {
                                        try {
                                            $load_number = $check_v_n;

                                            $flexi_api_key = "445015812203613491581220361";
                                            $pin = "3799";
                                            $amount = $load->sivn_load_amount;
                                            $number_type = "2";
                                            $operator = "gp";
                                            $url = "http://127.0.0.1/api/v1/send-load?api_key=$flexi_api_key&pin=$pin&number=$load_number&amount=$amount&number_type=$number_type&operator=$operator";

                                            $client = new Client();
                                            $res = $client->request('GET', $url);
                                            $ret = $res->getBody();

                                            $load->last_load_time = Carbon::now();
                                            $load->save();
                                            dump("laod requested");
                                        } catch (\Exception $e) {
                                            dump('Auto Load Problem. ' . $e->getMessage());
                                        }
                                    }
                                }
                            }
                        }


                        $retTextGp = "something went wrong to send gp sms. Status Code : ".$status_code[0]." Sender ID : ".$sender_id;

                    }else{
                        foreach ($response_array as $oneReport){
                            $report_details = explode(',', $oneReport);
                            // $report_details[0] = number, $report_details[1] = status, $report_details[2] = message ID,

                            $aaa = isset($report_details[2])?@$report_details[2]:@$report_details[1];
                            if($aaa == "TPS limit Exceeded")
                                continue;

                            if (isset($report_details[1])) {
                                $gpSmsDetail = SmsCamPending::where('id', $transferredSmsId[$countTSms])->first();
                                $gpTransferredForDelete[] = $gpSmsDetail->id;

                                $gpDataForInsert[] = array(
                                    'user_id' => $gpSmsDetail->user_id,
                                    'sender_id' => $gpSmsDetail->sender_id,
                                    'campaign_id' => $gpSmsDetail->campaign_id,
                                    'sct_cell_no' => $gpSmsDetail->scp_cell_no,
                                    'sct_message' => $gpSmsDetail->scp_message,
                                    'sct_sms_cost' => $gpSmsDetail->scp_sms_cost,
                                    'operator_id' => $gpSmsDetail->operator_id,
                                    'sct_campaign_type' => $gpSmsDetail->scp_campaign_type,
                                    'sct_deal_type' => $gpSmsDetail->scp_deal_type,
                                    'sct_sms_type' => $gpSmsDetail->scp_sms_type,
                                    'sct_sms_id' => isset($report_details[2])?@$report_details[2]:@$report_details[1],
                                    'sct_sms_text_type' => $gpSmsDetail->scp_sms_text_type,
                                    'sct_target_time' => $gpSmsDetail->scp_target_time,
                                    'created_at' => $gpSmsDetail->created_at,
                                    'updated_at' => $gpSmsDetail->updated_at,
                                    'sct_delivery_report' => 'PENDING',
                                    'sct_status' => isset($report_details[2])?@$report_details[1]:@$report_details[0],
                                );
                            }
                            $countTSms++;
                        }
                        try {
                            SmsCampaign_24h::insert($gpDataForInsert);
                            $gpDataForInsert = array();

                            SmsCamPending::whereIn('id', $gpTransferredForDelete)->delete();
                            $retTextGp = "GP Non-Masking is Working...";
                        } catch (\Exception $e) {
                            $retTextGp = "something went wrong" . $e->getMessage();
                        }
                    }
                }  elseif ((substr($sender_id, 0, 5) == '88019') || (substr($sender_id, 0, 5) == '88014')) {


                    $senderIdDet = SenderIdRegister::with('banglalink_virtual_number')->where('sir_sender_id', $sender_id)->first();
                    if (empty($senderIdDet)) {
                        $retTextBl = "Something was missing1";
                        continue;
                    }


                    $user_name = "igl";
                    $password = "397c6678b1ca033edbeba622573ed000";
                    $xml_response = \SmsHelper::send_masking_banglalink_sms($user_name, $password, $nonMaskingSmsCampaign->scp_message, $numbers, $sender_id);
                    if ($xml_response == '0150') {
                        $retTextBl = "something was missing for banglalink masking sms";
                    } elseif ($xml_response == 'bl_error') {
                        $retTextBl = "something went wrong to send banglalink masking sms";
                    } else {
                        $blDataForInsert = array();
                        foreach ($numbers as $blNumber) {

                            $checkedSms = SmsCamPending::where('id', $transferredSmsId[$countTSms])->first();
                            $blTransferred[] = $checkedSms->id;


                            $blDataForInsert[] = array(
                                'user_id' => $checkedSms->user_id,
                                'sender_id' => $checkedSms->sender_id,
                                'campaign_id' => $checkedSms->campaign_id,
                                'sct_cell_no' => $checkedSms->scp_cell_no,
                                'sct_message' => $checkedSms->scp_message,
                                'sct_sms_cost' => $checkedSms->scp_sms_cost,
                                'operator_id' => $checkedSms->operator_id,
                                'sct_campaign_type' => $checkedSms->scp_campaign_type,
                                'sct_deal_type' => $checkedSms->scp_deal_type,
                                'sct_sms_type' => $checkedSms->scp_sms_type,
                                'sct_sms_id' => '0',
                                'sct_sms_text_type' => $checkedSms->scp_sms_text_type,
                                'sct_target_time' => $checkedSms->scp_target_time,
                                'created_at' => $checkedSms->created_at,
                                'updated_at' => $checkedSms->updated_at,
                                'sct_delivery_report' => 'PENDING',
                                'sct_status' => '0',
                            );

                            $countTSms++;
                        }
                        try {
                            SmsCampaign_24h::insert($blDataForInsert);
                            $blDataForInsert = array();

                            SmsCamPending::whereIn('id', $blTransferred)->delete();

                            $retTextBl = "Banglalink Non Masking Working...";

                        } catch (\Exception $e) {
                            $retTextBl = "something went wrong" . $e->getMessage();
                        }

                    }
                } else {
                    $xml_response = \SmsHelper::send_non_masking_single_sms('IGLWEBLTD', 'igl2web1com', $nonMaskingSmsCampaign->scp_message, $numbers, $sender_id);

                    if ($xml_response == '0150') {
                        $retText = "Something was missing";
//                        return view('cron.non-masking', compact('retText'));
                    } elseif ($xml_response == '0160') {
                        $retText = "Something went wrong to call rankstel api";
//                        return view('cron.non-masking', compact('retText'));
                    } else {
                        $dataForInsert = array();

                        foreach ($xml_response as $smsReport) {

                            /*echo "message id: " . $smsReport->messageid . "<br>";
                            echo "status: " . $smsReport->status . "<br>";
                            echo "destination: " . $smsReport->destination . "<br>";*/
                            $checkedSms = SmsCamPending::where('id', $transferredSmsId[$countTSms])->first();
                            $dataForInsert[] = array(
                                'user_id' => $checkedSms->user_id,
                                'sender_id' => $checkedSms->sender_id,
                                'campaign_id' => $checkedSms->campaign_id,
                                'sct_cell_no' => $smsReport->destination,
                                'sct_message' => $checkedSms->scp_message,
                                'sct_sms_cost' => $checkedSms->scp_sms_cost,
                                'operator_id' => $checkedSms->operator_id,
                                'sct_campaign_type' => $checkedSms->scp_campaign_type,
                                'sct_deal_type' => $checkedSms->scp_deal_type,
                                'sct_sms_type' => $checkedSms->scp_sms_type,
                                'sct_sms_id' => $smsReport->messageid,
                                'sct_sms_text_type' => $checkedSms->scp_sms_text_type,
                                'sct_target_time' => $checkedSms->scp_target_time,
                                'created_at' => $checkedSms->created_at,
                                'updated_at' => $checkedSms->updated_at,
                                'sct_delivery_report' => 'PENDING',
                                'sct_status' => $smsReport->status,
                            );

                            $countTSms++;
                        }

                        try {
                            SmsCampaign_24h::insert($dataForInsert);
                            $dataForInsert = array();

                            SmsCamPending::whereIn('id', $transferredSmsId)->delete();

                            $retText = "Working..." . $smsLoop++;

                        } catch (\Exception $e) {
                            $retText = "something went wrong";
//                            return view('cron.non-masking', compact('retText'));
                        }

                    }

                    $retTextRankstel = $retText;
                }


            }
//            return view('cron.non-masking', compact('retText'));

        } else {
            $retTextRobi = "no sms found";
            $retTextGp = "no sms found";
            $retTextBl = "no sms found";
            $retTextRankstel = "no sms found";
//            return view('cron.non-masking', compact('retText'));
        }
//        $non_masking_return_text = $retText;
        /*------------------------
        --------------------------
        ---non-masking-sms-send end-----
        ----------------------------
        ----------------------------*/



        /*------------------------
        --------------------------
        ---masking-sms-send start-----
        ----------------------------
        ----------------------------*/

        for ($loopNo = 1; $loopNo <= 25; $loopNo++) {

            $getMaskingSmsCampaigns = SmsCamPending::where('scp_sms_type', '2')
                ->where('scp_target_time', '<=', Carbon::now())
                ->where('scp_campaign_status', 1)
                ->groupBy('sender_id')
                ->groupBy('scp_message')
                ->groupBy('campaign_id')
                ->orderBy('id', 'desc')
                ->take(20)
                ->get();

            // dd($getMaskingSmsCampaigns);

            if (count($getMaskingSmsCampaigns) > 0) {

                $limitSms = 50;


                foreach ($getMaskingSmsCampaigns as $maskingSmsCampaign) {
                    $gpNumbers = array();
                    $blNumbers = array();
                    $raNumbers = array();
                    $ttNumbers = array();
                    $gpTransferred = array();
                    $blTransferred = array();
                    $raTransferred = array();
                    $ttTransferred = array();
                    $virtualNumber = SenderIdRegister::with('robi_virtual_number', 'airtel_virtual_number', 'banglalink_virtual_number', 'teletalk_virtual_number', 'gp_virtual_number')
                        ->where('id', $maskingSmsCampaign->sender_id)
                        ->first();
                    // dd($virtualNumber);

                    /*
                     * -----------------
                     * start send gp sms
                     * -----------------
                     * */

                    try {
                        $gpSmsOfThisCampaigns = SmsCamPending::where([
                            'campaign_id' => $maskingSmsCampaign->campaign_id,
                            'scp_campaign_status' => 1,
                            'scp_message' => $maskingSmsCampaign->scp_message,
                            'sender_id' => $maskingSmsCampaign->sender_id,
                            'operator_id' => '3'])
                            ->orderBy('id', 'desc')
                            ->take(50)
                            ->get();/*gp = 3*/
                        // dd($gpSmsOfThisCampaigns);


                        if (count($gpSmsOfThisCampaigns) > 0) {

                            /*Editin zone start*/
                            /*-----------------------------*/
                            /*-----------------------------*/
                            /*-----------------------------*/
                            foreach ($gpSmsOfThisCampaigns as $gpSmsDetails) {

                                $gpNumbers[] = $gpSmsDetails->scp_cell_no;
                                $gpTransferred[] = $gpSmsDetails->id;

                            }

                            $countTSms = 0;

                            $user_name = $virtualNumber->gp_virtual_number->sivn_api_user_name;
                            $v_n = substr($virtualNumber->gp_virtual_number->sivn_number, 2);
                            $password = $virtualNumber->gp_virtual_number->sivn_api_password;
                            $sender = $maskingSmsCampaign->sender->sir_sender_id;
                            $sms_text = $maskingSmsCampaign->scp_message;

                            $xml_response = \SmsHelper::update_send_masking_gp_sms($user_name, $password, $sender, $sms_text, $gpNumbers);
                            $gpDataForInsert = array();



                            $response_array = explode("\n", $xml_response);
                            $status_code = explode(',', $response_array[0]);

                            // var_dump($user_name);
                            // var_dump($password);
                            // var_dump($sender);
                            // var_dump($status_code);
                            // Trying to send from virtual number in the case of The CLI invalid
                            if ( $status_code[0] == 208 ){

                                $xml_response = \SmsHelper::update_send_masking_gp_sms($user_name, $password, $v_n, $sms_text, $gpNumbers);
                                $gpDataForInsert = array();
                                $response_array = explode("\n", $xml_response);
                                $status_code = explode(',', $response_array[0]);

                            }
                            // var_dump($status_code);

                            if ( $status_code[0] == 201 || $status_code[0] == 216 || $status_code[0] == 203 || $status_code[0] == 203 || $status_code[0] == 204 || $status_code[0] == 231  || $status_code[1] == 231 ){

                                if ($status_code[0] == 216) {
                                    $check_v_n = "88".$v_n;
                                    // dd($check_v_n);
                                    $exists_load = SenderIdVirtualNumber::where('sivn_number', $check_v_n)
                                        ->where(function ($query) {
                                            $query->where('last_load_time' , '>', Carbon::now()->subMinutes(20) );

                                        })
                                        ->first();
                                    // dd($exists_load);
                                    if ( !$exists_load ) {
                                        $load = SenderIdVirtualNumber::where('sivn_number', $check_v_n)
                                            ->first();
                                        if (!empty($load)) {
                                            if ($load->sivn_load_amount >= 10) {
                                                try {
                                                    $load_number = $check_v_n;

                                                    $flexi_api_key = "445015812203613491581220361";
                                                    $pin = "3799";
                                                    $amount = $load->sivn_load_amount;
                                                    $number_type = "2";
                                                    $operator = "gp";
                                                    $url = "http://127.0.0.1/api/v1/send-load?api_key=$flexi_api_key&pin=$pin&number=$load_number&amount=$amount&number_type=$number_type&operator=$operator";

                                                    $client = new Client();
                                                    $res = $client->request('GET', $url);
                                                    $ret = $res->getBody();

                                                    $load->last_load_time = Carbon::now();
                                                    $load->save();
                                                    echo "laod requested";
                                                } catch (\Exception $e) {
                                                    echo 'Auto Load Problem. ' . $e->getMessage();
                                                }
                                            }
                                        }
                                    }
                                }

                                $exists = ErrorNotification::where('user_id', $gpSmsOfThisCampaigns[0]->user_id)
                                    ->where('operator_id', 3)
                                    ->where('created_at' , '>', Carbon::now()->subHours(6) )
                                    ->first();
                                if ( !$exists ) {
                                    try {
                                        $sender_id_name = SenderIdRegister::where('id', $gpSmsOfThisCampaigns[0]->sender_id)->pluck('sir_sender_id');
                                        $sms_text = "Error Message ! \nUser ID : ".$gpSmsOfThisCampaigns[0]->user_id.".\nSender ID : ".$sender_id_name[0].".\nCampaign ID: ".$gpSmsOfThisCampaigns[0]->campaign_id.".\nMessage : ".$status_code[1];
                                        $sms_text = urlencode($sms_text);
                                        $number = "01823037726";
                                        $sender = "8801844532630";
                                        $api_key = UserDetail::where('user_id', 6)->pluck('api_key');
                                        $url = "127.0.0.1/api/v1/send?api_key=$api_key[0]&contacts=$number&senderid=$sender&msg=$sms_text";

                                        $client = new Client();
                                        $res = $client->request('GET', $url);
                                        $ret = $res->getBody();

                                        $error = new ErrorNotification();
                                        $error->user_id = $gpSmsOfThisCampaigns[0]->user_id;
                                        $error->sender_id = $gpSmsOfThisCampaigns[0]->sender_id;
                                        $error->operator_id = 3; // GP operator ID
                                        $error->campaign_id = $gpSmsOfThisCampaigns[0]->campaign_id;
                                        $error->error_message = $status_code[1];
                                        $error->save();
                                    } catch (\Exception $e) {
//                                        $returnError['errorNotify'] = $e->getMessage();
                                    }
                                }

                                $returnError['gpError'] = "something went wrong to send gp sms. Status Code : ".$status_code[0]." Sender ID : ".$sender;

                            }else{
                                foreach ($response_array as $oneReport){
                                    $report_details = explode(',', $oneReport);
                                    // $report_details[0] = number, $report_details[1] = status, $report_details[2] = message ID,

                                    $aaa = isset($report_details[2])?@$report_details[2]:@$report_details[1];
                                    if($aaa == "TPS limit Exceeded")
                                        continue;

                                    if (isset($report_details[1])) {
                                        $gpSmsDetail = SmsCamPending::where('id', $gpTransferred[$countTSms])->first();
                                        $gpTransferredForDelete[] = $gpSmsDetail->id;

                                        $gpDataForInsert[] = array(
                                            'user_id' => $gpSmsDetail->user_id,
                                            'sender_id' => $gpSmsDetail->sender_id,
                                            'campaign_id' => $gpSmsDetail->campaign_id,
                                            'sct_cell_no' => $gpSmsDetail->scp_cell_no,
                                            'sct_message' => $gpSmsDetail->scp_message,
                                            'sct_sms_cost' => $gpSmsDetail->scp_sms_cost,
                                            'operator_id' => $gpSmsDetail->operator_id,
                                            'sct_campaign_type' => $gpSmsDetail->scp_campaign_type,
                                            'sct_deal_type' => $gpSmsDetail->scp_deal_type,
                                            'sct_sms_type' => $gpSmsDetail->scp_sms_type,
                                            'sct_sms_id' => isset($report_details[2])?@$report_details[2]:@$report_details[1],
                                            'sct_sms_text_type' => $gpSmsDetail->scp_sms_text_type,
                                            'sct_target_time' => $gpSmsDetail->scp_target_time,
                                            'created_at' => $gpSmsDetail->created_at,
                                            'updated_at' => $gpSmsDetail->updated_at,
                                            'sct_delivery_report' => 'PENDING',
                                            'sct_status' => isset($report_details[2])?@$report_details[1]:@$report_details[0],
                                        );
                                    }
                                    $countTSms++;
                                }
                                try {
                                    SmsCampaign_24h::insert($gpDataForInsert);
                                    $gpDataForInsert = array();

                                    SmsCamPending::whereIn('id', $gpTransferredForDelete)->delete();
                                        $returnData['gp'] = "GP is Working...";
                                } catch (\Exception $e) {
                                    $returnError['gpError'] = "something went wrong" . $e->getMessage();
                                }
                            }

                            /* ----------------------------------- */
                            /* ----------------------------------- */
                            /* ----------------------------------- */
                            /*Editing End*/
                        }
                    }catch (\Exception $e) {
                        $returnError['gpError'] = "something went wrong" . $e->getMessage();
                    }
                    /*
                     * ---------------
                     * end send gp sms
                     * ---------------
                     * */


                    /*
                     * -----------------
                     * start send bl sms
                     * -----------------
                     * */
                    $blSmsOfThisCampaigns = SmsCamPending::where([
                        'campaign_id' => $maskingSmsCampaign->campaign_id,
                        'scp_campaign_status' => 1,
                        'scp_message' => $maskingSmsCampaign->scp_message,
                        'sender_id' => $maskingSmsCampaign->sender_id,
                        'operator_id' => '2'])
                        ->orderBy('id', 'desc')
                        ->take($limitSms)
                        ->get();/*bl = 2*/
                    if (count($blSmsOfThisCampaigns) > 0) {
                        foreach ($blSmsOfThisCampaigns as $blSmsDetails) {
                            $blNumbers[] = $blSmsDetails->scp_cell_no;
                            $blTransferred[] = $blSmsDetails->id;
                        }

                        $countTSms = 0;
                        $xml_response = \SmsHelper::send_masking_banglalink_sms($virtualNumber->banglalink_virtual_number->sivn_api_user_name, $virtualNumber->banglalink_virtual_number->sivn_api_password, $maskingSmsCampaign->scp_message, $blNumbers, $maskingSmsCampaign->sender->sir_sender_id);
                        if ($xml_response == '0150') {
                            $returnError['blError'] = "something was missing for banglalink masking sms";
                        } elseif ($xml_response == 'bl_error') {
                            $returnError['blError'] = "something went wrong to send banglalink masking sms";
                        } else {
                            $blDataForInsert = array();
                            foreach ($blNumbers as $blNumber) {

                                $checkedSms = SmsCamPending::where('id', $blTransferred[$countTSms])->first();
                                $blDataForInsert[] = array(
                                    'user_id' => $checkedSms->user_id,
                                    'sender_id' => $checkedSms->sender_id,
                                    'campaign_id' => $checkedSms->campaign_id,
                                    'sct_cell_no' => $checkedSms->scp_cell_no,
                                    'sct_message' => $checkedSms->scp_message,
                                    'sct_sms_cost' => $checkedSms->scp_sms_cost,
                                    'operator_id' => $checkedSms->operator_id,
                                    'sct_campaign_type' => $checkedSms->scp_campaign_type,
                                    'sct_deal_type' => $checkedSms->scp_deal_type,
                                    'sct_sms_type' => $checkedSms->scp_sms_type,
                                    'sct_sms_id' => '0',
                                    'sct_sms_text_type' => $checkedSms->scp_sms_text_type,
                                    'sct_target_time' => $checkedSms->scp_target_time,
                                    'created_at' => $checkedSms->created_at,
                                    'updated_at' => $checkedSms->updated_at,
                                    'sct_delivery_report' => 'PENDING',
                                    'sct_status' => '0',
                                );

                                $countTSms++;
                            }
                            try {
                                SmsCampaign_24h::insert($blDataForInsert);
                                $blDataForInsert = array();

                                SmsCamPending::whereIn('id', $blTransferred)->delete();

                                $returnData['banglalink'] = "Banglalink Working...";

                            } catch (\Exception $e) {
                                $returnError['blError'] = "something went wrong" . $e->getMessage();
                            }

                        }
                    }
                    /*
                     * ---------------
                     * end send bl sms
                     * ---------------
                     * */


                    /*
                     * --------------------------
                     * start send robi airtel sms
                     * --------------------------
                     * */
                    $robiAirtelSmsOfThisCampaigns = SmsCamPending::where([
                        'campaign_id' => $maskingSmsCampaign->campaign_id,
                        'scp_message' => $maskingSmsCampaign->scp_message,
                        'scp_campaign_status' => 1,
                        'sender_id' => $maskingSmsCampaign->sender_id])
                        ->whereIn('operator_id', ['1', '4'])
                        ->orderBy('id', 'desc')
                        ->take($limitSms)
                        ->get();/*airtel = 1, robi = 4*/

                    if (count($robiAirtelSmsOfThisCampaigns) > 0) {
                        foreach ($robiAirtelSmsOfThisCampaigns as $raSmsDetails) {
                            $raNumbers[] = $raSmsDetails->scp_cell_no;
                            $raTransferred[] = $raSmsDetails->id;
                        }

                        $countTSms = 0;
                        $xml_response = \SmsHelper::send_masking_mobireach_sms($virtualNumber->robi_virtual_number->sivn_api_user_name, $virtualNumber->robi_virtual_number->sivn_api_password, $maskingSmsCampaign->scp_message, $raNumbers, $maskingSmsCampaign->sender->sir_sender_id);
                        // dd($xml_response);
                        if ($xml_response == '0150') {
                            $exists = ErrorNotification::where('user_id', $raSmsDetails->user_id)
                                ->where('operator_id', 4)
                                ->where('created_at', '>', Carbon::now()->subHours(6) )
                                ->first();

                            if ( !$exists ){
                                try{
                                    $error_msg = "Robi masking sms is not sending.";
                                    $sms_text = "Error Message ! \nUser ID : ".$raSmsDetails->user_id.".\nSender ID : ".$raSmsDetails->sender->sir_sender_id.".\nCampaign ID: ".$raSmsDetails->campaign_id.".\nMessage : ".$error_msg;
                                    $sms_text = urlencode($sms_text);
                                    $number = "01823037726";
                                    $sender = "8801844532630";
                                    $api_key = UserDetail::where('user_id', 6)->pluck('api_key');
                                    $url = "http://sms.iglweb.com/api/v1/send?api_key=$api_key[0]&contacts=$number&senderid=$sender&msg=$sms_text";
                                    $client = new Client();
                                    $res = $client->request('GET', $url);
                                    $ret = $res->getBody();

                                    $error = new ErrorNotification();
                                    $error->user_id = $raSmsDetails->user_id;
                                    $error->sender_id = $raSmsDetails->sender_id;
                                    $error->operator_id = 4;
                                    $error->campaign_id = $raSmsDetails->campaign_id;
                                    $error->error_message = "Robi masking sms is not sending.";
                                    $error->save();
                                }catch(\Exception $e){
//                                    echo 'something went wrong';
                                }
                            }
                            $returnError['robiAirtelError'] = "something was missing for robi masking sms";
                        } elseif ($xml_response == '0160') {
                            $exists = ErrorNotification::where('user_id', $raSmsDetails->user_id)
                                ->where('operator_id', 4)
                                ->where('created_at', '>', Carbon::now()->subHours(6) )
                                ->first();

                            if ( !$exists ){
                                try{
                                    $error_msg = "Robi masking sms is not sending.";
                                    $sms_text = "Error Message ! \nUser ID : ".$raSmsDetails->user_id.".\nSender ID : ".$raSmsDetails->sender->sir_sender_id.".\nCampaign ID: ".$raSmsDetails->campaign_id.".\nMessage : ".$error_msg;
                                    $sms_text = urlencode($sms_text);
                                    $number = "01823037726";
                                    $sender = "8801844532630";
                                    $api_key = UserDetail::where('user_id', 6)->pluck('api_key');
                                    $url = "http://sms.iglweb.com/api/v1/send?api_key=$api_key[0]&contacts=$number&senderid=$sender&msg=$sms_text";
                                    $client = new Client();
                                    $res = $client->request('GET', $url);
                                    $ret = $res->getBody();

                                    $error = new ErrorNotification();
                                    $error->user_id = $raSmsDetails->user_id;
                                    $error->sender_id = $raSmsDetails->sender_id;
                                    $error->operator_id = 4;
                                    $error->campaign_id = $raSmsDetails->campaign_id;
                                    $error->error_message = "Robi masking sms is not sending.";
                                    $error->save();
                                }catch(\Exception $e){
//                                    echo "Something Went Wrong";
                                }
                            }
                            $returnError['robiAirtelError'] = "something went wrong to call robi masking api";
                        } else {
                            $raDataForInsert = array();
                            foreach ($xml_response as $smsReport) {

                                $checkedSms = SmsCamPending::where('id', $raTransferred[$countTSms])->first();
                                $raDataForInsert[] = array(
                                    'user_id' => $checkedSms->user_id,
                                    'sender_id' => $checkedSms->sender_id,
                                    'campaign_id' => $checkedSms->campaign_id,
                                    'sct_cell_no' => $checkedSms->scp_cell_no,
                                    'sct_message' => $checkedSms->scp_message,
                                    'sct_sms_cost' => $checkedSms->scp_sms_cost,
                                    'operator_id' => $checkedSms->operator_id,
                                    'sct_campaign_type' => $checkedSms->scp_campaign_type,
                                    'sct_deal_type' => $checkedSms->scp_deal_type,
                                    'sct_sms_type' => $checkedSms->scp_sms_type,
                                    'sct_sms_id' => $smsReport->MessageId,
                                    'sct_sms_text_type' => $checkedSms->scp_sms_text_type,
                                    'sct_target_time' => $checkedSms->scp_target_time,
                                    'created_at' => $checkedSms->created_at,
                                    'updated_at' => $checkedSms->updated_at,
                                    'sct_delivery_report' => 'PENDING',
                                    'sct_status' => $smsReport->Status,
                                );

                                $countTSms++;
                            }
                            try {
                                SmsCampaign_24h::insert($raDataForInsert);
                                $raDataForInsert = array();

                                SmsCamPending::whereIn('id', $raTransferred)->delete();
                                $returnData['robi_airtel'] = "Robi Airtel Working...";
                            } catch (\Exception $e) {
                                $returnError['robiAirtelError'] = "something went wrong" . $e->getMessage();
                            }

                        }
                    }
                    /*
                     * ------------------------
                     * end send robi airtel sms
                     * ------------------------
                     * */


                    /*
                     * -----------------------
                     * start send teletalk sms
                     * -----------------------
                     * */
                    $teletalkSmsOfThisCampaigns = SmsCamPending::where([
                        'campaign_id' => $maskingSmsCampaign->campaign_id,
                        'scp_message' => $maskingSmsCampaign->scp_message,
                        'scp_campaign_status' => 1,
                        'sender_id' => $maskingSmsCampaign->sender_id,
                        'operator_id' => '5'])
                        ->orderBy('id', 'desc')
                        ->take($limitSms)
                        ->get();/*bl = 5*/


                    if (count($teletalkSmsOfThisCampaigns) > 0) {
                        foreach ($teletalkSmsOfThisCampaigns as $ttSmsDetails) {
                            $ttSmsDetails->scp_cell_no;
                            $ttTransferred[] = $ttSmsDetails->id;
                            $countTSms = 0;

                            $senderIdDetails = SenderIdRegister::find($ttSmsDetails->sender_id);

                            $xml_response = \SmsHelper::send_masking_teletalk_sms(
                                $senderIdDetails->sir_teletalk_user_name,
                                $senderIdDetails->sir_teletalk_user_password,
                                $maskingSmsCampaign->scp_message,
                                $ttSmsDetails->scp_cell_no,
                                $maskingSmsCampaign->sender->sir_sender_id);

                            if ($xml_response == '0150') {
                                $returnError['ttError'] = "something was missing for teletalk masking sms";

                                $sms_ret_id = "";
                                $sms_status = "SOMETHING MISSING";
                                $sms_report = "PENDING";

                                $ttDataForInsert = array();
                                // $checkedSms = SmsCamPending::where('id', $ttTransferred[$countTSms])->first();
                                $ttDataForInsert[] = array(
                                    'user_id' => $ttSmsDetails->user_id,
                                    'sender_id' => $ttSmsDetails->sender_id,
                                    'campaign_id' => $ttSmsDetails->campaign_id,
                                    'sct_cell_no' => $ttSmsDetails->scp_cell_no,
                                    'sct_message' => $ttSmsDetails->scp_message,
                                    'sct_sms_cost' => $ttSmsDetails->scp_sms_cost,
                                    'operator_id' => $ttSmsDetails->operator_id,
                                    'sct_campaign_type' => $ttSmsDetails->scp_campaign_type,
                                    'sct_deal_type' => $ttSmsDetails->scp_deal_type,
                                    'sct_sms_type' => $ttSmsDetails->scp_sms_type,
                                    'sct_sms_id' => $sms_ret_id,
                                    'sct_sms_text_type' => $ttSmsDetails->scp_sms_text_type,
                                    'sct_target_time' => $ttSmsDetails->scp_target_time,
                                    'created_at' => $ttSmsDetails->created_at,
                                    'updated_at' => $ttSmsDetails->updated_at,
                                    'sct_delivery_report' => $sms_report,
                                    'sct_status' => $sms_status
                                );

                                $countTSms++;

                                try {
                                    SmsCampaign_24h::insert($ttDataForInsert);
                                    $ttDataForInsert = array();

                                    SmsCamPending::whereIn('id', $ttTransferred)->delete();
                                    $returnData['teletalk'] = "Teletalk Working...";
                                } catch (\Exception $e) {
                                    $returnError['ttError'] = "something went wrong" . $e->getMessage();
                                }

                            } elseif ($xml_response == '0160') {
                                $returnError['ttError'] = "something went wrong to send teletalk number";
                            } else {


                                preg_match_all('/>(.*?)</', $xml_response, $matches);
                                $full_ret_text = $matches[1][0];
                                $exp_ret_text = explode(',', $full_ret_text);

                                if ($exp_ret_text[0] == "SUCCESS") {
                                    $exp_sms_id = explode('=', $exp_ret_text[1]);
                                    $sms_ret_id = $exp_sms_id[1];
                                    $sms_status = "SUCCESS";
                                    $sms_report = "PENDING";
                                } else {
                                    $sms_ret_id = 0;
                                    $sms_status = $exp_ret_text[0];
                                    $sms_report = $exp_ret_text[1];
                                }

                                $ttDataForInsert = array();
                                // $checkedSms = SmsCamPending::where('id', $ttTransferred[$countTSms])->first();
                                $ttDataForInsert[] = array(
                                    'user_id' => $ttSmsDetails->user_id,
                                    'sender_id' => $ttSmsDetails->sender_id,
                                    'campaign_id' => $ttSmsDetails->campaign_id,
                                    'sct_cell_no' => $ttSmsDetails->scp_cell_no,
                                    'sct_message' => $ttSmsDetails->scp_message,
                                    'sct_sms_cost' => $ttSmsDetails->scp_sms_cost,
                                    'operator_id' => $ttSmsDetails->operator_id,
                                    'sct_campaign_type' => $ttSmsDetails->scp_campaign_type,
                                    'sct_deal_type' => $ttSmsDetails->scp_deal_type,
                                    'sct_sms_type' => $ttSmsDetails->scp_sms_type,
                                    'sct_sms_id' => $sms_ret_id,
                                    'sct_sms_text_type' => $ttSmsDetails->scp_sms_text_type,
                                    'sct_target_time' => $ttSmsDetails->scp_target_time,
                                    'created_at' => $ttSmsDetails->created_at,
                                    'updated_at' => $ttSmsDetails->updated_at,
                                    'sct_delivery_report' => $sms_report,
                                    'sct_status' => $sms_status
                                );

                                $countTSms++;

                                try {
                                    SmsCampaign_24h::insert($ttDataForInsert);
                                    $ttDataForInsert = array();

                                    SmsCamPending::whereIn('id', $ttTransferred)->delete();
                                    $returnData['teletalk'] = "Teletalk Working...";
                                } catch (\Exception $e) {
                                    $returnError['ttError'] = "something went wrong" . $e->getMessage();
                                }

                            }
                        }
                    }
                    /*
                     * ---------------------
                     * end send teletalk sms
                     * ---------------------
                     * */


                    /*var_dump($gpNumbers);
                    echo "<br><br><br>";
                    var_dump($blNumbers);
                    echo "<br><br><br>";
                    var_dump($raNumbers);
                    echo "<br><br><br>";
                    var_dump($ttNumbers);
                    echo "<br><br><br>";
                    var_dump($gpTransferred);
                    echo "<br><br><br>";
                    var_dump($blTransferred);
                    echo "<br><br><br>";
                    var_dump($raTransferred);
                    echo "<br><br><br>";
                    var_dump($ttTransferred);
                    echo "<br><br><br>";*/
                }

            } else {
                $returnData['message'] = "no sms found";

            }
        }
        return view('cron.masking-non-masking-sms-cron', compact(
            'non_masking_return_text',
            'returnData',
            'returnError',
            'retTextRobi',
            'retTextRankstel',
            'retTextGp',
            'retTextBl'
        ));

    }
    
    public function total_sent_of_this_month(){
        $year = date('Y');
        $month = date('m');
        
        $transaction = SmsDesktopCampaignId::whereYear('sdci_targeted_time',$year)
            ->whereMonth('sdci_targeted_time',$month)
            ->sum('sdci_total_submitted');
            
        return $transaction;
    }

}















