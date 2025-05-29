<?php

namespace App\Http\Controllers\Cron;

use App\Model\LoadCampaign30day;
use App\Model\LoadSimAvailablleBalance;
use App\Model\LoadSimMessages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LoadCamPending;
use App\Model\LoadPackage;
use App\Model\LoadCampaign;
use Illuminate\Support\Facades\DB;

class FlexiloadCronController extends Controller
{
    public function sendFlexiload()
    {
        $pending_loads = LoadCamPending::where('status', 0)->orderBy('id', 'asc')->take(5)->get();

        foreach ($pending_loads as $pending_load) {
            $targeted_number = $pending_load->targeted_number;
            $flexiload_price = $pending_load->campaign_price;
            $number_type = $pending_load->number_type;
            $operator = $pending_load->operator_id;
            $sms_id = $pending_load->sms_id;

            if ($operator == "gp") {
                $number_type = 1;
            }
            if ($operator == "airtel") {
                $number_type = 1;
            }
            if ($operator == "robi") {
                $number_type = 1;
            }
            if ($operator == "teletalk") {
                $number_type = 1;
            }

            $flapi_key = "W2SA149S9DQ35JTPX0Z695W8K06Z42O73JDM58XH1Z6W0MQH8D";
            $flapi_userid = "01958666900";
            $postdata = array(
                "number" => substr($targeted_number, 2),
                "service" => 64,
                "amount" => $flexiload_price,
                "type" => $number_type,
                "operator" => $operator,
                "id" => $sms_id,
                "user" => $flapi_userid,
                "key" => $flapi_key
            );
            // dump($postdata);
            $sendtourl = "http://rambd.com/sendapi/request";


            DB::beginTransaction();
            $pending_load->status = 1;
            $succesfull_load = new LoadCampaign30day();
            $succesfull_load->create(json_decode($pending_load, true));
            $pending_load->delete();

            $sendtoApi = \SmsHelper::sendFlexiload($sendtourl, $postdata);

            if (!isset((json_decode($sendtoApi)->status)) || (json_decode($sendtoApi)->status != 1)) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        }

        try {
            DB::transaction(function () {
                $moveDatasFromMonth = LoadCampaign30day::where('created_at', '<', Carbon::now()->startOfMonth())->get();
                $moved_data = array();
                foreach ($moveDatasFromMonth as $moveData) {
                    $moved_data[] = $moveData->id;
                    LoadCampaign::create([
                        'user_id' => $moveData->user_id,
                        'operator_id' => $moveData->sender_id,
                        'sms_id' => $moveData->campaign_id,
                        'campaign_id' => $moveData->sct_cell_no,
                        'targeted_number' => $moveData->sct_message,
                        'owner_name' => $moveData->sct_sms_cost,
                        'package_id' => $moveData->operator_id,
                        'number_type' => $moveData->sct_campaign_type,
                        'campaign_type' => $moveData->sct_deal_type,
                        'campaign_price' => $moveData->sct_sms_type,
                        'remarks' => $moveData->sct_sms_id,
                        'transaction_id' => $moveData->transaction_id,
                        'status' => $moveData->sct_sms_text_type,
                        'created_at' => $moveData->created_at,
                        'updated_at' => $moveData->sct_target_time
                    ]);
                }

                LoadCampaign30day::whereIn('id', $moved_data)->delete();

            });
        } catch (\Exception $e) {

        }
        $rest_pending_count = LoadCamPending::count();
        return view('cron.sendPendingLoad', ['rest_pendings' => $rest_pending_count]);

    }


    public function testlexiloadReport()
    {
        $flapi_key = "W2SA149S9DQ35JTPX0Z695W8K06Z42O73JDM58XH1Z6W0MQH8D";
        $flapi_userid = "01958666900";
        $postdata = array(
            "id" => "290158071081052",
            "user" => $flapi_userid,
            "key" => $flapi_key
        );
        $sendtourl = "http://rambd.com/sendapi/status";

        $sendtoApi = \SmsHelper::sendFlexiload($sendtourl, $postdata);
        return $sendtoApi;
        json_decode($sendtoApi)->status;
    }

