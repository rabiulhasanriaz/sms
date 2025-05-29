<?php

namespace App\Http\Controllers\reseller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\AccSmsBalance;
use App\Model\User;
use App\Model\EmployeeUserCommission;
use App\Model\EmployeeUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BalanceController extends Controller
{
    /*show user credit balance form*/
    public function cdtCreate()
    {
        $resellers = User::where(['create_by'=> Auth::id()])->orderBy('company_name','asc')->get();
        $paymentable_balance = \BalanceHelper::reseller_paymentable_balance(Auth::id());

        return view('reseller.balance.add_fund_credit', compact('resellers', 'paymentable_balance'));
    }

    /*store reseller credit*/
    public function cdtStore(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            "user_id" => ['required'],
            "credit_ammount" => ['required'],
            "payment_reference" => ['required'],
            "payment_method" => ['required'],
        ]);

        if($validateData->fails()){
            return redirect()->back()->withInput()->withErrors($validateData);
        }

        if($request->target_time==null){
            $target_time = Carbon::now();
        }else{
            $target_time = date("Y-m-d h:i:s",strtotime($request->target_time));
        }

        if($request->credit_ammount>\BalanceHelper::reseller_paymentable_balance(Auth::id())){
            session()->flash('type', 'danger');
            session()->flash('message', 'Warning ! You can\'t pay more then your paymentable balance....!');

            return redirect()->back();
        }

        // Employee Commission calculation Editing start
           /* $data = \BalanceHelper::get_employee_commission($request->user_id, $request->credit_ammount);
               
               $add_commission = EmployeeUserCommission::create([
                   'eu_id' => $data['employee_id'],
                   'eu_ref_id' => '0',
                   'euc_credit' => $data['commission_amount'],
                   'euc_debit' => 0,
                   'euc_status' => 1,
                   ]);*/
        

        try{
            DB::transaction(function () use ($request, $target_time) {
            
            
                $added_credit = AccSmsBalance::create([
                    'asb_paid_by' => Auth::user()->id,
                    'asb_pay_to' => $request->user_id,
                    'asb_pay_ref' => $request->payment_reference,
                    'asb_credit' => $request->credit_ammount,
                    'asb_debit' => '0',
                    'asb_submit_time' => Carbon::now(),
                    'asb_target_time' => $target_time,
                    'asb_pay_mode' => $request->payment_method,
                    'asb_payment_status' => '1',
                    'asb_deal_type' => '1',
                ]);

               $data = \BalanceHelper::get_employee_commission($request->user_id, $request->credit_ammount);
               $comission = User::where('id',$request->user_id)
                                ->first();
                $total = ($request->credit_ammount * $comission->flexi_emp_comission)/100;
               if ( $data != 0 ){
                   $add_commission = EmployeeUserCommission::create([
                       'eu_id' => $comission->employee_user_id,
                       'eu_ref_id' => $added_credit->id,
                       'euc_credit' => $total,
                       'euc_debit' => 0,
                       'euc_status' => 1,
                       ]);
               }

            });

            // Editing end

            session()->flash('type', 'success');
            session()->flash('message', 'successfully added credit balance..');
            return redirect()->back();

        }
        catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to add balance..');
            return redirect()->back();
        }
    }


    public function dbtCreate()
    {
        $resellers = User::where(['create_by'=> Auth::id()])->get();
        return view('reseller.balance.add_fund_debit', compact('resellers'));
    }


    /*store reseller debited amount*/
    public function dbtStore(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            "user_id" => ['required'],
            "debit_amount" => ['required'],
            "payment_reference" => ['required'],
        ]);

        if($validateData->fails()){
            return redirect()->back()->withInput()->withErrors($validateData);
        }

        $checkUser = User::where(['id'=>$request->user_id, 'create_by'=>Auth::id()])->first();
        if(!$checkUser){
            session()->flash('type', 'danger');
            session()->flash('message', 'Warning! You can\'t get balance from user who is not under you....!');
            return redirect()->back();
        }
        elseif ($request->debit_amount>\BalanceHelper::user_available_balance($request->user_id)){
            session()->flash('type', 'danger');
            session()->flash('message', 'Warning ! You can\'t withdraw more then this user balance....!');

            return redirect()->back();
        }

        try{
            DB::transaction(function () use ($request) {
                
                $added_debit = AccSmsBalance::create([
                    'asb_paid_by' => Auth::user()->id,
                    'asb_pay_to' => $request->user_id,
                    'asb_pay_ref' => $request->payment_reference,
                    'asb_credit' => '0',
                    'asb_debit' => $request->debit_amount,
                    'asb_submit_time' => Carbon::now(),
                    'asb_target_time' => Carbon::now(),
                    'asb_pay_mode' => '1',
                    'asb_payment_status' => '1',
                    'asb_deal_type' => '2',
                ]);

                $data = \BalanceHelper::get_employee_commission($request->user_id, $request->debit_amount);
                
                if ( $data != 0 ){
                    $removed_commission = EmployeeUserCommission::create([
                    'eu_id' => $data['employee_id'],
                    'eu_ref_id' => $added_debit->id,
                    'euc_credit' => 0,
                    'euc_debit' => $data['commission_amount'],
                    'euc_status' => 2,
                    ]);
                }
            
            });

            session()->flash('type', 'success');
            session()->flash('message', 'successfully debited balance..');
            return redirect()->back();
        }
        catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to debit balance..'.$e->getMessage());
            return redirect()->back();
        }

    }


    public function show($id)
    {
	    $checkUser = User::where(['create_by'=>Auth::id(), 'id'=>$id])->first();
	    if($checkUser) {
            $SmsBalances = AccSmsBalance::where('asb_pay_to', $id)->orderBy('id', 'asc')->get();
            $user = User::where('id', $id)->first();
            return view('reseller.users.user_transaction_history', compact('SmsBalances', 'user'));
        }elseif ($id==Auth::id()){
            $SmsBalances = AccSmsBalance::where('asb_pay_to', $id)->orderBy('id', 'asc')->get();
            $user = User::where('id', $id)->first();
            return view('reseller.users.user_transaction_history', compact('SmsBalances', 'user'));
        }
        else{
            $users = User::with('userDetail')->where('create_by', Auth::id())->whereNotIn('status', ['3'])->get();
            session()->flash('type', 'danger');
            session()->flash('message', 'Unknown user...!');
            return redirect()->route('reseller.user.index', compact('users'));
        }
    }

    public function totalTransactionHistory()
    {
        $resellers = User::where(['create_by'=> Auth::id()])->get();
        return view('reseller.balance.transaction_history', compact('resellers'));
    }
}
