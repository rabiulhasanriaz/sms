<?php

namespace App\Http\Controllers\Auth;

use App\Model\PhonebookCategory;
use App\Model\User;
use App\Model\UserDetail;
use App\Model\SenderIdUserDefault;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    //
    public function index()
    {
        $user_domain = $_SERVER['HTTP_HOST'];
        $userInfo = UserDetail::where('domain_name',$user_domain)->first();
        if($userInfo){
            $user_logo = $userInfo->logo;
        }else{
            $user_logo = '../default.png';
        }
        return view('login', compact('user_logo'));
    }

    public function processLogin(Request $request)
    {
        if(is_numeric($request->email)){
            $getEmail = User::where('cellphone', $request->email)->first();
            if($getEmail){
                $email = $getEmail->email;
            }else{
                session()->flash('message', 'login credential was wrong...');
                return redirect()->back();
            }
        }else{
            $email = $request->email;
        }

        if (Auth::attempt(['email' => $email, 'password' => $request->password])) {
            UserDetail::where('user_id', '=', Auth::id())->update(['last_log_ip' => $request->ip(), 'last_log_os' => \OtherHelpers::getOS()]);
            if (Auth::user()->status == '1') {
//                $phonebookCategories = PhonebookCategory::where('user_id', Auth::id())->get();
//                session(['phonebookCategories' => $phonebookCategories]);
//                session()->put(['phonebookCategories' => $phonebookCategories]);
                $user = Auth::user();
                $user->login_status = 1;
                $user->last_login_time = Carbon::now();
                $user->last_active_time = Carbon::now();
                $user->save();
                return redirect('/home');
            } elseif (Auth::user()->status == '2') {
                Auth::logout();
                session()->flash('message', 'Your account was suspended');
                return redirect()->back();
            } else {
                Auth::logout();
                session()->flash('message', 'Your account was expired');
                return redirect()->back();
            }
        } else {
            session()->flash('message', 'login credential was wrong...');
            return redirect()->back();
        }
    }


    public function logout()
    {
        if(Auth::check()) {

            // Deactivating Login status
            $user = Auth::user();
            $user->login_status = 0;
            $user->save();

            if (Auth::user()->role != '5') {
                $logout_url = Auth::user()->userDetail['logout_url'];
            } else {
                $userParent = User::where('id', Auth::user()->create_by)->first();
                $logout_url = $userParent->userDetail['logout_url'];
            }


            Auth::logout();
            if ($logout_url != null) {
                return redirect($logout_url);
            } else {
                return redirect()->back();
            }
        }else{
            return redirect()->back();
        }

        if(Auth::guard('employee')->check()){
            Auth::guard('employee')->logout();
        }
    }


    /*forgot password*/
    public function forgotPassword(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'verification_number' => 'required',
        ]);

        if($validateData->fails()){
            session()->flash('message', 'require your number for reset password');
            return redirect()->back();
        }
        $checkUser = User::where('cellphone', $request->verification_number)->first();
        if($checkUser){
            /*send sms to created user*/
            $message = "Your password is: ".$checkUser->userDetail->user_p;
            $message = rawurlencode($message);
            $number = '88'.$checkUser->cellphone;
            // $sender_id = "8804445604441";

            $user_default_sender_id = SenderIdUserDefault::where('user_id', $checkUser->id)->first();

            $number = '88'.$checkUser->cellphone;
            $sender_id = $user_default_sender_id->sender->sir_sender_id;

            // dd($sender_id);

            $client = new Client();
            $url = config('app.url')."/api/v1/send?api_key=".$checkUser->userDetail->api_key."&contacts=".$number."&senderid=".$sender_id."&msg=".$message."&for_registration=resellerToUser";


            $res = $client->request('GET', $url);
            // $ret = $res->getBody();

            session()->flash('message', 'sending password to your phone. please check it. it can take up-to 5 minutes.');
            return redirect()->back();

        }else{
            session()->flash('message', 'can\'t find your account. please provide your mobile number to reset password');

            return redirect()->back();
        }
    }


    public function update_login_status(Request $request)
    {
        if(Auth::check()) {
            $user = Auth::user();

            if ($user->last_active_time > Carbon::now()->subMinute()) {
                if ($request->currentSecond <= 300) {

                    $user->login_status = 1;

                    $user->last_active_time = Carbon::now();
                    $user->save();
                }
            } else {
                if ($request->currentSecond > 300) {
                    $user->login_status = 2;
                } else {
                    $user->login_status = 1;
                }

                $user->last_active_time = Carbon::now();
                $user->save();
            }

        }
        return response()->json(['code'=>200]);
    }

    public function maintenance(){
        return view('maintain');
    }
}
