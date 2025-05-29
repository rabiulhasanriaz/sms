<?php

namespace App\Http\Controllers\Ajax;

use App\Model\SmsCampaign;
use App\Model\SmsCampaign_24h;
use App\Model\SmsDesktop24h;
use App\Model\SmsDesktopCampaignId;
use App\Model\SmsDesktop;
use App\Model\SmsCampaignId;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserAjaxController extends Controller
{
    /*show todays sms reports*/
    public function showTodaysReportDetail(Request $request)
    {
        try{
            $reports = SmsCampaign_24h::with('sender', 'operator')->where(['user_id'=>Auth::id(), 'campaign_id'=>$request->campaign_id])->get();
            if(count($reports)>0) {
                return view('user.ajax.views.todays_report', compact('reports'));
            }else{
                return "No Data Available";
            }
        }catch (\Exception $e){
            return "message:: ".$e->getMessage();
        }
    }

    public function showTodaysDynamicReportDetail(Request $request)
    {
        try{
            $reports = SmsDesktop24h::with('operator')->where(['user_id'=>Auth::id(), 'campaign_id'=>$request->campaign_id])->get();
            if(count($reports)>0) {
                return view('user.ajax.dynamic.ajax.views.todays_report', compact('reports'));
            }else{
                return "No Data Available";
            }
        }catch (\Exception $e){
            return "message:: ".$e->getMessage();
        }
    }

    /*show todays sms reports*/
    public function showArchivedReportDetail(Request $request)
    {
        try{
            $campaign = SmsCampaignId::where('id',$request->campaign_id)->first();
            $reports = SmsCampaign::with('sender', 'operator')->where(['user_id'=>Auth::id(), 'campaign_id'=>$request->campaign_id])->get();
            if(count($reports)>0) {
                return view('user.ajax.views.archived_report', compact('reports','campaign'));
            }else{
                return "No Data Available";
            }
        }catch (\Exception $e){
            return "message:: ".$e->getMessage();
        }
    }

    public function showArchivedReportDetailDynamic(Request $request)
    {
        try{
            $campaign = SmsDesktopCampaignId::where('id',$request->campaign_id)->first();
            $reports = SmsDesktop::with('operator')->where(['user_id'=>Auth::id(), 'campaign_id'=>$request->campaign_id])->get();
            // dd($reports);
            if(count($reports)>0) {
                return view('user.ajax.dynamic.ajax.views.archived_report', compact('reports','campaign'));
            }else{
                return "No Data Available";
            }
        }catch (\Exception $e){
            return "message:: ".$e->getMessage();
        }
    }


}
