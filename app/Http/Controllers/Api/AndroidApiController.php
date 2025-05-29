<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\UserDetail;
use App\Model\LoadCamPending;
use App\Model\LoadCampaignId;
use App\Model\AccSmsBalance;
use App\Model\LoadCampaign30day;
use DB;
use Carbon\Carbon;
use Auth;

class AndroidApiController extends Controller
{
    public function androidApiLogin(Request $request){
        // dd($request->all());
        $mobile = $request->mobile;
        $password = $request->password;
        if (Auth::attempt(['cellphone' => $mobile, 'password' => $password])) {

            $response = array(
                'msg' => 'Sucessfully Login',
                'mobile' => Auth::user()->cellphone,
                'api_key' => Auth::user()->userDetail->flexi_api_key,
                'status' => '2'
            );
            return response()->json([$response], 200);
        }else {
            $response = array(
                'msg' => "Data Doesn't Match",
                'status' => '4'
            );

            return response()->json([$response], 200);
        }

    }

    public function androidFlexiloadApi(Request $request){
        // dd($request->all());
        $pin = $request->flexipin;
        $mobile = $request->mobile;
        // dd($mobile);

        $user = User::where('cellphone',$mobile)->first();
        // dd($user->flexipin);

        if ($user->flexipin == $pin) {

            $response = array(
                'msg' => 'Flexiload Send Successfully',
                'api_key' => $user->userDetail->flexi_api_key,
                'status' => '2'
            );


            return response()->json([$response], 200);


        }else {
            $response = array(
                'msg' => "Data Doesn't Match",
                'status' => '4'
            );

            return response()->json([$response], 200);
        }
    }

    public function apdroidFlexiReport(Request $request){
       $mobile = $request->mobile;

       $user = User::where('cellphone',$mobile)->first();
       $loadReports = LoadCampaign30day::where('user_id',$user->id)->get();

       if (!empty($loadReports)) {

         return response()->json($loadReports, 200);

       }else {
           $response = array(
               'msg' => "Data Doesn't Match",
               'status' => '4'
           );

           return response()->json([$response], 200);
       }
    }
}