    public function getFlexiloadReport()
    {
        $chengedNumber = 0;
        /*set offsetData in session if wasn't set previous*/
        if (session()->get('flexi_offsetData') == NULL) {
            session(['flexi_offsetData' => 0]);
        }

        /*set goToNullOffset in session if wasn't set previous*/
        if (session()->get('flexi_goToNullOffset') == NULL) {
            session(['flexi_goToNullOffset' => 0]);
        }

        for ($j = 0; $j < 10; $j++) {
            /*set offsetData variable based on session offsetData & goToNullOffset*/
            if (session()->get('flexi_goToNullOffset') == 0) {
                $offsetData = session()->get('flexi_offsetData');
            } else {
                $offsetData = 0;
                session(['flexi_offsetData' => 0]);
                session(['flexi_goToNullOffset' => 0]);
            }

            /*get undelivered numbers*/
            $pendingNumbers = LoadCampaign30day::where(['transaction_id' => NULL])->orWhere(['transaction_id' => ''])->skip($offsetData)->take(5)->get();

            $undeliveredNumber = Null;
            if (count($pendingNumbers) < 5) {
                session(['flexi_goToNullOffset' => 1]);
            }
            if (count($pendingNumbers) > 0) {
                foreach ($pendingNumbers as $pending) {
                    $flapi_key = "W2SA149S9DQ35JTPX0Z695W8K06Z42O73JDM58XH1Z6W0MQH8D";
                    $flapi_userid = "01958666900";
                    $postdata = array(
                        "id" => $pending->sms_id,
                        "user" => $flapi_userid,
                        "key" => $flapi_key
                    );
                    $sendtourl = "http://rambd.com/sendapi/status";

                    $jsonDeliveryReport = \SmsHelper::sendFlexiload($sendtourl, $postdata);
                    $decodeJson = json_decode($jsonDeliveryReport);
                    if (isset($decodeJson->trxid) && ($decodeJson->trxid != "") && ($decodeJson->trxid != NULL)) {
                        $pending->transaction_id = $decodeJson->trxid;
                        $pending->save();
                    } else {
                        session(['flexi_offsetData' => session()->get('flexi_offsetData') + 1]);
                    }

                }

            } else {
                $returnData['no_number'] = "no number available for check report";
            }

            if (session()->get('flexi_goToNullOffset') == 1) {
                break;
            }

        }

        $returnData['still_pending'] = session('flexi_offsetData');
        $returnData['check_complete'] = session('flexi_goToNullOffset');
        $returnData['changed'] = $chengedNumber;

        return view('cron.flexiload-report', compact('returnData'));
    }


    public function flexiload_pending(Request $request)
    {

        $simno = $request->simno;
        $opcompany = $request->company;
        $simbalance = $request->simbal;

        if ($opcompany == 2) {
            $opcompany = "banglalink";
        }

        //only pending data find
        $pendingdatacount = LoadCamPending::where('status', 0)->where('operator_id', $opcompany)->count();
        if ($pendingdatacount > 0) {
            $pendingdata = LoadCamPending::where('status', 0)->where('operator_id', $opcompany)->first();

            //op id  airetl =1,blink =2, gp = 3,robi= 4, teletalk =4

            $serial_id = $pendingdata->id;
            $number = $pendingdata->targeted_number;
            $operator_name = $pendingdata->operator_id;
            $number_type = $pendingdata->number_type;
            $amount = $pendingdata->campaign_price;

            $number = substr($number, 2);
            $offer_data = LoadPackage::where('package_price',$amount)->first();
            if (!empty($offer_data)) {
                $offer = 'yes';
            }else{
                $offer = 'no';
            }

            if ($operator_name == "gp" && ($number_type == 1 || $number_type == 2)) {
                $number_type = 1;
            }
            if ($operator_name == "gp" && $number_type == 3) {
                $number_type = 3;
            }
            if ($operator_name == "airtel") {
                $number_type = 1;
            }
            if ($operator_name == "robi") {
                $number_type = 1;
            }
            if ($operator_name == "teletalk") {
                $number_type = 1;
            }

            $pendingdataupdate = LoadCamPending::where('id', $pendingdata->id)->update(['status'=> 1]);

            /*$pendingdata->status = 1;
            $pendingdata->sms_id = $pendingdata->id;
            $succesfull_load = new LoadCampaign30day();
            $succesfull_load->create(json_decode($pendingdata, true));
            $pendingdata->delete();*/


            return response()->json([
                'id' => $serial_id,
                'phone' => $number,
                'amount' => $amount,
                'type' => $number_type,
                'offer' => $offer,
                'status' => 1
            ], 200);


        }

        return response()->json([
            'data' => 'Not Found',
            'status' => 2
        ], 200);


        //
    }

