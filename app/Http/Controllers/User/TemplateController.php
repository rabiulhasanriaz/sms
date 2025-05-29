<?php

namespace App\Http\Controllers\User;

use App\Model\SmsTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TemplateController extends Controller
{
    /*view all template*/
    public function index(){
        $templates = SmsTemplate::where('user_id', Auth::user()->id)->orderBy('st_name', 'asc')->get();
    	return view('user.messaging.templates', compact('templates'));
    }


    /*add new template*/
    public function store(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'tmp_name' => 'required',
            'tmp_message' => 'required',
        ]);

        if($validateData->fails()){
            return redirect()->back()->withErrors($validateData);
        }


        if(\SmsHelper::is_unicode($request->tmp_message)){
            $smsType = 2; //unicode
            $sms_number = \SmsHelper::unicode_sms_count($request->tmp_message);

        }else{
            $smsType = 1; //text
            $sms_number = \SmsHelper::text_sms_count($request->tmp_message);
        }

        try{
            SmsTemplate::create([
                'user_id' => Auth::id(),
                'st_name' => $request->tmp_name,
                'st_content' => $request->tmp_message,
                'st_total_sms' => $sms_number,
                'st_content_type' => $smsType,
            ]);

            session()->flash('type', 'success');
            session()->flash('message', 'Successfully added Template....');
            return redirect()->back();
        }catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'Oops! Something went wrong to add template. Please try again....!');
            return redirect()->back();
        }
    }


    /*update template*/
    public function update(Request $request)
    {

        $validateData = Validator::make($request->all(), [
            'tmp_name' => 'required',
            'tmp_message' => 'required',
            'template_id' => 'required',
        ]);

        if($validateData->fails()){
            return redirect()->back()->withErrors($validateData);
        }

        $editTemplate = SmsTemplate::where(['id'=>$request->template_id, 'user_id'=>Auth::id()])->first();

        if($editTemplate) {
            if (\SmsHelper::is_unicode($request->tmp_message)) {
                $smsType = 2; //unicode
                $sms_number = \SmsHelper::unicode_sms_count($request->tmp_message);

            } else {
                $smsType = 1; //text
                $sms_number = \SmsHelper::text_sms_count($request->tmp_message);
            }

            try {

                $editTemplate->st_name = $request->tmp_name;
                $editTemplate->st_content = $request->tmp_message;
                $editTemplate->st_total_sms = $sms_number;
                $editTemplate->st_content_type = $smsType;

                $editTemplate->save();

                session()->flash('type', 'success');
                session()->flash('message', 'Successfully added Template....');
                return redirect()->back();
            } catch (\Exception $e) {
                session()->flash('type', 'danger');
                session()->flash('message', 'Oops! Something went wrong to add template. Please try again....!');
                return redirect()->back();
            }
        }else{
            session()->flash('type', 'danger');
            session()->flash('message', 'Oops! Can\'t find your template for edit. Please try again....!');
            return redirect()->back();
        }
    }


    /*delete a template*/
    public function delete($id)
    {
        $deleteTemplate = SmsTemplate::where(['id'=>$id, 'user_id'=>Auth::id()])->first();

        if($deleteTemplate) {
            $deleteTemplate->delete();
            session()->flash('type', 'success');
            session()->flash('message', 'Successfully deleted Template....');
            return redirect()->back();
        }else{
            session()->flash('type', 'danger');
            session()->flash('message', 'Oops! Can\'t find your template for delete. Please try again....!');
            return redirect()->back();
        }
    }

}
