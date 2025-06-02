<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\ApiAdd;
use Illuminate\Http\Request;

class ApiAddController extends Controller
{
    public function api_add(){
        $apis = ApiAdd::all();
        return view('admin.api-add')->with('apis',$apis);
    }

    public function api_add_insert(Request $request){
        $apiAdd = new ApiAdd();
        $apiAdd->api_name = $request->api_name;
        $apiAdd->api_url = $request->api_url;
        $apiAdd->api_balance = $request->api_balance;
        $apiAdd->api_status = 1;
        $apiAdd->save();
        session()->flash('type', 'success');
        session()->flash('message', 'Successfully updated Category.....');
        return redirect()->back();
    }

    public function api_status_active($id){
        $active = ApiAdd::where('id',$id)->update(['api_status' => 1]);
        return redirect()->back()->with(['success' => 'Api Activated Successfully']);
    }

    public function api_status_suspend($id){
        $active = ApiAdd::where('id',$id)->update(['api_status' => 2]);
        return redirect()->back()->with(['success' => 'Api Deactivated Successfully']);
    }
}
