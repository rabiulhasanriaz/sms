<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\UserDetail;

class ApiPermissionController extends Controller
{
    public function api_user(){
        $api = UserDetail::where('api_key','!=','')
                         ->pluck('id')
                         ->toArray();
        $api_user = User::with('userDetail')->whereIn('id',$api)
                              ->where('role',5)
                              ->get();
                            //   dd($api_user);
        return view('admin.api_permission',compact('api_user'));
    }
    public function api_user_active($id){
        $active = UserDetail::where('user_id',$id)->update(['api_permission' => 1]);
        return redirect()->back()->with(['success' => 'Api Activated Successfully']);
    }
    public function api_user_suspend($id){
        $active = UserDetail::where('user_id',$id)->update(['api_permission' => 2]);
        return redirect()->back()->with(['suspend' => 'Api Suspended!']);
    }
}
