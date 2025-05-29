<?php

namespace App\Http\Controllers\Api;

use App\Model\AccSmsBalance;
use App\Model\AccUserCreditHistory;
use App\Model\SenderIdRegister;
use App\Model\SenderIdUser;
use App\Model\SmsDesktopCampaignId;
use App\Model\SmsDesktopPending;
use App\Model\SmsDesktop24h;
use App\Model\User;
use App\Model\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class SmsSendDesktopController extends Controller
{
    public function sendSmsDesktop(Request $request)
    {
        
        // $sender = SenderIdRegister::where('id',$request->sender_id)->first();
    //    dd($request->all());
        /*validate data*/
        if(!$request->api_key){
            return response()->json(['code'=>'10010', 'message'=>'Missing api key']);
        }
        elseif (!$request->mobileno){
            return response()->json(['code'=>'10020', 'message'=>'Missing contact numbers']);
        }
        // elseif(!$request->senderid){
        //     return response()->json(['code'=>'445030', 'message'=>'Missing sender id']);
        // }
        elseif (!$request->msg){
            return response()->json(['code'=>'10170', 'message'=>'Missing text sms']);
        }


        /*check exist data*/
        /*check api*/
        $userDetail = UserDetail::where('api_key', $request->api_key)->where('api_permission', 1)->first();
        
        // dd($main_text);
        if(!$userDetail){
            return response()->json(['code'=>'10040', 'message'=>'Invalid api key or You Need API Permission']);
        }
        $user = User::where('id', $userDetail->user_id)->first();
        if($user->status==2){
            return response()->json(['code'=>'10050', 'message'=>'Your account was suspended']);
        }elseif($user->status==3){
            return response()->json(['code'=>'10060', 'message'=>'Your account was expired']);
        }elseif ($user->role!=5){
            if(!$request->for_registration){
                return response()->json(['code'=>'10070', 'message'=>'Only a user can send sms']);
            }elseif (($request->for_registration!='resellerToUser') && ($request->for_registration!='adminToReseller')){
                return response()->json(['code'=>'10071', 'message'=>'Only a user can send sms']);
            }
        }
        /*check sender id*/
        // $sender = SenderIdRegister::where('sir_sender_id', $request->senderid)->first();
        // if(!$sender){
        //     return response()->json(['code'=>'445080', 'message'=>'Invalid sender id']);
        // }
        // $checkSenderUser = SenderIdUser::where(['user_id'=>$user->id, 'sender_id'=>$sender->id])->first();
        // if(!$checkSenderUser){
        //     return response()->json(['code'=>'445090', 'message'=>'You have no access to this sender id']);
        // }

        /*check and get numbers*/
        $allContacts = explode(',',$request->mobileno);
        $validNumbers = array();
        foreach ($allContacts as $contact) {
            $number = \PhoneNumber::addNumberPrefix($contact);
            if (\PhoneNumber::isValid($number)) {
                $validNumbers[] = $number;
            }
        }
        /*get unique number*/
        $validUniqueNumbers = array_unique($validNumbers);
        if (count($validUniqueNumbers) < 1) {
            return response()->json(['code'=>'10110', 'message'=>'All numbers are invalid']);
        }

        /*sms count*/
        if ($userDetail->template_permission != NULL) {
            $a = $request->msg;
           
            // $num = array();
            // $main_text = array();
            // $filteredNumbers = preg_match_all('!\d+-?\d+!', $a, $matches);
            
            // if($matches[0][0] == ''){
            //     return response()->json(['code' => '445500', 'message' => 'You Need OTP Number']);
            // }
            // $num = $matches[0][0];
            $num = array();
            $main_text = array();
            

            if (strpos($a, 'Code is ') !== false) {
                /*gp and bl format*/
                $numberFromText = explode('Code is ', $a)[1];
                // dd($message_with_trx_id);

                $num = explode(' ', $numberFromText)[0];
            }elseif(strpos($a, 'HI Use ') !== false){
                $numberFromText = explode('HI Use ', $a)[1];
                // dd($message_with_trx_id);

                $num = explode(' ', $numberFromText)[0];
            }elseif(strpos($a, 'Hi Use ') !== false){
                $numberFromText = explode('Hi Use ', $a)[1];
                // dd($message_with_trx_id);

                $num = explode(' ', $numberFromText)[0];
            }else{
                
                $filteredNumbers = preg_match_all('!\d+-?\d+!', $a, $matches);
                $num = $matches[0][0];
            }
            
            // dd($num);

            // $main_text = \SmsHelper::main_test_api($userDetail->user_id,$num);
            $text = \SmsHelper::main_test_api($userDetail->user_id,$num);
            // $search = str_contains('OTPDATE', $main_text);
            // dd($main_text);
            if ($userDetail->date_format != NULL) {
                $main_text = $text. ".\n" .\SmsHelper::date_format_api($userDetail->user_id);
                $searchReplaceArray = array(
                '0' => json_decode('"0\ufe0f\u20e3"'), 
                '1' => json_decode('"1\ufe0f\u20e3"'),
                '2' => json_decode('"2\ufe0f\u20e3"'),
                '3' => json_decode('"3\ufe0f\u20e3"'),
                '4' => json_decode('"4\ufe0f\u20e3"'),
                '5' => json_decode('"5\ufe0f\u20e3"'),
                '6' => json_decode('"6\ufe0f\u20e3"'),
                '7' => json_decode('"7\ufe0f\u20e3"'),
                '8' => json_decode('"8\ufe0f\u20e3"'),
                '9' => json_decode('"9\ufe0f\u20e3"'),
              );
             if(strpos($main_text,'0') || strpos($main_text,'1') || strpos($main_text,'2') || strpos($main_text,'3') || strpos($main_text,'4') || strpos($main_text,'5') || strpos($main_text,'6') || strpos($main_text,'7') || strpos($main_text,'8') || strpos($main_text,'9')){
                 $main_text = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$main_text);
             }else{
                $main_text = $request->msg;
             }
                // dd($main_text);
            }else {
                $main_text = $text;
                $searchReplaceArray = array(
                '0' => json_decode('"0\ufe0f\u20e3"'), 
                '1' => json_decode('"1\ufe0f\u20e3"'),
                '2' => json_decode('"2\ufe0f\u20e3"'),
                '3' => json_decode('"3\ufe0f\u20e3"'),
                '4' => json_decode('"4\ufe0f\u20e3"'),
                '5' => json_decode('"5\ufe0f\u20e3"'),
                '6' => json_decode('"6\ufe0f\u20e3"'),
                '7' => json_decode('"7\ufe0f\u20e3"'),
                '8' => json_decode('"8\ufe0f\u20e3"'),
                '9' => json_decode('"9\ufe0f\u20e3"'),
              );
             if(strpos($main_text,'0') || strpos($main_text,'1') || strpos($main_text,'2') || strpos($main_text,'3') || strpos($main_text,'4') || strpos($main_text,'5') || strpos($main_text,'6') || strpos($main_text,'7') || strpos($main_text,'8') || strpos($main_text,'9')){
                 $main_text = str_replace(array_keys($searchReplaceArray),array_values($searchReplaceArray),$main_text);
             }else{
                $main_text = $request->msg;
             }
                // dd($main_text);
                // dd($main_text);
            }
            
        }else {
            $main_text = $request->msg;
           
        }
        if (\SmsHelper::is_unicode($main_text)) {
            $smsType = 'unicode'; //unicode
            $sms_number = \SmsHelper::unicode_sms_count($main_text);
          

        } else {
            $smsType = 'text'; //text
            $sms_number = \SmsHelper::text_sms_count($request->msg);
        }

       
        $total_cost = \BalanceHelper::campaignDesktopTotalCost($sms_number, $validUniqueNumbers, $user->id);


        if (\BalanceHelper::user_available_balance($user->id) < $total_cost) {
            return response()->json(['code'=>'10120', 'message'=>'You haven\'t enough balance . please recharge first...']);
        } elseif (\BalanceHelper::check_parents_Desktop_available_balance($user->id, $sms_number, $validUniqueNumbers) == false) {
            return response()->json(['code'=>'10130', 'message'=>'Your reseller don\'t have enough balance . told him to recharge first...']);
        } else {
            try {
                $campaign_id = $user->id . time() . random_int(1, 9) . random_int(1, 9) . random_int(1, 9) . random_int(1, 9) . random_int(1, 9);
                
                    $sms_masking_type = '1';
               
                    
                $current_date = Carbon::now()->toDateTimeString();

                $sms_sender_op = null;
                    
               
                    
                $insertCampaign = SmsDesktopCampaignId::create([
                    'user_id' => $user->id,
                    // 'sender_id' => $sender->id,
                    'sdci_campaign_id' => $campaign_id,
                    'sdci_total_submitted' => count($validUniqueNumbers),
                    'sdci_total_cost' => $total_cost,
                    'sdci_campaign_type' => '1', /*1=instant, 2=Schedule */
                    'sdci_deal_type' => '1', /* 1=SMS, 2=Campaign */
                    'sdci_sms_type' => $sms_masking_type, /*1=NonMasking, 2=Masking*/
                    'sdci_sender_operator' => $sms_sender_op, /*1=NonMasking, 2=Masking*/
                    'sdci_dynamic_type' => '0',/*1=dynamic, 0=general*/
                    'sdci_targeted_time' => $current_date,
                    'sdci_browser' => $request->header('User-Agent'),
                    'sdci_mac_address' => null,
                    'sdci_ip_address' => $request->ip(),
                    'sdci_from_api' => 4,
                ]);

                
                $insertCount = 0;
                $dataForInsert = array();
                $serial = 0;
                foreach ($validUniqueNumbers as $number) {
                    
                    $operator = \PhoneNumber::checkOperator($number);

                    $desktopPending = SmsDesktopPending::create([
                        'user_id' => $user->id,
                        'campaign_id' => $insertCampaign->id,
                        'sdp_cell_no' => $number,
                        'sdp_message' => $main_text,
                        'sdp_customer_message' => $request->msg,
                        'sdp_sms_cost' => \BalanceHelper::singleSmsDesktopCost($sms_number, $number, $user->id),
                        'operator_id' => $operator['id'],
                        'sdp_campaign_type' => '1', //*1=instant, 2=Schedule *
                        'sdp_deal_type' => '1', //* 1=SMS, 2=Campaign *
                        'sdp_sms_type' => $sms_masking_type, //*1=NonMasking, 2=Masking*
                        'sdp_sms_id' => '0',
                        'sdp_tried' => '0', //*Try For Send *
                        'sdp_picked' => '0', //*0=not try, 1= try *
                        'sdp_sms_text_type' => $smsType, //*SMS type=text/unicode*
                        'sdp_target_time' => $current_date,
                        'sdp_status' => '4',
                        'created_at' => $current_date,
                        'updated_at' => $current_date,
                    ]);
                   
                    
                }
                


                /*debit user balance*/
                $user_position = $user->position;
                $user_id = $user->id;

                $user_det = User::where('id', $user_id)->first();

                while ($user_position >= 1) {
                    /*get total cost*/
                    $campaign_cost = \BalanceHelper::campaignDesktopTotalCost($sms_number, $validUniqueNumbers, $user_det->id);

                    AccSmsBalance::create([
                        'asb_paid_by' => $user_det->create_by,
                        'asb_pay_to' => $user_det->id,
                        'asb_pay_ref' => $campaign_id,
                        'asb_credit' => '0',
                        'asb_debit' => $campaign_cost,
                        'asb_submit_time' => $current_date,
                        'asb_target_time' => $current_date,
                        'asb_pay_mode' => '4', //*campaign*
                        'asb_payment_status' => '1', //*1=paid, 2=checking*
                        'asb_deal_type' => '2', //*1=deposit, 2=campaign*
                        'credit_return_type' => '0',
                    ]);

                    $user_det = User::where('id', $user_det->create_by)->first();
                    $user_position = $user_det->position;
                }

                /*add user credit history*/
                AccUserCreditHistory::create([
                    'campaign_id' => $insertCampaign->id,
                    'user_id' => $user->id,
                    'uch_sms_count' => count($validUniqueNumbers),
                    'uch_sms_cost' => $total_cost,
                ]);

                return response()->json(['code'=>'10000', 'message'=>'Message has been sent...']);

            } catch (\Exception $e) {

                return response()->json(['code'=>'10150', 'message'=>'Something was wrong to sent sms. please contact with admin!!! ..'.$e->getMessage()]);
            }
        }

    }

    public function showBalance(Request $request)
    {
        /*validate data*/
        if(!$request->api_key){
            return response()->json(['code'=>'10010', 'message'=>'Missing api key']);
        }
        /*check api*/
        $userDetail = UserDetail::where('api_key', $request->api_key)->first();
        if(!$userDetail){
            return response()->json(['code'=>'10040', 'message'=>'Invalid api key']);
        }

        $user = User::where('id', $userDetail->user_id)->first();
        if($user->status==2){
            return response()->json(['code'=>'10050', 'message'=>'Your account was suspended']);
        }elseif($user->status==3){
            return response()->json(['code'=>'10060', 'message'=>'Your account was expired']);
        }

        try{
            $userAvailableBalance = \BalanceHelper::user_available_balance($user->id);
            return response()->json(['code'=>'10000', 'balance'=>number_format($userAvailableBalance, 2)." tk"]);
        }catch (Exception $e){
            return response()->json(['code'=>'10160', 'message'=>'Something was wrong to check balance. please contact with admin!!! ..']);
        }
    }
}
