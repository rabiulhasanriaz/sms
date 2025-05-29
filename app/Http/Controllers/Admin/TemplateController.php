<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\SmsDesktopTemplate;
use App\Model\User;
use App\Model\UserDetail;
use App\Model\DateFormater;
use Carbon\Carbon;

class TemplateController extends Controller
{
    public function create(){
        $templates = SmsDesktopTemplate::all();
        return view('admin.template.create',compact('templates'));
    }
    public function store(Request $request){
        // dd($request->all());
        $submit_at = Carbon::now()->format('Y-m-d H:i:s');
        $messages = 
        [
            'title:required' => 'Title is Required',
            'content:required' => 'Content is Required'
        ];
        $request->validate([
            'title' => 'required',
            'content' => 'required'
        ]);
        $data = new SmsDesktopTemplate();
        $data->template_title = $request->title;
        $data->template_content = $request->content;
        $data->created_at = $submit_at;
        $data->save();

        session()->flash('type', 'success');
        session()->flash('message', 'Template Created Successfully');
        return redirect()->back();
    }
    public function template_edit($id){
        $temp = SmsDesktopTemplate::where('id',$id)->first();
        return view('admin.template.temp-edit',compact('temp'));
    }
    public function update(Request $request,$id){
        $update_at = Carbon::now()->format('Y-m-d H:i:s');
        $request->validate([
            'title' => 'required',
            'content' => 'required'
        ]);
        $data = SmsDesktopTemplate::where('id',$id)->first();
        $data->template_title = $request->title;
        $data->template_content = $request->content;
        $data->status = $request->status;
        $data->updated_at = $update_at;
        $data->save();

        session()->flash('type', 'success');
        session()->flash('message', 'Template Updated Successfully');
        return redirect()->route('admin.template.template-create');
    }
    public function delete($id){
        SmsDesktopTemplate::where('id',$id)->delete();
        session()->flash('type', 'success');
        session()->flash('message', 'Template Deleted Successfully');
        return redirect()->back();
    }

    public function assign(){
        $users = User::where('role',5)->get();
        return view('admin.template.assign',compact('users'));
    }
    public function assign_template($id){
        $user = User::where('id',$id)->first();
        $userData = UserDetail::where('user_id',$id)->first();
        $templates = SmsDesktopTemplate::where('status',1)->get();
        return view('admin.template.templates',compact('templates','user','userData'));
    }
    public function template_give(Request $request,$id){
        // dd($request->all());
        $temp = implode(',',$request->temps);
        $data = UserDetail::where('user_id',$id)->first();
        $data->template_permission = $temp;
        $data->save();

        session()->flash('type', 'success');
        session()->flash('message', 'Template Assigned Successfully');
        return redirect()->route('admin.template.template-assign');
    }
    
    public function dateFormat(){
        $formates = DateFormater::all();
        return view('admin.template.dateFormat',compact('formates'));
    }

    public function storeDateFormat(Request $request){
        // dd($request->all());
        $messages = 
        [
            'format:required' => 'Format is Required',
        ];
        $request->validate([
            'format' => 'required',
        ]);
        $date = new DateFormater();
        $date->dateFormat = $request->format;
        $date->save();
        session()->flash('type', 'success');
        session()->flash('message', 'Format Assigned Successfully');
        return redirect()->back();
    }

    public function assignDateFormat(){
        $users = User::where('role',5)->get();
        return view('admin.template.assign-date-format',compact('users'));
    }

    public function assign_date($id){
        // dd($id);
        $user = User::where('id',$id)->first();
        $userData = UserDetail::where('user_id',$id)->first();
        $formates = DateFormater::where('status',1)->get();
        return view('admin.template.formates',compact('formates','user','userData'));
    }

    public function format_give(Request $request,$id){
        // dd($request->all());
        if ($request->dateFormates < 1) {
            $forms = '';
        }else {
            $forms = implode(',',$request->dateFormates);
        }
        
        $data = UserDetail::where('user_id',$id)->first();
        $data->date_format = $forms;
        $data->save();

        session()->flash('type', 'success');
        session()->flash('message', 'Format Assigned Successfully');
        return redirect()->route('admin.template.date-format-assign');
    }
    
    public function format_delete($id){
        DateFormater::where('id',$id)->delete();
        session()->flash('type', 'danger');
        session()->flash('message', 'Format Deleted Successfully');
        return redirect()->back();
    }
    
    public function format_ajax(Request $request){
        // dd($request->all());
        $date = DateFormater::where('id',$request->value)->first();
        return view('admin.template.format-ajax',compact('date'));
    }

    public function format_update(Request $request){
        // dd($request->all());
        DateFormater::where('id',$request->formatId)->update([
            'dateFormat' => $request->format
        ]);
        session()->flash('type', 'success');
        session()->flash('message', 'Format Updated Successfully');
        return redirect()->route('admin.template.date-format');
    }
}
