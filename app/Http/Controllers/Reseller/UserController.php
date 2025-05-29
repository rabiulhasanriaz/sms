<?php

namespace App\Http\Controllers\Reseller;


use App\Model\SenderIdUserDefault;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Model\User;
use App\Model\UserDetail;
use App\Rules\UserMobile;
use App\Model\SenderIdRegister;
use App\Model\SenderIdUser;
use App\Model\AccSmsRate;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    /*show all user list*/
    public function index()
    {
        $users = User::with('userDetail')->where('create_by', Auth::id())->whereNotIn('status', ['3'])->get();
        return view('reseller.users.user_list', compact('users'));
    }


    /*show new user registration form*/
    public function create()
    {
        return view('reseller.users.user_registration');
    }


    /* create & store new user */
    public function store(Request $request)
    {

        /*validate user information*/
        $validateData = Validator::make($request->all(), [
            'company_name' => ['required'],
            'user_name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'phone' => ['required', 'unique:users,cellphone', new UserMobile],
            'password' => ['required', 'min:3'],
            'status' => ['required'],
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withInput()->withErrors($validateData);
        }

        if ($request->status == 'Reseller') {
            $role = '4';
            $permission = $request->permission;
        } else {
            $role = '5';
            $permission = $request->permission;
        }
        // DB::beginTransaction();
        /*insert reseller information*/
        $userCreateData = [
            'create_by' => Auth::user()->id,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'cellphone' => $request->phone,
            'password' => bcrypt($request->password),
            'status' => '1',
            'role' => $role,
            'position' => (Auth::user()->position + 1),
            'permission' => $permission,
        ];
        // dd($userCreateData);
        $createUser = User::create($userCreateData);

        if ($createUser == true) {
            $randid = time();
            $api_key = '445' . $randid . $createUser->id . $randid;
            /*insert reseller details information*/
            $UserDetUpd = UserDetail::create([
                'user_id' => $createUser->id,
                'domain_name' => Auth::user()->userDetail->domain_name,
                'name' => $request->user_name,
                'designation' => $request->designation,
                'address' => $request->address,
                'nid' => $request->nid,
                'dob' => $request->dob,
                'user_p' => $request->password,
                'api_key' => $api_key,
            ]);


            if ($UserDetUpd == true) {
                /*if has reseller image then upload it adn save*/
                if ($request->hasFile('image')) {
                    $files = $request->file('image');
                    $name = str_random(20) . $UserDetUpd->id . '.' . $files->getClientOriginalExtension();
                    $destinationPath = 'assets/uploads/User_Logo';
                    $url = $destinationPath . "/" . $name;
                    $files->move($destinationPath, $name);
                    $logoUpd = UserDetail::where('id', $UserDetUpd->id)->update([
                        'logo' => $name,
                    ]);
                }
                /*insert initial user sms rate as 0*/
                try {
                    foreach (Auth::user()->smsRates as $allRate) {
                        AccSmsRate::create([
                            'country_id' => '1',
                            'user_id' => $createUser->id,
                            'operator_id' => $allRate->operator_id,
                            'asr_masking' => $allRate->asr_masking,
                            'asr_nonmasking' => $allRate->asr_nonmasking,
                        ]);
                    }
                    $senderId = SenderIdUser::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
                    SenderIdUser::create([
                        'user_id' => $createUser->id,
                        'sender_id' => $senderId->sender_id,
                    ]);
                    SenderIdUserDefault::create([
                        'user_id' => $createUser->id,
                        'sender_id' => $senderId->sender_id,
                    ]);
                } catch (\Exception $e) {
                    session()->flash('message', 'Something went wrong to create user(error code: 0030)' . $e->getMessage());
                    session()->flash('type', 'danger');
                    return redirect()->back()->withInput();
                }

                /*send sms to created user*/
                // $message = "Welcome To " . Auth::user()->userDetail->company_name . "\nYour SMS Portal Is Ready to Use\nURL: " . Auth::user()->userDetail->domain_name . "\nUID: " . $request->phone . "\nPass: " . $request->password;
                // $message = rawurlencode($message);
                // $number = '88'.$request->phone;
                // $defaultSenderId = SenderIdUserDefault::where('user_id', Auth::id())->first();
                // $client = new Client();
                // $url = config('app.url')."/api/v1/send?api_key=".Auth::user()->userDetail->api_key."&contacts=".$number."&senderid=".$defaultSenderId->sender->sir_sender_id."&msg=".$message."&for_registration=resellerToUser";
                // $res = $client->request('GET', $url);
                // $ret = $res->getBody();

                session()->flash('message', 'User Registration Successfully completed');
                session()->flash('type', 'success');
                return redirect()->back();

            } else {
                session()->flash('message', 'Something went wrong to create user(error code: 0020)');
                session()->flash('type', 'danger');
                return redirect()->back()->withInput();
            }
        } else {
            session()->flash('message', 'Something went wrong to create user(error code: 0010)');
            session()->flash('type', 'danger');
            return redirect()->back()->withInput();
        }
    }


    /*show user edit form*/
    public function edit($id)
    {
        $user = User::where(['create_by' => Auth::id(), 'id' => $id])->first();
        if ($user) {
            return view('reseller.users.edit_user_account', compact('user'));
        } else {
            session()->flash('message', 'Unknown user(error code: 0050)');
            session()->flash('type', 'danger');
            return redirect()->back();
        }
    }


    /*update user information*/
    public function update(Request $request, $id)
    {

        $updUser = User::where('id', $id)->first();
        if ($updUser) {
            /*validate user information*/
            $validateData = Validator::make($request->all(), [
                'company_name' => ['required'],
                'user_name' => ['required'],
                'email' => ['required', 'email', Rule::unique('users', 'id')->ignore($id)],
                'phone' => ['required', new UserMobile, Rule::unique('users', 'id')->ignore($id)],
                'password' => ['required', 'min:3'],
                'status' => ['required'],
            ]);

            if ($validateData->fails()) {
                return redirect()->back()->withInput()->withErrors($validateData);
            }

            if ($request->status == 'Reseller') {
                $role = '4';
            } else {
                $role = '5';
            }

            try {

                $updUser->permission = $request->permission;
             
                $updUserDetails = UserDetail::where('user_id', $id)->first();
                $updUser->company_name = $request->company_name;
                $updUser->email = $request->email;
                $updUser->cellphone = $request->phone;
                $updUser->password = bcrypt($request->password);
                $updUser->role = $role;
                $updUserDetails->designation = $request->designation;
                $updUserDetails->address = $request->address;
                $updUserDetails->name = $request->user_name;
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
                session()->flash('message', 'Successfully updated user information');

                $users = User::with('userDetail')->where('create_by', Auth::id())->whereNotIn('status', ['3'])->get();
                return redirect()->route('reseller.user.index', compact('users'));


            } catch (\Exception $e) {
                session()->flash('type', 'danger');
                session()->flash('message', 'something went wrong to update user. please try again.....!'. $e->getMessage());
                return redirect()->back();
            }
        } else {
            session()->flash('type', 'danger');
            session()->flash('message', 'can\'t find this user. please try again.....!');
            return redirect()->back();
        }
    }


    /*show suspend user list*/
    public function suspendUser()
    {
        $users = User::with('userDetail')->where(['create_by' => Auth::id(), 'status' => 2])->get();
        return view('reseller.users.suspend_user_list', compact('users'));
    }


    /*suspend a user*/
    public function suspend($id)
    {
        $suspendUser = User::where(['create_by' => Auth::id(), 'id' => $id])->first();
        if ($suspendUser) {
            try {
                $suspendUser->status = 2;
                $suspendUser->save();
                session()->flash('type', 'success');
                session()->flash('message', 'Suspend User Successfully completed');
                return redirect()->back();

            } catch (\Exception $e) {
                session()->flash('message', 'Something went wrong to suspend user(error code: 0040)');
                session()->flash('type', 'danger');
                return redirect()->back();
            }
        } else {
            session()->flash('message', 'Unknown user(error code: 0050)');
            session()->flash('type', 'danger');
            return redirect()->back();
        }
    }


    /*active a user*/
    public function active($id)
    {
        $activeUser = User::where(['create_by' => Auth::id(), 'id' => $id])->first();
        if ($activeUser) {
            try {

                $activeUser->status = 1;
                $activeUser->save();
                session()->flash('type', 'success');
                session()->flash('message', 'Active User Successfully completed');
                return redirect()->back();

            } catch (\Exception $e) {
                session()->flash('message', 'Something went wrong to active user(error code: 0080)');
                session()->flash('type', 'danger');
                return redirect()->back();
            }
        } else {
            session()->flash('message', 'Unknown user(error code: 0090)');
            session()->flash('type', 'danger');
            return redirect()->back();
        }
    }


    /*delete a user*/
    public function delete($id)
    {
        $deleteUser = User::where(['create_by' => Auth::id(), 'id' => $id])->first();
        if ($deleteUser) {
            try {
                $deleteUser->delete();
                session()->flash('type', 'success');
                session()->flash('message', 'User Deleted Successfully completed');
                return redirect()->back();

            } catch (\Exception $e) {
                session()->flash('message', 'Something went wrong to delete user(error code: 0060)');
                session()->flash('type', 'danger');
                return redirect()->back();
            }
        } else {
            session()->flash('message', 'Unknown user(error code: 0070)');
            session()->flash('type', 'danger');
            return redirect()->back();
        }
    }


    /*go to this reseller account*/
    public function goToThisAccount($id)
    {
        $user = User::where(['create_by' => Auth::id(), 'id' => $id])->first();
        if ($user) {
            try {
                if (Auth::attempt(['email' => $user->email, 'password' => $user->userDetail->user_p])) {
                    if (Auth::user()->status == '1') {
                        return redirect('/home');
                    } elseif (Auth::user()->status == '2') {
                        Auth::logout();
                        session()->flash('type', 'danger');
                        session()->flash('message', 'Your account was suspended');
                        return redirect()->back();
                    } else {
                        Auth::logout();
                        session()->flash('type', 'danger');
                        session()->flash('message', 'Your account was expired');
                        return redirect()->back();
                    }
                } else {
                    session()->flash('type', 'danger');
                    session()->flash('message', 'login credential was wrong...');
                    return redirect()->back();
                }

            } catch (\Exception $e) {
                session()->flash('type', 'danger');
                session()->flash('message', 'Something went wrong to go this user account. please try again1........' . $e->getMessage());
                return redirect()->back();
            }
        } else {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to go this user account. please try again2........');
            return redirect()->back();
        }
    }
}
