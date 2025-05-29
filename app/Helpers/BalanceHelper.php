<?php

use App\Model\User;
use App\Model\AccSmsBalance;
use App\Model\AccSmsRate;
use App\Model\EmployeeUser;
use App\Model\UserDetail;
use App\Model\SmsCampaign_24h;
use App\Model\LoadCampaign30day;
use Carbon\Carbon;


class BalanceHelper
{

    /*available balance of a user*/
    public static function user_available_balance($user_id)
    {
        try {
            $checkUser = User::where(['id' => $user_id])->first();
            if ($checkUser) {
                $customerCredit = AccSmsBalance::select(DB::raw('SUM(asb_credit - asb_debit) as total'))->where('asb_pay_to',$user_id)->get();
                foreach ($customerCredit as $val) {
                    return $val->total;
                }
                // return $customerCredit;
                // return $customerCredit - $customerDebit;
            }
            return '0';
        } catch (\Exception $e) {
            return '0';
        }
    }

   

    /*public static function check_parents_available_balance($user_id, $total_cost)
    {
        $user = User::where('id', $user_id)->first();
        if ($user->position == 1) {
            return true;
        } else {
            $position = $user->position;
            $parent_id = $user->create_by;

            while ($position >= 1) {
                if (self::user_available_balance($parent_id) < $total_cost) {
                    return false;
                } else {
                    $user = User::where('id', $parent_id)->first();

                    if ($user->position == 1) {
                        return true;
                    }
                    $position = $user->position;
                    $parent_id = $user->create_by;
                }
            }

            return true;
        }
    }*/

    public static function check_parents_available_balance($user_id, $sms_number, $validUniqueNumbers, $isMasking)
    {
        $user = User::where('id', $user_id)->first();

        if ($user->position == 1) {
            return true;
        } else {
            $position = $user->position;
            $parent_id = $user->create_by;
                          
            while ($position >= 1) {
                $total_cost = self::campaignTotalCost($sms_number, $validUniqueNumbers, $isMasking, $parent_id);
                if (self::user_available_balance($parent_id) < $total_cost) {
//                    return self::user_available_balance($parent_id)." ".$total_cost;
                    return false;
                } else {
                    $user = User::where('id', $parent_id)->first();

                    if ($user->position == 1) {
//                        return self::user_available_balance($parent_id)." ".$total_cost;
                        return true;
                    }
                    $position = $user->position;
                    $parent_id = $user->create_by;
                    $user_id = $user->id;
                }
            }

            return true;
        }
    }

    public static function check_dynamic_parents_available_balance($user_id, $valid_numbers, $valid_messages, $isMasking)
    {
        $user = User::where('id', $user_id)->first();
        if ($user->position == 1) {
            return true;
        } else {
            $position = $user->position;
            $parent_id = $user->create_by;
            $validNumbers = $valid_numbers;
            $validMessages = $valid_messages;
            while ($position >= 1) {
                $total_cost = 0;
                $total_sms_number = 0;
                for ($i = 0; $i < count($validNumbers); $i++) {

                    if (\SmsHelper::is_unicode($validMessages[$i])) {
                        $smsType = 'unicode'; //unicode
                        $sms_number = \SmsHelper::unicode_sms_count($validMessages[$i]);

                    } else {
                        $smsType = 'text'; //text
                        $sms_number = \SmsHelper::text_sms_count($validMessages[$i]);
                    }
                    $smsCost = \BalanceHelper::singleSmsCost($sms_number, $validNumbers[$i], $isMasking, Auth::id());
                    $total_cost = $total_cost + $smsCost;
                    $total_sms_number = $total_sms_number + $sms_number;
                }
                if (self::user_available_balance($parent_id) < $total_cost) {
                    return false;
                } else {
                    $user = User::where('id', $parent_id)->first();

                    if ($user->position == 1) {
                        return true;
                    }
                    $position = $user->position;
                    $parent_id = $user->create_by;
                    $user_id = $user->id;
                }
            }

            return true;
        }
    }


    /*paymentable balance of a reseller*/
    public static function reseller_paymentable_balance($reseller_id)
    {
        try {
            $checkReseller = User::with('userDetail')->where(['id' => $reseller_id, 'role' => '4'])->first();
            if ($checkReseller) {
                $reseller_limit = $checkReseller->userDetail->limit;
                $reseller_available_balance = static::user_available_balance($checkReseller->id);
                $reseller_customers = User::where('create_by', $checkReseller->id)->get();
                $reseller_customer_available_balance = 0;
                foreach ($reseller_customers as $reseller_customer) {
                    $reseller_customer_available_balance += static::user_available_balance($reseller_customer->id);
                }
                $reseller_paymentable_balance = ($reseller_limit + $reseller_available_balance) - $reseller_customer_available_balance;

                return $reseller_paymentable_balance;

            }
            return '0';
        } catch (\Exception $e) {
            return '0';
        }
    }


