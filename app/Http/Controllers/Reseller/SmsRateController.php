<?php

namespace App\Http\Controllers\reseller;

use App\Model\AccSmsRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Model\User;

class SmsRateController extends Controller
{
    //
    public function edit($id){
        $user = User::where(['create_by' => Auth::id(), 'id' => $id])->first();
        if($user) {
            $smsRates = AccSmsRate::with('country', 'operator')->where('user_id', $id)->get();
            if ($smsRates) {
                return view('reseller.users.user_price_view', compact('smsRates', 'user'));
            } else {
                session()->flash('type', 'danger');
                session()->flash('message', 'unknown user. please try again.....');
                return redirect()->route('reseller.user.index');
            }
        }else{
            session()->flash('type', 'danger');
            session()->flash('message', 'unknown user. please try again.....');
            return redirect()->route('reseller.user.index');
        }
    }

    public function update(Request $request, $id)
    {

        try{
            $smsRate = AccSmsRate::where('id', $id)->first();
            if($smsRate){
                $checkUser = User::where(['create_by' => Auth::id(), 'id' => $smsRate->user_id])->first();
                if($checkUser) {
                    $smsRate->asr_masking = $request->masking_price;
                    $smsRate->asr_nonmasking = $request->non_masking_price;
                    $smsRate->asr_dynamic = $request->dynamic_price;

                    $smsRate->save();

                    session()->flash('type', 'success');
                    session()->flash('message', 'successfully update price');
                    return redirect()->back();

                }else{
                    session()->flash('type', 'danger');
                    session()->flash('message', 'unknown user. please try again.....');
                    return redirect()->route('reseller.user.index');
                }
            }else{
                session()->flash('type', 'danger');
                session()->flash('message', 'unknown sms balance. please try again.....');
                return redirect()->route('reseller.user.index');
            }

        }catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to update price. please try again.....');
            return redirect()->back();
        }
    }
}