    public function flexiload_message_store(Request $request)
    {

        /*$request->validate([
            'sim' => 'required',
            'op' => 'required',
            'msg' => 'required',
            'phone' => 'required',
            'st' => 'nullable'
        ]);*/
        if (empty($request->msg)) {
            return response()->json([
                'msg' => 'message empty',
                'status' => 2
            ], 200);
        }

        $simno = $request->sim;
        $opcompany = $request->op;
        $msg = $request->msg;
        $sender = $request->phone;
        $serialid = $request->st;

        $total_message = $msg;

        try {
            $trx_id = $this->getTransactionIdFromMessage($total_message, $opcompany);
            // dd($trx_id);
        } catch (\Exception $e) {
            $trx_id = "";
        }


        try {
            if (strpos($total_message, 'account balance is TK ') !== false) {
                /*gp and bl format*/
                $message_with_balance = explode('account balance is TK ', $total_message)[1];

                $available_balance = explode(' ', $message_with_balance)[0];
            } elseif (strpos($total_message, 'new balance is ') !== false) {
                /*robi/airtel/teletalk format*/
                $message_with_balance = explode('new balance is ', $total_message)[1];

                $available_balance = explode(' ', $message_with_balance)[0];

            } else {
                $available_balance = "";
            }
        } catch (\Exception $e) {
            $available_balance = "";
        }


        try {
            $loadMsg = new LoadSimMessages();

            $loadMsg->sim_no = $simno;
            $loadMsg->operator_company = $opcompany;
            $loadMsg->message = $msg;
            $loadMsg->sender = $sender;
            $loadMsg->serial_id = $serialid;
            $loadMsg->save();


            if ((isset($trx_id)) && ($trx_id != '') && ($serialid != '')) {
                $loadCamPending = LoadCamPending::where('id', $serialid)->first();
                if (!empty($loadCamPending)) {
                    $loadCamPending->transaction_id = $trx_id;
                    $loadCamPending->save();

                    $loadMsg->user_id = $loadCamPending->user_id;
                    $loadMsg->save();

                    $loadCamPending->sms_id = $loadCamPending->id;
                    $succesfull_load = new LoadCampaign30day();
                    $succesfull_load->create(json_decode($loadCamPending, true));
                    $loadCamPending->delete();
                }
            } 
            elseif ((isset($trx_id)) && ($trx_id != '')) {
                $load_number = $this->getPhoneNumberFromMessage($total_message, $opcompany);
                $load_amount = $this->getLoadAmountFromMessage($total_message, $opcompany);

                $loadCamPending = LoadCamPending::where('targeted_number', $load_number)
                    ->where('campaign_price', $load_amount)
                    ->where(function ($query) {
                        $query->where('transaction_id', NULL)
                            ->orWhere('transaction_id', '');

                    })
                    ->orderBy('id', 'DESC')
                    ->first();
//                dd($loadCam30Days);as
                if (!empty($loadCamPending)) {
                    $loadCamPending->transaction_id = $trx_id;
                    $loadCamPending->save();

                    $loadMsg->user_id = $loadCamPending->user_id;
                    $loadMsg->save();

                    $loadCamPending->sms_id = $loadCamPending->id;
                    $succesfull_load = new LoadCampaign30day();
                    $succesfull_load->create(json_decode($loadCamPending, true));
                    $loadCamPending->delete();
                }
            }

            if ((isset($available_balance)) && ($available_balance != '')) {
                $loadSimBal = LoadSimAvailablleBalance::where('status', 1)->first();

                if (empty($loadSimBal)) {
                    $loadSimBal = new LoadSimAvailablleBalance();
                }

                $loadSimBal->$opcompany = $available_balance;
                $loadSimBal->save();
            }
        } catch (\Exception $exception) {

        }


        return response()->json([
            'data' => 'message insert successfully',
            'status' => 1,
            'insert' => 1
        ], 200);

    }


    public function getLoadAmountFromMessage($total_message, $opcompany)
    {
        try {
            if (strpos($total_message, 'Payment request of TK ') !== false) {
                /*gp format*/
                $message_with_amount = explode('Payment request of TK ', $total_message)[1];

                $amount = explode(' ', $message_with_amount)[0];
            }elseif (strpos($total_message, 'request of BDT ') !== false) {
                /*gp format*/
                $message_with_amount = explode('request of BDT ', $total_message)[1];

                $amount = explode(' ', $message_with_amount)[0];
            } elseif ((strpos($total_message, 'Recharge request of TK ') !== false)) {
                /*bl format*/
                $message_with_amount = explode('Recharge request of TK ', $total_message)[1];

                $amount = explode(' ', $message_with_amount)[0];

            } elseif ((strpos($total_message, 'Recharge Request of TK ') !== false)) {
                /*bl format*/
                $message_with_amount = explode('Recharge Request of TK ', $total_message)[1];

                $amount = explode(' ', $message_with_amount)[0];

            } elseif ((strpos($total_message, 'Recharge ') !== false) && (($opcompany == 'robi') || ($opcompany == 'airtel'))) {
                /*robi/airtel format*/
                $message_with_amount = explode('Recharge ', $total_message)[1];

                $amount = explode(' ', $message_with_amount)[0];

            } elseif (strpos($total_message, 'recharged successfully with ') !== false) {
                /*teletalk format*/
                $message_with_amount = explode('recharged successfully with ', $total_message)[1];

                $amount = explode(' ', $message_with_amount)[0];

            } else {
                $amount = "";
            }
        } catch (\Exception $exception) {
            $amount = "";
        }

        return $amount;
    }


