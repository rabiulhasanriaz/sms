<?php

namespace App\Http\Controllers\User\Flexiload;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LoadCampaign30day;
use App\Model\LoadCampaign;
use App\Model\Operator;
use App\Model\User;
use App\Model\AccSmsBalance;
use App\Model\LoadPackage;
use App\Model\LoadFlexibooksData;
use App\Model\LoadCamPending;
use App\Model\LoadCampaignId;
use Carbon\Carbon;
use Importer;
use PDF;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\Response;


class LoadController extends Controller
{
    public function flexiloadFormView()
    {
        return view('user.flexiload.flexiloadForm');
    }

    // Single load
    // Single load
    // Single load
    // Single load
    public function flexiloadFormProcess(Request $request)
    {
        // dd($request->all());
        $validated_data = $request->validate([
            'amount' => 'required|integer|min:10|max:50000',
            'number_type' => 'required',
            'targeted_number' => 'required',
            'flexipin' => 'required',
        ]);


        try {
            $flexipin = $request->flexipin;
            $targeted_number = $request->targeted_number;
            $operator = $request->operator;
            $targeted_number = \PhoneNumber::addNumberPrefix($targeted_number);
            $number_type = $request->number_type;
            $campaign_id = random_int(10, 90) . time() . random_int(1, 9);
            $remarks = $request->remarks;


            if (!\PhoneNumber::isValid($targeted_number)) {
                return redirect()->back()->with(['type' => 'danger', 'message' => 'Invalid Number']);
            }

            $user = auth()->user();
            $user_balance = \BalanceHelper::user_available_balance($user->id);
            $flexiload_price = $request->amount;

            if($operator == 'airtel'){
                $op_id = 1;
            } else if($operator == 'blink'){
                $op_id = 2;
            } else if($operator == 'gp'){
                $op_id = 3;
            } else if($operator == 'robi'){
                $op_id = 4;
            } else{
                $op_id = 5;
            }

            $amount = array();
            if ( $operator == 'robi' || $operator =='airtel' || $number_type == 1 && $flexiload_price > 1000) {
                $amount[] = $flexiload_price;
                foreach($amount as $val){
                    $res = $val;
                    $limit = array();
                    while($res > 1000){
                        if(abs($res) > 1000){
                            $limit[] = 1000;
                            $res -= 1000;
                            if(abs($res) > 900){
                                $limit[] = 900;
                                $res -= 900;
                                if(abs($res) > 800){
                                    $limit[] = 800;
                                    $res -= 800;
                                    if(abs($res) > 700){
                                        $limit[] = 700;
                                        $res -= 700;
                                        if(abs($res) > 600){
                                            $limit[] = 600;
                                            $res -= 600;
                                            if(abs($res) > 500){
                                                $limit[] = 500;
                                                $res -= 500;
                                                if(abs($res) > 400){
                                                    $limit[] = 400;
                                                    $res -= 400;
                                                    if(abs($res) > 300){
                                                        $limit[] = 300;
                                                        $res -= 300;
                                                        if(abs($res) > 200){
                                                            $limit[] = 200;
                                                            $res -= 200;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }    
                            }           
                        }
                    }
                    $limit[] = $res;
                }

                // dd($limit);

            }else{
                $limit[] = $flexiload_price;
                // dd($limit);
            }
    
            $package = LoadPackage::where('operator_id', $op_id)->where('package_price', $flexiload_price)->first();
            if(!empty($package)){
                $package_id = $package->id;
            }else{
                $package_id = 0;
            }
            // Checking flexipin
            if ($user->flexipin != $flexipin) {
                return redirect()->back()->with(['type' => 'danger', 'message' => 'Wrong Flexipin !']);
            }

            // Checking available balance
            $eligible_amount = $user->flexiload_limit + $flexiload_price;
            if ($eligible_amount >= $user_balance) {
                return redirect()->back()->with(['type' => 'danger', 'message' => 'Insufficient balance !']);
            }

            if (\BalanceHelper::check_flexiload_parent_available_balance($user->id, $flexiload_price)) {

                DB::beginTransaction();
                for ($count = 0; $count < count($limit); $count++) {
                    $load_campaign = new LoadCamPending();
                    $load_campaign->user_id = $user->id;
                    $load_campaign->sms_id = auth()->user()['id'].time().random_int(10,99);
                    if ($request->operator != '') {
                    $load_campaign->operator_id = $request->operator;
                    }else {
                        $load_campaign->operator_id = \PhoneNumber::getOperatorNameForLoadByNumber($targeted_number);
                    }
                    $load_campaign->campaign_id = $campaign_id;
                    $load_campaign->targeted_number = $targeted_number;
                    $load_campaign->owner_name = $request->dynamic_name_column ?? '';
                    $load_campaign->package_id = $package_id;
                    $load_campaign->number_type = \PhoneNumber::checkOperator($targeted_number)->id == 3 ? 1 : $number_type;
                    $load_campaign->campaign_type = '1'; // Single Flexiload
                    $load_campaign->campaign_price = $limit[$count];
                    $load_campaign->remarks = $remarks;

                    $load_campaign->status = '0';
                    // dd($load_campaign);
                    $load_campaign->save();

                }

                    // Insert to load campaign ID table
                    $campaign = new LoadCampaignId();
                    $campaign->user_id = auth()->id();
                    $campaign->campaign_id = $campaign_id;
                    $campaign->campaign_name = $request->campaign_name ?? '';
                    $campaign->total_number = 1;
                    $campaign->total_amount = $flexiload_price;
                    $campaign->save();
                

                /*debit user balance*/
                $user_position = $user->position;
                $user_id = $user->id;

                $user_det = User::where('id', $user_id)->first();
                $current_date = Carbon::now();
                while ($user_position >= 1) {
                    /*get total cost against each reseller*/
                    $price_after_commission = $flexiload_price - (($flexiload_price * $user_det->flexiload_commission) / 100);

                    AccSmsBalance::create([
                        'asb_paid_by' => $user_det->create_by,
                        'asb_pay_to' => $user_det->id,
                        'asb_pay_ref' => $campaign_id,
                        'asb_credit' => '0',
                        'asb_debit' => $price_after_commission,
                        'asb_submit_time' => $current_date,
                        'asb_target_time' => $current_date,
                        'asb_pay_mode' => '5', //*Flexiload*
                        'asb_payment_status' => '1', //*1=paid, 2=checking*
                        'asb_deal_type' => '2', //*1=deposit, 2=campaign*
                        'credit_return_type' => '0',
                    ]);
                    $user_det = User::where('id', $user_det->create_by)->first();
                    $user_position = $user_det->position;
                }
                DB::commit();

            } else {
                return redirect()->back()->with(['type' => 'danger', 'message' => 'Its seems you have a insufficient balance.Please contact with your reseller.']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['type' => 'danger', 'message' => 'Something is going Wrong' . $e->getMessage()]);
        }
        return redirect()->back()->with(['type' => 'success', 'message' => 'Successfully flexiloaded ' . $flexiload_price . ' Tk to ' . $targeted_number]);
    }



// Flexibook from a flexibook
// Flexibook from a flexibook
// Flexibook from a flexibook
// Flexibook from a flexibook
    public function flexiload_book(Request $request)
    {

        $validated_data = $request->validate([
            'flexipin' => 'required',
            'flexibook_id' => 'required',
        ]);


        $book_id = $request->flexibook_id;
        $user = auth()->user();
        $flexipin = $request->flexipin;

        // Checking flexipin
        if ($user->flexipin != $flexipin) {
            return redirect()->back()->with(['type' => 'danger', 'message' => 'Wrong Flexipin !']);
        }

        $allContacts = LoadFlexibooksData::where('load_flexibooks_id', $book_id)
            ->where('status', '1')
            ->get();

        if (isset($request->customize_amount)) {
            if(($request->customize_amount <10) || ($request->customize_amount > 50000)) {
                return redirect()->back()->with(['type' => 'danger', 'message' => 'Amount must be in 10 to 50000 taka']);
            }
            $customized_price = $request->customize_amount;
            $flexiload_total_cost = $request->customize_amount * count($allContacts);
        } else {
            $flexiload_total_cost = $allContacts->sum('amount');
        }


        $user_balance = \BalanceHelper::user_available_balance($user->id);
        $eligible_amount = $user->flexiload_limit + $flexiload_total_cost;

        if ($eligible_amount >= $user_balance) {
            return redirect()->back()->with(['type' => 'danger', 'message' => 'Insufficient balance !']);
        }

        if (\BalanceHelper::check_flexiload_parent_available_balance($user->id, $flexiload_total_cost)) {
            $campaign_id = random_int(10, 90) . time() . random_int(1, 9);
            try {
                DB::beginTransaction();


                $total_amount = 0;
                foreach ($allContacts as $contact) {
                    $load_campaign = new LoadCamPending();
                    $load_campaign->user_id = $user->id;
                    $load_campaign->sms_id = auth()->user()['id'].time().random_int(10,99);
                    if ($contact->operator != '') {
                        $load_campaign->operator_id = $contact->operator;
                    }else{
                        $load_campaign->operator_id = \PhoneNumber::getOperatorNameForLoadByNumber($contact->number);
                    }
                    $load_campaign->campaign_id = $campaign_id;
                    $load_campaign->targeted_number = $contact->number;
                    $load_campaign->owner_name = $contact->name;
                    $load_campaign->package_id = $package_id ?? '0';
                    $load_campaign->number_type = ($contact->operator == 1 || $contact->operator == 3) ? 1 : $contact->number_type;
                    $load_campaign->campaign_type = '1'; //

                    $amount_aux = isset($customized_price) ? $customized_price : $contact->amount;

                    $load_campaign->campaign_price = $amount_aux;
                    $load_campaign->remarks = $contact->remarks;

                    $load_campaign->status = '0';

                    $load_campaign->save();

                    $total_amount += $amount_aux;
                }

                // Insert to load campaign ID table
                $campaign = new LoadCampaignId();
                $campaign->user_id = auth()->id();
                $campaign->campaign_id = $campaign_id;
                $campaign->campaign_name = $request->campaign_name ?? '';
                $campaign->total_number = count($allContacts);
                $campaign->total_amount = $total_amount;

                $campaign->save();

                /*debit user balance*/
                $user_position = $user->position;
                $user_id = $user->id;

                $user_det = User::where('id', $user_id)->first();
                $current_date = Carbon::now();
                while ($user_position >= 1) {
                    /*get total cost against each reseller*/
                    $price_after_commission = $flexiload_total_cost - (($flexiload_total_cost * $user_det->flexiload_commission) / 100);

                    AccSmsBalance::create([
                        'asb_paid_by' => $user_det->create_by,
                        'asb_pay_to' => $user_det->id,
                        'asb_pay_ref' => $campaign_id,
                        'asb_credit' => '0',
                        'asb_debit' => $price_after_commission,
                        'asb_submit_time' => $current_date,
                        'asb_target_time' => $current_date,
                        'asb_pay_mode' => '5', //*Flexiload*
                        'asb_payment_status' => '1', //*1=paid, 2=checking*
                        'asb_deal_type' => '2', //*1=deposit, 2=campaign*
                        'credit_return_type' => '0',
                    ]);
                    $user_det = User::where('id', $user_det->create_by)->first();
                    $user_position = $user_det->position;
                }

            } catch (\Exception $e) {
                DB::rollback();
                dd($e->getMessage());
                return redirect()->back()->with(['type' => 'danger', 'message' => 'Something wrong ' . $e]);
            }
            Db::commit();
        } else {
            return redirect()->back()->with(['type' => 'danger', 'message' => 'In Sufficient Balance']);
        }
        return redirect()->back()->with(['type' => 'success', 'message' => 'Success']);

    }


    /*
        * Package flexiload processing
        * Inserting after checking after all validation load_cam_pending table
    */
    public function packageFormProcess(Request $request)
    {
        $validated_data = $request->validate([
            'package_id' => 'required',
            'number_type' => 'required',
            'targeted_number' => 'required',
            'flexipin' => 'required',
        ]);


        try {
            $flexipin = $request->flexipin;
            $package_id = $request->package_id;
            $targeted_number = $request->targeted_number;
            $targeted_number = \PhoneNumber::addNumberPrefix($targeted_number);
            $number_type = $request->number_type;
            $campaign_id = random_int(10, 90) . time() . random_int(1, 9);
            $remarks = $request->remarks;

            $op = \PhoneNumber::checkOperator($targeted_number)->id;

            if (!\PhoneNumber::isValid($targeted_number)) {
                return redirect()->back()->with(['type' => 'danger', 'message' => 'Invalid Number', 'op' => $op]);
            }

            $user = auth()->user();
            $user_balance = \BalanceHelper::user_available_balance($user->id);
            $flexiload_price = LoadPackage::where('id', $package_id)->first()->package_price;

            // Checking flexipin
            if ($user->flexipin != $flexipin) {
                return redirect()->back()->with(['type' => 'danger', 'message' => 'Wrong Flexipin !', 'op' => $op]);
            }

            // Checking available balance
            // $eligible_amount = $user->flexiload_limit + $flexiload_price;
            if ($flexiload_price >= $user_balance) {
                return redirect()->back()->with(['type' => 'danger', 'message' => 'Insufficient balance !', 'op' => $op]);
            }


            if (\BalanceHelper::check_flexiload_parent_available_balance($user->id, $flexiload_price)) {
                DB::beginTransaction();


                $load_campaign = new LoadCamPending();
                $load_campaign->sms_id = auth()->user()['id'].time().random_int(10,99);
                $load_campaign->user_id = $user->id;
                if ($request->operator != '') {
                $load_campaign->operator_id = $request->operator;
                }else {
                    $load_campaign->operator_id = \PhoneNumber::getOperatorNameForLoadByNumber($targeted_number);
                }
                $load_campaign->campaign_id = $campaign_id;
                $load_campaign->targeted_number = $targeted_number;
                $load_campaign->owner_name = $request->dynamic_name_column ?? '';
                $load_campaign->package_id = $package_id;
                $load_campaign->number_type = \PhoneNumber::checkOperator($targeted_number)->id == 3 ? 1 : $number_type;
                $load_campaign->campaign_type = '2'; //Package
                $load_campaign->campaign_price = $flexiload_price;
                $load_campaign->remarks = $remarks;
                $load_campaign->status = '0';

                $load_campaign->save();

                // Insert to load campaign ID table
                $campaign = new LoadCampaignId();
                $campaign->user_id = auth()->id();
                $campaign->campaign_id = $campaign_id;
                $campaign->campaign_name = $request->campaign_name ?? '';
                $campaign->total_number = 1;
                $campaign->total_amount = $flexiload_price;

                $campaign->save();


                /*debit user balance*/
                $user_position = $user->position;
                $user_id = $user->id;

                $user_det = User::where('id', $user_id)->first();
                $current_date = Carbon::now();
                while ($user_position >= 1) {
                    /*get total cost against each reseller*/
                    $price_after_commission = $flexiload_price - (($flexiload_price * $user_det->flexiload_commission) / 100);

                    AccSmsBalance::create([
                        'asb_paid_by' => $user_det->create_by,
                        'asb_pay_to' => $user_det->id,
                        'asb_pay_ref' => $campaign_id,
                        'asb_credit' => '0',
                        'asb_debit' => $price_after_commission,
                        'asb_submit_time' => $current_date,
                        'asb_target_time' => $current_date,
                        'asb_pay_mode' => '5', //*Flexiload*
                        'asb_payment_status' => '1', //*1=paid, 2=checking*
                        'asb_deal_type' => '2', //*1=deposit, 2=campaign*
                        'credit_return_type' => '0',
                    ]);
                    $user_det = User::where('id', $user_det->create_by)->first();
                    $user_position = $user_det->position;
                }
                DB::commit();

            } else {
                return redirect()->back()->with(['type' => 'danger', 'message' => 'Its seems you have a insufficient balance.Please contact with your reseller.', 'op' => $op]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['type' => 'danger', 'message' => 'Something is going Wrong', 'op' => $op]);
        }
        return redirect()->back()->with(['type' => 'success', 'message' => 'Successfully flexiloaded ' . $flexiload_price . ' Tk to ' . $targeted_number, 'op' => $op]);
    }


    public function bulkLoadForm()
    {
        return view('user.flexiload.bulkLoadForm');
    }

    /*
        * Bulk Flexiload Processing starts

    */
    public function bulkLoadFormProcess(Request $request)
    {

        $validateData = Validator::make($request->all(), [
            'sms_file' => 'required',
            'dynamic_name_column' => 'required',
            'dynamic_number_column' => 'required',
            'dynamic_amount_column' => 'required',
            'dynamic_number_type_column' => 'required',
            'dynamic_number_operator_column' => 'required',
            'flexipin' => 'required',
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withErrors($validateData)->withInput();
        }

        $remarks = $request->remarks;


        $user = auth()->user();
        $flexipin = $request->flexipin;

        // Checking flexipin
        if ($user->flexipin != $flexipin) {
            return redirect()->back()->with(['type' => 'danger', 'message' => 'Wrong Flexipin !']);
        }

        $dynamic_name_column = $request->dynamic_name_column - 1;
        $dynamic_number_column = $request->dynamic_number_column - 1;
        $dynamic_amount_column = $request->dynamic_amount_column - 1;
        $dynamic_number_type_column = $request->dynamic_number_type_column - 1;
        $dynamic_number_operator_column = $request->dynamic_number_operator_column - 1;
        $remarks = $request->remarks;


        $file = Input::file('sms_file');
        $filename = $request->file('sms_file')->getClientOriginalName();

        $fileType = \FileRead::getFileType($filename);
        $allName1 = array();
        $allContacts1 = array();
        $allAmount1 = array();
        $allNumberTypes1 = array();
        $allOperator1 = array();

        $allName = array();
        $allContacts = array();
        $allAmount = array();
        $allNumberTypes = array();
        $allOperator = array();
        $validOperator = [
            'airtel',
            'robi',
            'gp',
            'teletalk',
            'blink',
            'gpst'
        ];
        if ($fileType == "Excel") {
            $fileContents = Importer::make('Excel')->load($file)->getCollection();
            $f = 0;

            foreach ($fileContents as $fileContent) {
                $allName1[$f] = (string)$fileContent[$dynamic_name_column];
                $allContacts1[$f] = \PhoneNumber::addNumberPrefix((string)(abs((int)$fileContent[$dynamic_number_column])));
                $allAmount1[$f] = (int)$fileContent[$dynamic_amount_column];
                $allNumberTypes1[$f] = (int)$fileContent[$dynamic_number_type_column];
                $allOperator1[$f] = (string)$fileContent[$dynamic_number_operator_column];
                if (($allAmount1[$f] < 10) || ($allAmount1[$f] > 50000)) {
                    $allName1[$f] = '';
                    $allContacts1[$f] = '';
                    $allAmount1[$f] = '';
                    $allNumberTypes1[$f] = '';
                    $allOperator1[$f] = '';
                    continue;
                }
                if ($allNumberTypes1[$f] < 1 || $allNumberTypes1[$f] > 2) {
                    $allName1[$f] = '';
                    $allContacts1[$f] = '';
                    $allAmount1[$f] = '';
                    $allNumberTypes1[$f] = '';
                    $allOperator1[$f] = '';
                    continue;
                }

                $f++;
            }

            $kz = 0;
            for ($iz = 0; $iz < count($allContacts1); $iz++) {
                $amount = array();
                if ( (in_array($allOperator1[$iz],['robi']) || in_array($allOperator1[$iz],['airtel'])) || $allNumberTypes1[$iz] == 1 && $allAmount1[$iz] > 1000) {
                    // return redirect()->back()->with(['type' => 'danger', 'message' => 'Check Above Instructions. Check Robi/Airtel Amount!']);
                    $amount[] = $allAmount1[$iz];

                    foreach($amount as $val){
                        $res = $val;
                        $limit = array();
                        while($res > 1000){
                            if(abs($res) > 1000){
                                $limit[] = 1000;
                                $res -= 1000;
                                if(abs($res) > 900){
                                    $limit[] = 900;
                                    $res -= 900;
                                    if(abs($res) > 800){
                                        $limit[] = 800;
                                        $res -= 800;
                                        if(abs($res) > 700){
                                            $limit[] = 700;
                                            $res -= 700;
                                            if(abs($res) > 600){
                                                $limit[] = 600;
                                                $res -= 600;
                                                if(abs($res) > 500){
                                                    $limit[] = 500;
                                                    $res -= 500;
                                                    if(abs($res) > 400){
                                                        $limit[] = 400;
                                                        $res -= 400;
                                                        if(abs($res) > 300){
                                                            $limit[] = 300;
                                                            $res -= 300;
                                                            if(abs($res) > 200){
                                                                $limit[] = 200;
                                                                $res -= 200;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }    
                                }           
                            }
                        }
                        $limit[] = $res;
                    }

                    // dd($limit);

                    foreach($limit as $tk){
                        if( !\PhoneNumber::isValid($allContacts1[$iz]) ) continue;
                        $allName[$kz] = $allName1[$iz];
                        $allContacts[$kz] = $allContacts1[$iz];
                        $allAmount[$kz] = $tk;
                        $allNumberTypes[$kz] = $allNumberTypes1[$iz];
                        $allOperator[$kz] = $allOperator1[$iz];
                        $kz++;
                    }
                    

                    // dd($allAmount);

                }else{
                    if( !\PhoneNumber::isValid($allContacts1[$iz]) ) continue;
                    $allName[$kz] = $allName1[$iz];
                    $allContacts[$kz] = $allContacts1[$iz];
                    $allAmount[$kz] = $allAmount1[$iz];
                    $allNumberTypes[$kz] = $allNumberTypes1[$iz];
                    $allOperator[$kz] = $allOperator1[$iz];
                    $kz++;
                }
            }



            $flexiload_total_cost = array_sum($allAmount);
            $user_balance = \BalanceHelper::user_available_balance($user->id);
            $eligible_amount = $user->flexiload_limit + $flexiload_total_cost;

            if ($eligible_amount >= $user_balance) {
                return redirect()->back()->with(['type' => 'danger', 'message' => 'Insufficient balance !']);
            }
            if (\BalanceHelper::check_flexiload_parent_available_balance($user->id, $flexiload_total_cost)) {
                $campaign_id = random_int(10, 90) . time() . random_int(1, 9);
                try {
                    DB::beginTransaction();

                    for ($it = 0; $it < count($allAmount); $it++) {
                        // dd($allContacts);
                        $load_campaign = new LoadCamPending();
                        $load_campaign->sms_id = auth()->user()['id'].time().random_int(10,99);
                        $load_campaign->user_id = $user->id;
                        if (in_array($allOperator[$it],$validOperator)) {
                            $load_campaign->operator_id = $allOperator[$it];
                        }else{
                            $load_campaign->operator_id = \PhoneNumber::getOperatorNameForLoadByNumber($allContacts[$it]);
                        }
                        $load_campaign->campaign_id = $campaign_id;
                        $load_campaign->owner_name = $allName[$it];
                        $load_campaign->targeted_number = $allContacts[$it];
                        
                        $load_campaign->number_type = \PhoneNumber::checkOperator($allContacts[$it])->id == 3 ? 1 : $allNumberTypes[$it];
                        $load_campaign->campaign_type = '3'; // Bulk load
                        $load_campaign->campaign_price = $allAmount[$it];

                        if($load_campaign->operator_id == 'airtel'){
                            $op_id = 1;
                        } else if($load_campaign->operator_id == 'blink'){
                            $op_id = 2;
                        } else if($load_campaign->operator_id == 'gp'){
                            $op_id = 3;
                        } else if($load_campaign->operator_id == 'robi'){
                            $op_id = 4;
                        } else{
                            $op_id = 5;
                        }
                
                        $package = LoadPackage::where('operator_id', $op_id)->where('package_price', $load_campaign->campaign_price)->first();
                        if(!empty($package)){
                            $package_id = $package->id;
                        }else{
                            $package_id = 0;
                        }

                        $load_campaign->package_id = $package_id;
                        $load_campaign->remarks = $remarks;

                        $load_campaign->status = '0';

                        $load_campaign->save();
                    }

                    // Insert to load campaign ID table
                    $campaign = new LoadCampaignId();
                    $campaign->user_id = auth()->id();
                    $campaign->campaign_id = $campaign_id;
                    $campaign->campaign_name = $request->campaign_name ?? '';
                    $campaign->total_number = count($allContacts);
                    $campaign->total_amount = $flexiload_total_cost;

                    $campaign->save();

                    /*debit user balance*/
                    $user_position = $user->position;
                    $user_id = $user->id;

                    $user_det = User::where('id', $user_id)->first();
                    $current_date = Carbon::now();

                    while ($user_position >= 1) {
                        /*get total cost against each reseller*/
                        $price_after_commission = $flexiload_total_cost - (($flexiload_total_cost * $user_det->flexiload_commission) / 100);

                        AccSmsBalance::create([
                            'asb_paid_by' => $user_det->create_by,
                            'asb_pay_to' => $user_det->id,
                            'asb_pay_ref' => $campaign_id,
                            'asb_credit' => '0',
                            'asb_debit' => $price_after_commission,
                            'asb_submit_time' => $current_date,
                            'asb_target_time' => $current_date,
                            'asb_pay_mode' => '5', //*Flexiload*
                            'asb_payment_status' => '1', //*1=paid, 2=checking*
                            'asb_deal_type' => '2', //*1=deposit, 2=campaign*
                            'credit_return_type' => '0',
                        ]);
                        $user_det = User::where('id', $user_det->create_by)->first();
                        $user_position = $user_det->position;
                    }


                } catch (\Exception $e) {
                    DB::rollback();
                    dd($e->getMessage());
                    return redirect()->back()->with(['type' => 'danger', 'message' => 'Something wrong ' . $e]);
                }
                Db::commit();
            } else {
                return redirect()->back()->with(['type' => 'danger', 'message' => 'In Sufficient Balance']);
            }

            return redirect()->back()->with(['type' => 'success', 'message' => 'All requests submitted successfully']);

        } else {
            $res = new stdClass();
            $res->error = 'Invalid file...';
            return redirect()->back()->with(['type' => 'danger', 'message' => 'Invalid File Extention']);
        }
    }

    public function packageForm()
    {
        $operators = Operator::all();
        $allPackages = LoadPackage::where('status', 1)->get();
        $packages = $allPackages->groupBy('operator_id');

        return view('user.flexiload.create', ['operators' => $operators, 'packages' => $packages]);
    }


    public function showPackagesByAjax(Request $request)
    {
        try {
            if (!empty($request->operator) && !empty($request->type)) {
                $packages = LoadPackage::where('operator_id', $request->operator)
                    ->where('package_category', $request->type)
                    ->orderByRaw("CAST(validity as UNSIGNED) ASC")
                    ->orderBy('package_price', 'ASC')
                    ->get();

                return view('user.flexiload._ajax_packages_data', ['packages' => $packages]);

            } elseif (!empty($request->operator) && empty($request->type)) {
                $packages = LoadPackage::where('operator_id', $request->operator)
                    ->orderByRaw("CAST(validity as UNSIGNED) ASC")
                    ->orderBy('package_price', 'ASC')
                    ->get();

                return view('user.flexiload._ajax_packages_data', ['packages' => $packages]);

            } else {
                return response()->json(['code' => 400]); //error code 1
            }
        } catch (\Exception $e) {
            return response()->json(['code' => 401]); //error code 2
        }
    }


    // History related methods
    // History related methods
    // History related methods
    public function getCampaignHistoryByAjax(Request $request)
    {
        $allData = LoadCampaign30Day::where('campaign_id', $request->campaign_id)->get();
        return view('user.flexiload.campaignDataByAjax', ['allData' => $allData]);
    }

    public function downloadFlexiReport(Request $request)
    {
        $allData = LoadCampaign30Day::where('campaign_id', $request->campaign_id)
            ->orderBy('created_at', 'desc')
            ->get();
            // dd($allData);
            // return view('user.flexiload.reportPdf', ['allData' => $allData]);
        $pdf = PDF::loadView('user.flexiload.reportPdf', ['allData' => $allData]);

        return $pdf->download('flexiload_history.pdf');
    }

    public function history()
    {
        $loads = LoadCampaignId::with('package')->where('user_id', auth()->id())
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->orderBy('id', 'desc')
            ->get();

        return view('user.flexiload.history', ['loads' => $loads]);
    }

    public function history_archieve(Request $request)
    {
        if (!isset($request->year) && !isset($request->month)) {
            $loads = LoadCampaignId::with('package')->where('user_id', auth()->id())->where('created_at', '<', Carbon::now()->startOfMonth())->orderBy('id', 'desc')->get();
            // dd($loads);
        } else {
            $year = $request->year;
            $month = $request->month;
            
            $loads = LoadCampaignId::with('package')->where('user_id', auth()->id())
                ->whereYear('created_at', '=', $year)
                ->whereMonth('created_at', '=', $month)
                ->orderBy('id', 'desc')
                ->get();
            // dd($loads);
        }

        return view('user.flexiload.history_archieved', ['loads' => $loads]);
    }

    public function getCurrentMonthyCampaignHistoryByAjax(Request $request)
    {
        $all = LoadCamPending::where('campaign_id',$request->campaign_id)->get();
        
        $allData = LoadCampaign30day::where('campaign_id', $request->campaign_id)->get();
        return view('user.flexiload.currentMonthCampaignDataByAjax', ['allData' => $allData, 'all' => $all]);
    }

    public function downloadCurrentMonthFlexiReport(Request $request)
    {
        $allData = LoadCampaign30day::where('campaign_id', $request->campaign_id)
            
            ->orderBy('created_at', 'desc')
            ->get();
        // return view('user.flexiload.reportPdf', ['allData' => $allData]);
        $pdf = PDF::loadView('user.flexiload.currentMonthReportPdf', ['allData' => $allData]);

        return $pdf->download('flexiload_history.pdf');
    }

    public function package_history(Request $request)
    {
        $packages = LoadCampaign30day::select(DB::raw("SUM(campaign_price) as total_price"), DB::raw("count(*) as total_package"), 'operator_id')
        ->where('user_id', auth()->id())
        ->where('package_id', '!=', '0')
        ->groupBy('operator_id')
        ->get();

        return view('user.flexiload.package_history', ['packages' => $packages]);
    }

}
