<?php

namespace App\Http\Controllers\Admin;

use App\Model\SenderIdNonMasking;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NonMaskingSenderIDController extends Controller
{
    //
    /*show non masking sender id list*/
    public function index(){
        $nonMaskingSenderIds = SenderIdNonMasking::get();
        return view('admin.senderId.non_masking_sender_id_list', compact('nonMaskingSenderIds'));
    }

    /*add new non masking sender id*/
    public function store(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'nonmasking' => ['required', 'unique:sender_id_non_maskings,number'],
        ]);

        if($validateData->fails()){
            return redirect()->back()->withInput()->withErrors($validateData);
        }

        try {
            SenderIdNonMasking::create([
                'number' => $request->nonmasking,
            ]);

            session()->flash('type', 'success');
            session()->flash('message', 'successfully added non masking sender id');
            return redirect()->back();
        }
        catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to add sender id. please try again later');
            return redirect()->back();
        }

    }


    /*show form of edit non masking sender id*/
    public function edit($id)
    {

        $nonMaskingSenderId = SenderIdNonMasking::where('id', $id)->first();
        if($nonMaskingSenderId) {
            return view('admin.senderId.edit_non_masking_sender_id', compact('nonMaskingSenderId'));
        }
        else{
            session()->flash('type', 'danger');
            session()->flash('message', 'can\'t find your non masking id. please try again......');
            return redirect()->route('admin.senderID.nonMaskingSenderID.index');
        }

    }

    /*update non masking sender id*/
    public function update(Request $request, $id)
    {
        $validateData = Validator::make($request->all(), [
            'nonmasking' => ['required', Rule::unique('sender_id_non_maskings', 'number')->ignore($request->nonmasking)],
        ]);

        if($validateData->fails()){

            return redirect()->back()->withInput()->withErrors($validateData);
        }

        try {
            $updNonMaskingSenderId = SenderIdNonMasking::where('id', $id)->first();
            if ($updNonMaskingSenderId) {
                $updNonMaskingSenderId->number = $request->nonmasking;
                $updNonMaskingSenderId->save();
                session()->flash('type', 'success');
                session()->flash('message', 'successfully updated non masking sender id...');
                return redirect()->route('admin.senderID.nonMaskingSenderID.index');
            } else {
                session()->flash('type', 'danger');
                session()->flash('message', 'can\'t find your non masking id. please try again......');
                return redirect()->route('admin.senderID.nonMaskingSenderID.index');
            }
        }
        catch(\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to update non masking sender id......');
            return redirect()->back();
        }
    }

    /*delete non masking sender id*/
    public function delete($id)
    {
        try{
            SenderIdNonMasking::where('id', $id)->delete();
            session()->flash('type', 'success');
            session()->flash('message', 'Non Masking Sender Id deleted successfully');
            return redirect()->back();
        }catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to delete non masking sender id'.$e->getMessage());
            return redirect()->back();
        }
    }
}