    public function getPhoneNumberFromMessage($total_message, $opcompany)
    {
        try {
            if (strpos($total_message, 'for mobile no.') !== false) {
                /*gp format*/
                $message_with_number = explode('for mobile no.', $total_message)[1];

                $number = explode(',', $message_with_number)[0];
            } elseif ((strpos($total_message, 'for mobile no ') !== false)) {
                /*bl format*/
                $message_with_number = explode('for mobile no ', $total_message)[1];

                $number = explode(',', $message_with_number)[0];

            } elseif ((strpos($total_message, 'Tk to ') !== false) && (($opcompany == 'robi') || ($opcompany == 'airtel'))) {
                /*robi/airtel format*/
                $message_with_number = explode(' Tk to ', $total_message)[1];

                $number = explode(' ', $message_with_number)[0];

            } elseif (strpos($total_message, 'has been recharged successfully with ') !== false) {
                /*teletalk format*/
                $message_with_number = explode('has been recharged successfully with ', $total_message)[0];

                $t_number = explode('.', $message_with_number);
                $number = end($t_number);

            }elseif ((strpos($total_message, ' is accepted for processing') !== false) && $opcompany == 'blink' ) {
                /*teletalk format*/
                // dd("sadsfsdf");
                $message_with_number = explode(' is accepted for processing', $total_message)[0];
                // dd($message_with_number);

                $t_number = explode('for 0', $message_with_number);
                
                $number = end($t_number);
                // dd($number);

            }
            elseif (strpos($total_message, ' is accepted for processing') !== false) {
                /*teletalk format*/
                // dd("sadsfsdf");
                $message_with_number = explode(' is accepted for processing', $total_message)[0];
                // dd($message_with_number);

                $t_number = explode('for ', $message_with_number);
                
                $number = end($t_number);
                // dd($number);

            } else {
                $number = "";
            }
        } catch (\Exception $exception) {
            $number = "";
        }
        $number = trim($number);
        return "880" . $number;
    }


    public function getTransactionIdFromMessage($total_message, $opcompany = null)
    {
        try {
            if (strpos($total_message, 'transaction ID ') !== false) {
                /*gp and bl format*/
                $message_with_trx_id = explode('transaction ID ', $total_message)[1];

                $trx_id = explode(' ', $message_with_trx_id)[0];
            }elseif (strpos($total_message, 'Transaction ID is ') !== false) {
                /*gp and bl format*/
                $message_with_trx_id = explode('Transaction ID is ', $total_message)[1];

                $trx_id = explode(' ', $message_with_trx_id)[0];
            }elseif (strpos($total_message, 'Transaction ID ') !== false) {
                /*gp and bl format*/
                $message_with_trx_id = explode('Transaction ID ', $total_message)[1];

                $trx_id = explode(' ', $message_with_trx_id)[0];
            } elseif (strpos($total_message, 'Transaction number is ') !== false) {
                /*robi/airtel format*/
                $message_with_trx_id = explode('Transaction number is ', $total_message)[1];

                $trx_id = explode(' ', $message_with_trx_id)[0];

                if (substr($trx_id, -4) == 'Your') {
                    $trx_id = substr($trx_id, 0, -4);
                }
            } elseif (strpos($total_message, 'Transaction number ') !== false) {
                /*gp and bl format*/
                $message_with_trx_id = explode('Transaction number ', $total_message)[1];

                $trx_id = explode(' ', $message_with_trx_id)[0];
            } elseif (strpos($total_message, 'Transaction ID is ') !== false) {
                /*teletalk format*/
                $message_with_trx_id = explode('Transaction ID is ', $total_message)[1];

                $trx_id = explode(' ', $message_with_trx_id)[0];

                if (substr($trx_id, -4) == 'Your') {
                    $trx_id = substr($trx_id, 0, -4);
                }
            } else {
                $trx_id = "";
            }
        } catch (\Exception $e) {
            $trx_id = "";
        }

        return $trx_id;
    }
}


























