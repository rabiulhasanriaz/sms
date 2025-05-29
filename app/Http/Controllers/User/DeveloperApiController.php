<?php

namespace App\Http\Controllers\User;

use App\Model\User;
use App\Model\UserDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DeveloperApiController extends Controller
{
    //
    public function index(){
        $creator = User::where('id', Auth::user()->create_by)->first();
        if(!empty($creator)){
            $domain_url = $creator->userDetail->domain_name;
        }else{
            $domain_url = Auth::user()->userDetail->domain_name;
        }
    	return view('user.developerApi.developer_api', compact('domain_url'));
    }

    /*change api key*/
    public function changeApi()
    {
        $randid = time();
        $api_key = '445' . $randid . Auth::id() . $randid;
        try {
            $updateApiKey = UserDetail::where('user_id', Auth::id())->first();
            if (!$updateApiKey) {
                session()->flash('type', 'danger');
                session()->flash('message', 'can\'t find this user. try again');
                return redirect()->back();
            }
            $updateApiKey->api_key = $api_key;
            $updateApiKey->save();

            session()->flash('type', 'success');
            session()->flash('message', 'Your api key was changed successfully');
            return redirect()->back();

        }catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'something was wrong to update api key..');
            return redirect()->back();
        }
    }
}
