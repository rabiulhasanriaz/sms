<?php

namespace App\Http\Controllers\Admin;

use App\Model\AccSmsRate;
use App\Model\Operator;
use App\Model\SenderIdRegister;
use App\Model\SenderIdUser;
use App\Model\User;
use App\Model\UserDetail;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rules\UserMobile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Validator;
use Illuminate\Validation\Rule;
use App\Model\SenderIdUserDefault;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class ResellerController extends Controller
{
    //
    /*reseller list*/
    public function index()
    {
        $users = User::with('userDetail')->where('create_by', Auth::id())->get();
        return view('admin.reseller.reseller_list', compact('users'));

    }


    /*show create reseller form*/
    public function create()
    {
        return view('admin.reseller.reseller_registration');
    }


    /* create new reseller */
    public function store(Request $request)
    {
        // dd($request->all());
        /*validate reseller information*/
        $validateData = Validator::make($request->all(), [
            'company_name' => ['required'],
            'reseller_name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'phone' => ['required', 'unique:users,cellphone', new UserMobile],
            'password' => ['required', 'min:3'],
        ]);


        if ($validateData->fails()) {
            return redirect()->back()->withInput()->withErrors($validateData);
        }

        /*insert reseller information*/
        $createReseller = User::create([
            'create_by' => Auth::user()->id,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'cellphone' => $request->phone,
            'password' => bcrypt($request->password),
            'status' => '1',
            'role' => '4',
            'position' => (Auth::user()->position + 1),
        ]);

        if ($createReseller == true) {
            $randid = time();
            $api_key = '445' . $randid . $createReseller->id . $randid;
            /*insert reseller details information*/
            $resellerDetUpd = UserDetail::create([
                'user_id' => $createReseller->id,
                'domain_name' => Auth::user()->userDetail->domain_name,
                'name' => $request->reseller_name,
                'designation' => $request->designation,
                'address' => $request->address,
                'nid' => $request->nid,
                'dob' => $request->dob,
                'user_p' => $request->password,
                'api_key' => $api_key,
            ]);


            if ($resellerDetUpd == true) {
                /*if has reseller image then upload it adn save*/
                if ($request->hasFile('image')) {
                    $files = $request->file('image');
                    $name = Str::random(20) . $resellerDetUpd->id . '.' . $files->getClientOriginalExtension();
                    $destinationPath = 'assets/uploads/User_Logo';
                    $url = $destinationPath . "/" . $name;
                    $files->move($destinationPath, $name);
                    $logoUpd = UserDetail::where('id', $resellerDetUpd->id)->update([
                        'logo' => $name,
                    ]);
                }
                /*insert initial user sms rate as 0*/
                try {
                    $allOperators = Operator::orderBy('id', 'asc')->take(5)->get();
                    foreach (Auth::user()->smsRates as $allRate) {
                        AccSmsRate::create([
                            'country_id' => '1',
                            'user_id' => $createReseller->id,
                            'operator_id' => $allRate->operator_id,
                            'asr_masking' => $allRate->asr_masking,
                            'asr_nonmasking' => $allRate->asr_nonmasking,
                            'asr_dynamic' => $allRate->asr_dynamic
                        ]);
                    }
                    $senderId = SenderIdRegister::where('sir_active', '1')->orderBy('id', 'desc')->first();
                    SenderIdUser::create([
                        'user_id' => $createReseller->id,
                        'sender_id' => $senderId->id,
                    ]);
                    SenderIdUserDefault::create([
                        'user_id' => $createReseller->id,
                        'sender_id' => $senderId->id,
                    ]);
                } catch (\Exception $e) {
                    session()->flash('message', 'Something went wrong to create user(error code: 030)');
                    session()->flash('type', 'danger');
                    return redirect()->back()->withInput();
                }

                /*send sms to created user*/
                // $message = "Welcome To " . Auth::user()->userDetail->company_name . "\nYour SMS Portal Is Ready to Use\nURL: " . Auth::user()->userDetail->domain_name . "\nUID: " . $request->phone . "\nPass: " . $request->password;
                // $message = rawurlencode($message);
                // $number = '88'.$request->phone;

                // $client = new Client();
                // $url = config('app.url')."/api/v1/send?api_key=".Auth::user()->userDetail->api_key."&contacts=".$number."&senderid=8804445604445&msg=".$message."&for_registration=adminToReseller";

                // $res = $client->request('GET', $url);
                // $ret = $res->getBody();

                session()->flash('message', 'Reseller Registration Successfully completed');
                session()->flash('type', 'success');
                return redirect()->back();

            } else {
                session()->flash('message', 'Something went wrong to create user(error code: 020)');
                session()->flash('type', 'danger');
                return redirect()->back()->withInput();
            }
        } else {
            session()->flash('message', 'Something went wrong to create user(error code: 010)');
            session()->flash('type', 'danger');
            return redirect()->back()->withInput();
        }
    }


    /*show reseller edit form*/
    public function edit($id)
    {
        try {
            $userInfo = User::with('userDetail')->where('id', $id)->first();
            if ($userInfo) {
                return view('admin.reseller.edit_reseller_account', compact('userInfo'));
            } else {
                session()->flash('message', 'can\'t find this user. please try again');
                session()->flash('type', 'danger');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            session()->flash('message', 'Something went wrong to edit user');
            session()->flash('type', 'danger');
            return redirect()->back();
        }
    }


    /*update reseller details*/
    public function update(Request $request, $id)
    {

        $updUser = User::where('id', $id)->first();
        if ($updUser) {
            /*validate reseller information*/
            $validateData = Validator::make($request->all(), [
                'company_name' => ['required'],
                'reseller_name' => ['required'],
                'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
                'phone' => ['required', new UserMobile, Rule::unique('users', 'cellphone')->ignore($id)],
                'password' => ['required', 'min:3'],
            ]);

            if ($validateData->fails()) {
                return redirect()->back()->withErrors($validateData);
            }

            try {
                $updUserDetails = UserDetail::where('user_id', $id)->first();
                $updUser->company_name = $request->company_name;
                
                $updUser->email = $request->email;
                $updUser->cellphone = $request->phone;
                $updUser->password = bcrypt($request->password);
                $updUserDetails->designation = $request->designation;
                $updUserDetails->address = $request->address;
                $updUserDetails->name = $request->reseller_name;
                $updUserDetails->user_p = $request->password;

                /*if has reseller image then upload it adn save*/
                if ($request->hasFile('image')) {
                    $files = $request->file('image');
                    $name = str_random(20) . $updUser->id . '.' . $files->getClientOriginalExtension();
                    $destinationPath = 'assets/uploads/User_Logo';
                    $url = $destinationPath . "/" . $name;
                    $files->move($destinationPath, $name);
                    $updUserDetails->logo = $name;
                }

                $updUser->save();
                $updUserDetails->save();

                session()->flash('type', 'success');
                session()->flash('message', 'Successfully updated reseller information');

                $users = User::with('userDetail')->get();
                return redirect()->route('admin.reseller.index', compact('users'));


            } catch (\Exception $e) {
                session()->flash('type', 'danger');
                session()->flash('message', 'something went wrong to update user. please try again.....!');
                return redirect()->back();
            }
        } else {
            session()->flash('type', 'danger');
            session()->flash('message', 'can\'t find this user. please try again.....!');
            return redirect()->back();
        }

    }


    public function treeView()
    {
        $roots = User::with('myUsers')->where('position', '0')->get();
        return view('admin.reseller.reseller_tree_view', compact('roots'));
    }


    /*suspend a reseller*/
    public function suspend($id)
    {
        try {

            $suspendUser = User::where('id', $id)->first();
            if ($suspendUser) {
                $suspendUser->status = '2';

                $suspendUser->save();

                session()->flash('type', 'success');
                session()->flash('message', 'Suspended successfully');
                return redirect()->back();
            } else {
                session()->flash('type', 'danger');
                session()->flash('message', 'can\'t find this user. please try again........');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to suspend user. please try again........');
            return redirect()->back();
        }
    }


    /*active a reseller*/
    public function active($id)
    {
        try {

            $activeUser = User::where('id', $id)->first();
            if ($activeUser) {
                $activeUser->status = '1';

                $activeUser->save();

                session()->flash('type', 'success');
                session()->flash('message', 'Re-Active successfully');
                return redirect()->back();
            } else {
                session()->flash('type', 'danger');
                session()->flash('message', 'can\'t find this user. please try again........');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to re-active user. please try again........');
            return redirect()->back();
        }
    }


    /*go to this reseller account*/
    public function goToThisAccount($id)
    {
        $user = User::where(['id'=>$id])->first();
        if($user){

            try{
                if(Auth::attempt(['email'=>$user->email, 'password'=>$user->userDetail->user_p])){
                    if(Auth::user()->status=='1'){
                        return redirect('/home');
                    }
                    elseif(Auth::user()->status=='2'){
                        Auth::logout();
                        session()->flash('type', 'danger');
                        session()->flash('message', 'Your account was suspended');
                        return redirect()->back();
                    }
                    else{
                        Auth::logout();
                        session()->flash('type', 'danger');
                        session()->flash('message', 'Your account was expired');
                        return redirect()->back();
                    }
                }
                else{
                    session()->flash('type', 'danger');
                    session()->flash('message', 'login credential was wrong...');
                    return redirect()->back();
                }

            }catch (\Exception $e){
                session()->flash('type', 'danger');
                session()->flash('message', 'Something went wrong to go this user account. please try again1........'.$e->getMessage());
                return redirect()->back();
            }
        }else{
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to go this user account. please try again2........');
            return redirect()->back();
        }
    }

}
