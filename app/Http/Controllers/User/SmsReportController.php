<?php

namespace App\Http\Controllers\User;

use Excel;
use PDF;
use App\Exports\SmsDesktopExport;
use App\Exports\SmsDesktopTotalExport;
use App\Exports\SmsDesktop24hExport;
use App\Model\SmsCampaign;
use App\Model\SmsCampaignId;
use App\Model\SmsCampaign_24h;
use App\Model\SmsCamPending;
use App\Model\SmsDesktopCampaignId;
use App\Model\SmsDesktop;
use App\Model\SmsDesktop24h;
use App\Serialisers\ArchivedReportSerialiser;
use App\Serialisers\ApiReportsSerialiser;
use App\Serialisers\ArchivedDynamicReportSerialiser;
use App\Serialisers\TodaysReportSerialiser;
use App\Serialisers\TodaysDynamicReportSerialiser;
use App\Serialisers\ReportDownloadSerialiser;
use Carbon\Carbon;
use Exporter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SmsReportController extends Controller
{
    public function pending_for_approval_sms_report()
    {
        $pending_campaigns = SmsCampaignId::with('sender')
            ->where('user_id', Auth::id())
            ->where('sci_deal_type', '1')
            ->where('sci_campaign_status', 0)
            ->orderBy('id', 'desc')
            ->get();

        return view('user.reports.pending_for_approval_sms_report', compact('pending_campaigns'));
    }

    public function rejected_sms_report()
    {
        $rejected_campaigns = SmsCampaignId::with('sender')
            ->where('user_id', Auth::id())
            ->where('sci_deal_type', '1')
            ->where('sci_campaign_status', 2)
            ->orderBy('id', 'desc')
            ->get();

        return view('user.reports.rejected_sms_report', compact('rejected_campaigns'));
    }

    /*start view dlr*/
    public function todays_sms_report()
    {
        $todays_campaigns = SmsCampaignId::with('sender')
            ->where('user_id', Auth::id())
            ->where('sci_deal_type', '1')
            ->where('sci_campaign_status', 1)
            ->where(function ($query) {
                $query->where('sci_from_api', 0);
                $query->orWhere('sci_from_api', NULL);
            })
            ->where('sci_targeted_time', '>=', Carbon::now()->subHours(24))
            ->orderBy('id', 'desc')
            ->get();


        $todays_campaigns_by_api = SmsCampaignId::with('sender')
                                ->where('user_id', Auth::id())
                                ->where('sci_deal_type', '1')
                                ->where('sci_targeted_time', '>=', Carbon::now()->subHours(24))
                                ->where('sci_from_api',1)
                                ->groupBy('sci_from_api')
                                ->selectRaw('*, sum(sci_total_cost) as sum,sum(sci_total_submitted) as sub_sum')
                                ->orderBy('id', 'desc')
                                ->first();
        return view('user.reports.view-dlr.todays_sms_report', compact('todays_campaigns','todays_campaigns_by_api'));
    }

    public function todays_dynamic_sms_report()
    {
        $todays_campaigns = SmsDesktopCampaignId::where('user_id', Auth::id())
            ->where('sdci_deal_type', '1')
            ->where('sdci_campaign_status', 1)
            ->where(function ($query) {
                $query->where('sdci_from_api', 0);
                $query->orWhere('sdci_from_api', NULL);
            })
            ->where('sdci_targeted_time', '>=', Carbon::now()->subHours(24))
            ->orderBy('id', 'desc')
            ->get();


        $todays_campaigns_by_api = SmsDesktopCampaignId::where('user_id', Auth::id())
                                ->where('sdci_deal_type', '1')
                                ->where('sdci_targeted_time', '>=', Carbon::now()->subHours(24))
                                ->where('sdci_from_api',4)
                                ->groupBy('sdci_from_api')
                                ->selectRaw('*, sum(sdci_total_cost) as sum,sum(sdci_total_submitted) as sub_sum')
                                ->orderBy('id', 'desc')
                                ->first();
        return view('user.reports.dynamic.reports.view-dlr.todays_sms_report', compact('todays_campaigns','todays_campaigns_by_api'));
    }


    public function show_todays_report_ajax(Request $request){
        $todays_reports = SmsCampaignId::with('sender')
                                ->where('user_id', Auth::id())
                                ->where('sci_deal_type', '1')
                                ->where('sci_targeted_time', '>=', Carbon::now()->subHours(24))
                                ->where('sci_from_api',1)
                                ->orderBy('id', 'desc')
                                ->get();
        return view('user.ajax.views.show_report_of_today', compact('todays_reports'));
    }

    public function show_todays_dynamic_report_ajax(Request $request){
        $todays_reports = SmsDesktopCampaignId::where('user_id', Auth::id())
                                ->where('sdci_deal_type', '1')
                                ->where('sdci_targeted_time', '>=', Carbon::now()->subHours(24))
                                ->where('sdci_from_api',4)
                                ->orderBy('id', 'desc')
                                ->get();
        return view('user.ajax.dynamic.ajax.views.show_report_of_today', compact('todays_reports'));
    }

    /*download todays campaign details*/
    public function download_todays_report($campaign_id)
    {
        $reports = SmsCampaign_24h::with('sender')
            ->select('sender_id', 'sct_cell_no', 'sct_message' ,'sct_message' , 'sct_sms_cost', 'created_at', 'sct_delivery_report')
            ->where(['user_id' => Auth::id(), 'campaign_id' => $campaign_id])
            ->orderBy('id', 'desc')
            ->get();

        $campaign = SmsCampaignId::where('id', $campaign_id)->first();
        $fileName = $campaign->sci_campaign_id . ".xlsx";

        $serialiser = new TodaysReportSerialiser();
        $excel = Exporter::make('Excel');
        $excel->load($reports);
        // dd($excel);
        $excel->setSerialiser($serialiser);

        return $excel->stream($fileName);

    }

    public function download_dynamic_todays_report($campaign_id)
    {
      return Excel::download(new SmsDesktop24hExport($campaign_id), 'todays_report.xlsx');
    }

    public function archived_sms_report(Request $request)
    {
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $q_start_date = $request->start_date." 00:00:00";
            $q_end_date = $request->end_date." 23:59:59";
        }else{
            $start_date = Carbon::now()->subDays(15)->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $q_start_date = Carbon::now()->subDays(15);
            $q_end_date = Carbon::now();
        }
            $archived_campaigns = SmsCampaignId::with('sender')
            ->where('user_id', Auth::id())
            ->where('sci_deal_type', '1')
            ->where('sci_targeted_time', '>=', $q_start_date)
            ->where('sci_targeted_time', '<=', $q_end_date)
            ->where(function ($query) {
                $query->where('sci_from_api',0);
                $query->orWhere('sci_from_api',NULL);
            })
            ->orderBy('id', 'desc')
            ->get();

            $archived_campaign = SmsCampaignId::with('sender')
            ->where('user_id', Auth::id())
            ->where('sci_deal_type', '1')
            ->where('sci_targeted_time', '>=', $q_start_date)
            ->where('sci_targeted_time', '<=', $q_end_date)
            ->where(function ($query) {
                $query->where('sci_from_api',0);
                $query->orWhere('sci_from_api',NULL);
            })
            ->orderBy('id', 'desc')
            ->pluck('id')->toArray();
            $cost = SmsCampaign::whereIn('campaign_id',$archived_campaign)
                                ->groupBy('campaign_id')
                                ->get();
            dd($cost);
            $api = SmsCampaign::where('user_id',Auth::id())
                            ->where('sc_targeted_time','>=', $start_date)
                            ->where('sc_targeted_time', '<=', $end_date)
                            ->where('sc_status',200)
                            ->get();
            // dd($api);


            $archived_campaigns_by_api = SmsCampaignId::with('sender')
            ->where('user_id', Auth::id())
            ->where('sci_deal_type', '1')
            ->where('sci_targeted_time', '>=', $q_start_date)
            ->where('sci_targeted_time', '<=', $q_end_date)
            ->where('sci_from_api',1)
            ->groupBy('sci_from_api')
            ->selectRaw('*, sum(sci_total_cost) as sum,sum(sci_total_submitted) as sub_sum')
            ->orderBy('id', 'desc')
            ->first();
            // $total = $archived_campaigns_by_api->groupBy('sender_id')
            //         ->selectRaw('*, sum(sci_total_cost) as sum')
            //         ->pluck('sum','sender_id');
            // dd($archived_campaigns_by_api);


        return view('user.reports.view-dlr.archived_report', compact('archived_campaigns','archived_campaigns_by_api', 'start_date', 'end_date','cost'));
    }
    public function dynamic_archived_sms_report(Request $request)
    {
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $q_start_date = $request->start_date." 00:00:00";
            $q_end_date = $request->end_date." 23:59:59";
        }else{
            $start_date = Carbon::now()->subDays(15)->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $q_start_date = Carbon::now()->subDays(15);
            $q_end_date = Carbon::now();
        }
            $archived_campaigns = SmsDesktopCampaignId::where('user_id', Auth::id())
            ->where('sdci_deal_type', '1')
            ->where('sdci_targeted_time', '>=', $q_start_date)
            ->where('sdci_targeted_time', '<=', $q_end_date)
            ->where(function ($query) {
                $query->where('sdci_from_api',0);
                $query->orWhere('sdci_from_api',NULL);
            })
            ->orderBy('id', 'desc')
            ->get();

            $archived_campaign = SmsDesktopCampaignId::where('user_id', Auth::id())
            ->where('sdci_deal_type', '1')
            ->where('sdci_targeted_time', '>=', $q_start_date)
            ->where('sdci_targeted_time', '<=', $q_end_date)
            ->where(function ($query) {
                $query->where('sdci_from_api',0);
                $query->orWhere('sdci_from_api',NULL);
            })
            ->orderBy('id', 'desc')
            ->pluck('id')->toArray();
            $cost = SmsDesktop::whereIn('campaign_id',$archived_campaign)
                                ->groupBy('campaign_id')
                                ->get();


            $archived_campaigns_by_api = SmsDesktopCampaignId::where('user_id', Auth::id())
            ->where('sdci_deal_type', '1')
            ->where('sdci_targeted_time', '>=', $q_start_date)
            ->where('sdci_targeted_time', '<=', $q_end_date)
            ->where('sdci_from_api',4)
            ->groupBy('sdci_from_api')
            ->selectRaw('*, sum(sdci_total_cost) as sum,sum(sdci_total_submitted) as sub_sum')
            ->orderBy('id', 'desc')
            ->first();
            // dd($archived_campaigns_by_api);
            // $total = $archived_campaigns_by_api->groupBy('sender_id')
            //         ->selectRaw('*, sum(sci_total_cost) as sum')
            //         ->pluck('sum','sender_id');
            // dd($archived_campaigns_by_api);


        return view('user.reports.dynamic.reports.view-dlr.archived_report', compact('archived_campaigns','archived_campaigns_by_api', 'start_date', 'end_date','cost'));
    }

    public function show_api_report_ajax(Request $request){
        $start_date = $request->start_date." 00:00:00";
        $end_date = $request->end_date." 23:59:59";
        $api_reports = SmsCampaignId::with('sender')
                                ->where('user_id', Auth::id())
                                ->where('sci_targeted_time', '>=', $start_date)
                                ->where('sci_targeted_time', '<=', $end_date)
                                ->where('sci_deal_type', '1')
                                ->where('sci_from_api',1)
                                ->orderBy('id', 'desc')
                                ->get();
        return view('user.ajax.views.show_report_of_api',compact('api_reports'));
    }

    public function show_dynamic_api_report_ajax(Request $request){
        $start_date = $request->start_date." 00:00:00";
        $end_date = $request->end_date." 23:59:59";
        $api_reports = SmsDesktopCampaignId::where('user_id', Auth::id())
                                ->where('sdci_targeted_time', '>=', $start_date)
                                ->where('sdci_targeted_time', '<=', $end_date)
                                ->where('sdci_deal_type', '1')
                                ->where('sdci_from_api',4)
                                ->orderBy('id', 'desc')
                                ->get();
        return view('user.ajax.dynamic.ajax.views.show_report_of_api',compact('api_reports'));
    }
    /*end view dlr*/


    /*download todays campaign details*/
    public function download_archived_report($campaign_id)
    {
        $reports = SmsCampaign::with('sender')
            ->select('sender_id', 'sc_cell_no', 'sc_message' ,'sc_sms_cost', 'created_at', 'sc_delivery_report')
            ->where(['user_id' => Auth::id(), 'campaign_id' => $campaign_id])
            ->orderBy('id', 'desc')
            ->get();

        $campaign = SmsCampaignId::where('id', $campaign_id)->first();
        $fileName = $campaign->sci_campaign_id . ".xlsx";

        $serialiser = new ArchivedReportSerialiser();
        $excel = Exporter::make('Excel');
        $excel->load($reports);
        // dd($excel);
        $excel->setSerialiser($serialiser);

        return $excel->stream($fileName);

    }

    // public function download_api_report(Request $request){
    //     $start_date = $request->start_date." 00:00:00";
    //     $end_date = $request->end_date." 23:59:59";

    //     $api = SmsCampaign::where('user_id',Auth::id())

    //                         ->where('sc_targeted_time','>=', $start_date)
    //                         ->where('sc_targeted_time', '<=', $end_date)
    //                         ->where('sc_status',200)
    //                         ->get();
    //     dd($api);
    //     $fileName = "api_reports.xlsx";

    //     $serialiser = new ApiReportsSerialiser();
    //     $excel = Exporter::make('Excel');
    //     $excel->load($api_reports);
    //     // dd($excel);
    //     $excel->setSerialiser($serialiser);

    //     return $excel->stream($fileName);
    // }
    public function download_api_report(Request $request){
        $start_date = $request->start_date." 00:00:00";
        $end_date = $request->end_date." 23:59:59";
        // dd($end_date);
        

        // $api = SmsDesktop::where('user_id',Auth::id())

        //                     ->where('sd_targeted_time','>=', $start_date)
        //                     ->where('sd_targeted_time', '<=', $end_date)
                            
        //                     ->get();
        // dd($api);
        return Excel::download(new SmsDesktopTotalExport($start_date,$end_date), 'archieved_report.xlsx');

        // $fileName = "api_reports.xlsx";

        // $serialiser = new ApiReportsSerialiser();
        // $excel = Exporter::make('Excel');
        // $excel->load($api_reports);
        // // dd($excel);
        // $excel->setSerialiser($serialiser);

        // return $excel->stream($fileName);
    }

    public function download_dynamic_archived_report($campaign_id)
    {
        return Excel::download(new SmsDesktopExport($campaign_id), 'archieved_report.xlsx');
    }

    public function reportDownload(Request $request){
        // if ($request->has('start_date') && $request->has('end_date')) {
        //     $start_date = $request->start_date;
        //     $end_date = $request->end_date;
        //     $q_start_date = $request->start_date." 00:00:00";
        //     $q_end_date = $request->end_date." 23:59:59";
        // }else{
        //     $start_date = Carbon::now()->subDays(15)->format('Y-m-d');
        //     $end_date = Carbon::now()->format('Y-m-d');
        //     $q_start_date = Carbon::now()->subDays(15);
        //     $q_end_date = Carbon::now();
        // }
        // $reports = SmsDesktop::where('user_id',Auth::id())
        //                         ->where('created_at','>=',$q_start_date)
        //                         ->where('created_at','<=',$q_end_date)
        //                         ->get();
                                
        // return Excel::download(new SmsDesktopTotalExport($q_start_date,$q_end_date), 'archieved_report.xlsx');
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $q_start_date = $request->start_date." 00:00:00";
            $q_end_date = $request->end_date." 23:59:59";
        }else{
            $start_date = Carbon::now()->subDays(15)->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $q_start_date = Carbon::now()->subDays(15);
            $q_end_date = Carbon::now();
        }
        // $reports = SmsCampaign::with('sender', 'operator')
        //                         ->where('user_id',Auth::id())
        //                         ->where('created_at','>=',$q_start_date)
        //                         ->where('created_at','<=',$q_end_date)
        //                         ->get();
      
        return Excel::download(new SmsDesktopTotalExport($q_start_date,$q_end_date), 'archieved_report.xlsx');
        // $fileName =  "report.xlsx";

        // $serialiser = new ReportDownloadSerialiser();
        
        // return Excel::download($serialiser, $fileName);
       
    }


    /*start campaign dlr*/
    public function todays_campaign_sms_report()
    {
        $todays_campaigns = SmsCampaignId::with('sender')
            ->where('user_id', Auth::id())
            ->where('sci_deal_type', '2')
            ->where('sci_targeted_time', '>=', Carbon::now()->subHours(24))
            ->orderBy('id', 'desc')
            ->get();


        return view('user.reports.campaign-dlr.todays_campaign_sms_report', compact('todays_campaigns'));
    }

    public function archived_campaign_report()
    {
        $archived_campaigns = SmsCampaignId::with('sender')
            ->where('user_id', Auth::id())
            ->where('sci_deal_type', '2')
            ->where('sci_targeted_time', '<=', Carbon::now()->subHours(24))
            ->orderBy('id', 'desc')
            ->get();
        return view('user.reports.campaign-dlr.archived_campaign_report', compact('archived_campaigns'));
    }
    /*end campaign dlr*/


    /*start campaign dlr*/

    public function pending_sms_report() {
        $schedule_pending = SmsCampaignId::where('user_id', Auth::id())
            ->where('sci_campaign_type', 2)->where('sci_targeted_time', '>', Carbon::now() )->get();
        return view('user.reports.schedule-sms.pending_sms', compact('schedule_pending'));
    }

    public function today_sms_report() {
        $start_time = Carbon::now()->format('Y-m-d'). " 00:00:00";
        // var_dump(date('Y-m-d H:i:s', strtotime($start_time)));
        // dd($start_time);
        $end_time = Carbon::now();

        $schedule_today_sent_sms = SmsCampaignId::where('user_id', Auth::id())
        ->where('sci_campaign_type', 2)->whereBetween('sci_targeted_time', [$start_time, $end_time])->get();
        return view('user.reports.schedule-sms.today_pending_sms', compact('schedule_today_sent_sms'));
    }

    public function schedule_archieved_sms_report()
    {
        $schedule_campaign_sms = SmsCampaignId::with('sender')
            ->where('user_id', Auth::id())
            ->where('sci_deal_type', '2')
            ->where('sci_campaign_type', '2')
            ->orderBy('id', 'desc')
            ->get();
        return view('user.reports.schedule-sms.archieved_pending_sms', compact('schedule_campaign_sms'));
    }

    public function schedule_general_sms_report()
    {
        $schedule_general_sms = SmsCampaignId::with('sender')
            ->where('user_id', Auth::id())
            ->where('sci_deal_type', '1')
            ->where('sci_campaign_type', '2')
            ->orderBy('id', 'desc')
            ->get();

        return view('user.reports.schedule-sms.general_sms_send', compact('schedule_general_sms'));
    }

    public function change_shedule_sms_time()
    {
        $campaign_id = request()->campaign_id;
        $campaign_id_for_pending_table = SmsCampaignId::where('sci_campaign_id', '=', $campaign_id)->value('id');

        $target_time = request()->new_date_time;
        $target_time = date('Y-m-d H:i:s', strtotime($target_time));

        DB::beginTransaction();
        try{
            SmsCampaignId::where('sci_campaign_id', '=', $campaign_id )->update([
                'sci_targeted_time' => $target_time
            ]);
            SmsCamPending::where('campaign_id', '=', $campaign_id_for_pending_table)->update([
                'scp_target_time' => $target_time
            ]);

        }catch(\Exception $e){
            DB::rollBack();
            session()->flash('message', 'Something went wrong');
            session()->flash('type', 'danger');
            return back();
        }
        DB::commit();
        session()->flash('message', 'Target Time Changed succesfully');
        session()->flash('type', 'success');
        return back();
    }
    /*end campaign dlr*/
}