    /*user total credit balance*/
    public static function user_total_credit($user_id)
    {
        try {
            $checkUser = User::where(['id' => $user_id])->first();
            if ($checkUser) {
                $customerCredit = AccSmsBalance::where(['asb_pay_to' => $user_id])->sum('asb_credit');
                return $customerCredit;
            }
            return '0';
        } catch (\Exception $e) {
            return '0';
        }
    }

    /*user total debit balance*/
    public static function user_total_debit($user_id)
    {
        try {
            $checkUser = User::where(['id' => $user_id])->first();
            if ($checkUser) {
                $customerDebit = AccSmsBalance::where(['asb_pay_to' => $user_id])->sum('asb_debit');
                return $customerDebit;
            }
            return '0';
        } catch (\Exception $e) {
            return '0';
        }
    }


    /*get total cost of a campaign*/
    public static function campaignTotalCost($sms_count_number, $numbers, $isMasking, $user_id)
    {
        $countOperators = PhoneNumber::countOperator($numbers);
        $totalCost = 0;
        if ($isMasking == true) {
            foreach ($countOperators as $operatorId => $numberOfOperator) {
                /*get user sms rates*/
                $sms_rate = AccSmsRate::select('asr_masking')->where(['user_id' => $user_id, 'operator_id' => $operatorId])->first();
                $totalCost = $totalCost + ($sms_rate->asr_masking * $numberOfOperator);
            }
        } else {
            foreach ($countOperators as $operatorId => $numberOfOperator) {
                /*get user sms rates*/
                $sms_rate = AccSmsRate::select('asr_nonmasking')->where(['user_id' => $user_id, 'operator_id' => $operatorId])->first();
                $totalCost = $totalCost + ($sms_rate->asr_nonmasking * $numberOfOperator);
            }
        }

        $totalCost = $totalCost * $sms_count_number;
        return $totalCost;
    }


    /*get  cost of a single sms*/
    public static function singleSmsCost($sms_count_number, $number, $isMasking, $user_id)
    {
        $operator = \PhoneNumber::checkOperator($number);
        if ($isMasking == true) {
            /*get user sms rates*/
            $sms_rate = AccSmsRate::select('asr_masking')->where(['user_id' => $user_id, 'operator_id' => $operator->id])->first();
            $cost = $sms_rate->asr_masking;
        } else {
            /*get user sms rates*/
            $sms_rate = AccSmsRate::select('asr_nonmasking')->where(['user_id' => $user_id, 'operator_id' => $operator->id])->first();
            $cost = $sms_rate->asr_nonmasking;
        }
        $smsCost = $cost * $sms_count_number;
        return $smsCost;
    }


    /*add debit balance*/
    public static function add_debit_balance($pay_by, $pay_to, $pay_ref, $debit_balance, $pay_mode, $payment_status, $deal_type)
    {

    }

    // Get employee employee commission ( Credit commission )
    public static function get_employee_commission($user_id, $credit_amount) {
        
        $user = User::find($user_id);

        $employee = $user['employee_user_id'];

        if ( ( !empty($employee)) && ($employee != null)){
            $commission = EmployeeUser::where('id',$employee)->first()->commission;

            $data['commission_amount'] = ( $credit_amount * $commission ) / 100;
            $data['employee_id'] = $employee;

            return $data;
        }else{
            return 0;
        }

    }

    public static function last_transaction_date($user_id) {
        $last_transaction_date = AccSmsBalance::where('asb_pay_to', $user_id)->whereIn('asb_pay_mode', [1,2,3])->orderBy('asb_submit_time', 'desc')->first();
        if(!empty($last_transaction_date)) {
            $last_tr_date = $last_transaction_date->asb_submit_time;
            return $last_tr_date;
        } else {
            return "No Record Found";
        }

    }

    public static function getEmployeeBalance($cId) {
        $total_credit = DB::table('employee_user_commissions')->where('eu_id', $cId)->sum('euc_credit');
        $total_debit = DB::table('employee_user_commissions')->where('eu_id', $cId)->sum('euc_debit');

        return ($total_credit - $total_debit);
    }
    
    public static function getCredit($cId){
        return DB::table('employee_user_commissions')->where('eu_id', $cId)->sum('euc_credit');
    }

