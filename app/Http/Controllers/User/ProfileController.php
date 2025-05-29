<?php

namespace App\Http\Controllers\User;

use App\Model\User;
use App\Model\UserDetail;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class ProfileController extends Controller
{
    /*show user profile*/
    public function showProfile(){
    	return view('user.profile.profile');
    }

    /*update user profile*/
    public function updateProfile(Request $request)
    {

        $validateData = Validator::make($request->all(), [
            'name' => 'required',
            'company_name' => 'required',
            'designation' => 'required'
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withInput()->withErrors($validateData);
        }

        try{
            $updUser = User::where('id', Auth::id())->first();
            $updUserDetail = UserDetail::where('user_id', Auth::id())->first();

            $updUser->company_name = $request->company_name;
            $updUserDetail->designation = $request->designation;
            $updUserDetail->name = $request->name;

            /*if user set image then upload it adn save*/
            if ($request->hasFile('profile_image')) {
                $files = $request->file('profile_image');
                $name = str_random(20) . $updUser->id . '.' . $files->getClientOriginalExtension();
                $destinationPath = 'assets/uploads/User_Logo';
                $url = $destinationPath . "/" . $name;
                $files->move($destinationPath, $name);
                $updUserDetail->logo = $name;
            }

            $updUser->save();
            $updUserDetail->save();

            session()->flash('type', 'success');
            session()->flash('message', 'Successfully updated your information');
            return redirect()->back();

        }catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to update profile. please try again.....!');
            return redirect()->back();
        }
    }

    public function updateFlexipinForm()
    {
        return view('user.profile.updateFlexipinForm');
    }
    public function updateFlexipin(Request $request)
    {
        $validated_data = $request->validate([
            'new_pin' => "required|min:4|numeric|confirmed",
        ]);

        if ( isset($request->old_pin) and $request->old_pin != auth()->user()->flexipin ){
                return redirect()->back()->with(['type'=>'danger', 'message'=>'Incorrect Pin! Please contact with your resseler.']);
        }
        try{
            $user = User::find(auth()->user()->id);
            $user->flexipin = $request->new_pin;
            $user->save();

            return redirect()->back()->with(['type'=>'success', 'message'=>'Flexiload pin updated successfully']);
        }catch(\Exception $e){
            return redirect()->back()->with(['type'=>'danger', 'message'=>'Something Wrong']);
        }
        dd($request);
    }

    /*show change password form*/
	public function showChangePasswordForm(){
    	return view('user.profile.change_password');
    }

    /*update user password*/
    public function updatePassword(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
            're_password' => 'required',
        ]);

        if($validateData->fails()){
            return redirect()->back()->withErrors($validateData);
        }


        if(Hash::check($request->old_password,Auth::user()->getAuthPassword())) {
            if($request->new_password == $request->re_password){
                try{
                    $updPassword = User::where('id', Auth::user()->id)->first();

                    $updPasswordDet = UserDetail::where('user_id', Auth::id())->first();
                    $updPasswordDet->user_p = $request->new_password;
                    $updPasswordDet->save();

                    $updPassword->password = bcrypt($request->new_password);
                    $updPassword->save();

                    session()->flash('type', 'success');
                    session()->flash('message', 'Successfully changed your password.....!');
                    return redirect()->back();

                }catch (\Exception $e){
                    session()->flash('type', 'danger');
                    session()->flash('message', 'something went wrong to change password. please try again........!');
                    return redirect()->back();
                }
            }
            else{
                session()->flash('type', 'danger');
                session()->flash('message', 'password and confirm password didn\'t matched. please try again........!');
                return redirect()->back();
            }


        } else {
            session()->flash('type', 'danger');
            session()->flash('message', 'didn\'t matched your password with old password. please try again........!');
            return redirect()->back();
        }
    }
    public function password_for_pin(Request $request){
        // dd(bcrypt($request->password));
        if (Auth::user()->userDetail['user_p'] == $request->password) {
            $mobile_number = Auth::user()->cellphone;

            $message = "Your Flexipin is: ". Auth::user()->flexipin . "\nThanks For using our Service.";
            $message = urlencode($message);
            $api_key = "445156057064961560570649";
            $sender_id = "iglweb.com";
            $client = new \GuzzleHttp\Client();
            $api_url = "http://sms.iglweb.com/api/v1/send?api_key=". $api_key ."&contacts=". $mobile_number ."&senderid=". $sender_id ."&msg=".$message;
            $response = $client->request('GET', "$api_url");
            // dd($api_url);
            $json_response = $response->getBody()->getContents();
            $api_response = json_decode($json_response);

            if ($api_response->code == "445000") {
                return redirect()->back()->with(['success' => 'Pin Number Successfully Send to your number.']);
            } else {
                return redirect()->back()->with(['error' => 'Something Went Wrong, Talk to your admin.']);
            }
        }else {
            return redirect()->back()->with(['err' => "Password Doesn't Match"]);
        }
    }
}
