<?php

namespace App\Http\Controllers\Admin;

use App\Model\Operator;
use App\Model\SenderIdVirtualNumber;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class VirtualNumberController extends Controller
{
    //
    /*list of all virtual numbers*/
    public function index()
    {
        $virtualNumbers = SenderIdVirtualNumber::with('operator')->get();
        return view('admin.virtualNumber.virtual_number_list', compact('virtualNumbers'));
    }

    /*show form of create new virtual number*/
    public function create()
    {
        $operators = Operator::take(5)->get();
        return view('admin.virtualNumber.add_virtual_number', compact('operators'));
    }

    /*store new virtual number*/
    public function store(Request $request)
    {
        /*validate input data*/
        $validateData = Validator::make($request->all(), [
            'operator_id' => ['required', 'numeric'],
            'virtual_number' => ['required'],
            'api_username' => ['required'],
            'api_password' => ['required'],
            'auto_load_amount' => ['required'],
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withInput()->withErrors($validateData);
        }

        $createVirtualNumber = SenderIdVirtualNumber::create([
            'operator_id' => $request->operator_id,
            'sivn_number' => $request->virtual_number,
            'sivn_name' => $request->virtual_number_name,
            'sivn_api_user_name' => $request->api_username,
            'sivn_api_password' => $request->api_password,
            'sivn_load_amount' => $request->auto_load_amount,
        ]);
        if ($createVirtualNumber == true) {
            session()->flash('type', 'success');
            session()->flash('message', 'virtual number added successfully...... ');
            return redirect()->route('admin.virtualNumber.index');
        } else {
            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to add virtual number. please try again........');
            return redirect()->back()->withInput();
        }
    }


    /*show edit form of virtual number*/
    public function edit($id)
    {
        try {
            $operators = Operator::get();
            $virtualNumber = SenderIdVirtualNumber::where('id', $id)->first();
            if($virtualNumber) {
                return view('admin.virtualNumber.edit_virtual_number', compact('operators', 'virtualNumber'));
            }
            else{
                session()->flash('type', 'danger');
                session()->flash('message', 'can\'t find virtual number. please try again........!');
                $virtualNumbers = SenderIdVirtualNumber::with('operator')->get();
                return redirect()->route('admin.virtualNumber.index', compact('virtualNumbers'));
            }
        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'can\'t find virtual number. please try again.........!');
            $virtualNumbers = SenderIdVirtualNumber::with('operator')->get();
            return redirect()->route('admin.virtualNumber.index', compact('virtualNumbers'));
        }
    }

    public function update(Request $request, $id)
    {
        /*validate input data*/
        $validateData = Validator::make($request->all(), [
            'operator_id' => ['required', 'numeric'],
            'virtual_number' => ['required'],
            'api_username' => ['required'],
            'api_password' => ['required'],
            'auto_load_amount' => ['required']
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withErrors($validateData);
        }

        try {
            $updSenderId = SenderIdVirtualNumber::where('id', $id)->first();
            if ($updSenderId) {
                $updSenderId->operator_id = $request->operator_id;
                $updSenderId->sivn_number = $request->virtual_number;
                $updSenderId->sivn_name = $request->virtual_number_name;
                $updSenderId->sivn_api_user_name = $request->api_username;
                $updSenderId->sivn_api_password = $request->api_password;
                $updSenderId->sivn_load_amount = $request->auto_load_amount;

                $updSenderId->save();

                session()->flash('type', 'success');
                session()->flash('message', 'Virtual Number updated successfully updated......!');
                $virtualNumbers = SenderIdVirtualNumber::with('operator')->get();
                return redirect()->route('admin.virtualNumber.index', compact('virtualNumbers'));
            } else {
                session()->flash('type', 'danger');
                session()->flash('message', 'can\'t find this virtual number. please try again.....!');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to edit virtual number.....!');
            return redirect()->back();
        }
    }

    public function delete($id)
    {
        try{
            SenderIdVirtualNumber::where('id', $id)->delete();
            session()->flash('type', 'success');
            session()->flash('message', 'Virtual Number deleted successfully......!');
            return redirect()->back();
        }
        catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to delete virtual number.....!');
            return redirect()->back();
        }
    }


    /*check virtual number balance*/
    public function balanceCheck($id)
    {

        $virtualNumber = SenderIdVirtualNumber::where('id', $id)->first();
        if($virtualNumber){
            if($virtualNumber->operator_id=='3'){
                $userName = $virtualNumber->sivn_api_user_name;
                $password = $virtualNumber->sivn_api_password;
                $apiCode = '3';
                $url = "https://gpcmp.grameenphone.com/gpcmpapi/messageplatform/controller.home?username=".$userName."&password=".$password."&apicode=".$apiCode."&msisdn=0&countrycode=0&cli=0&messagetype=0&message=0&messageid=0";

                $client = new Client();

                $res = $client->request('GET', $url);
                $ret = $res->getBody()->getContents();
                return response()->json(['result'=> $ret]);
            }else{
                return response()->json(['result'=>'only gp working now. please select gp virtual number']);
            }
        }else{
            return response()->json(['result'=>'can\'t find your sender id']);
        }
//        https://cmp.grameenphone.com/gpcmpapi/messageplatform/controller.home?username=IGLWAdmin&password=qazXSW11!!!!&apicode=3&msisdn=0&countrycode=0&cli=0&messagetype=0&message=0&messageid=0
    }
}