    public static function getDebit($cId){
        return DB::table('employee_user_commissions')->where('eu_id', $cId)->sum('euc_debit');
    }
// Flexiload 
    public static function check_flexiload_parent_available_balance($user_id, $flexiload_price)
    {
        $user = User::where('id', $user_id)->first();
        if ($user->position == 1) {
            return true;
        } else {
            $position = $user->position;
            $parent_id = $user->create_by;

            while ($position >= 1) {
                $parent_user = User::where('id', $parent_id)->first();
                // Calcaulating charge againstst this reseller
                $price_after_commission = $flexiload_price -( ($flexiload_price * $parent_user->flexiload_commission) / 100);

                if (self::user_available_balance($parent_id) < $price_after_commission) {
                    return false;
                } else {
                    // $parent_user = User::where('id', $parent_id)->first();
                    if ($parent_user->position == 1) {
                        return true;
                    }
                    $position = $parent_user->position;
                    $parent_id = $parent_user->create_by;
                }
            }
            return true;
        }
    }

    public static function check_flexiload_employee_available_balance($user_id, $flexiload_price)
    {
        $user = EmployeeUser::where('id', $user_id)->first();
        if ($user->status == 1) {
            return true;
        } else {
            $position = $user->status;
            $parent_id = $user->create_by;

            while ($position >= 1) {
                $parent_user = EmployeeUser::where('id', $parent_id)->first();
                // Calcaulating charge againstst this reseller
                $price_after_commission = $flexiload_price -( ($flexiload_price * $parent_user->flexiload_commission) / 100);

                if (self::user_available_balance($parent_id) < $price_after_commission) {
                    return false;
                } else {
                    // $parent_user = User::where('id', $parent_id)->first();
                    if ($parent_user->status == 1) {
                        return true;
                    }
                    $position = $parent_user->status;
                    $parent_id = $parent_user->create_by;
                }
            }
            return true;
        }
    }

    public static function sms_cost($user_id){
        return SmsCampaign_24h::where('user_id',$user_id)->sum('sct_sms_cost');
    }
    public static function flexi_cost($user_id){
        return LoadCampaign30day::where('user_id',$user_id)->sum('campaign_price');
    }



    public static function campaignDesktopTotalCost($sms_count_number, $numbers, $user_id)
    {
        $countOperators = PhoneNumber::countOperator($numbers);
        $totalCost = 0;
        // if ($isMasking == true) {
        //     foreach ($countOperators as $operatorId => $numberOfOperator) {
        //         /*get user sms rates*/
        //         $sms_rate = AccSmsRate::select('asr_masking')->where(['user_id' => $user_id, 'operator_id' => $operatorId])->first();
        //         $totalCost = $totalCost + ($sms_rate->asr_masking * $numberOfOperator);
        //     }
        // } else {
            foreach ($countOperators as $operatorId => $numberOfOperator) {
                /*get user sms rates*/
                $sms_rate = AccSmsRate::select('asr_dynamic')->where(['user_id' => $user_id, 'operator_id' => $operatorId])->first();
                $totalCost = $totalCost + ($sms_rate->asr_dynamic * $numberOfOperator);
            }
        // }

        $totalCost = $totalCost * $sms_count_number;
        return $totalCost;

    }

    public static function check_parents_Desktop_available_balance($user_id, $sms_number, $validUniqueNumbers)
    {
        $user = User::where('id', $user_id)->first();
        if ($user->position == 1) {
            return true;
        } else {
            $position = $user->position;
            $parent_id = $user->create_by;
            while ($position >= 1) {
                $total_cost = self::campaignDesktopTotalCost($sms_number, $validUniqueNumbers, $parent_id);
                if (self::user_available_balance($parent_id) < $total_cost) {
//                    return self::user_available_balance($parent_id)." ".$total_cost;
                    return false;
                } else {
                    $user = User::where('id', $parent_id)->first();

                    if ($user->position == 1) {
//                        return self::user_available_balance($parent_id)." ".$total_cost;
                        return true;
                    }
                    $position = $user->position;
                    $parent_id = $user->create_by;
                    $user_id = $user->id;
                }
            }

            return true;
        }
    }


    public static function singleSmsDesktopCost($sms_count_number, $number, $user_id)
    {
        $operator = \PhoneNumber::checkOperator($number);
        // if ($isMasking == true) {
        //     /*get user sms rates*/
        //     $sms_rate = AccSmsRate::select('asr_masking')->where(['user_id' => $user_id, 'operator_id' => $operator->id])->first();
        //     $cost = $sms_rate->asr_masking;
        // } else {
            /*get user sms rates*/
            $sms_rate = AccSmsRate::select('asr_dynamic')->where(['user_id' => $user_id, 'operator_id' => $operator->id])->first();
            $cost = $sms_rate->asr_dynamic;
        // }
        $smsCost = $cost * $sms_count_number;
        return $smsCost;
    }


}
