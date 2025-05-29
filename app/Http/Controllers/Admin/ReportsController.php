<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LoadCampaign30day;
use App\Model\SmsCampaign_24h;
use App\Model\SmsDesktopCampaignId;
use App\Model\User;
use App\Model\SmsCampaign;
use Auth;
use DB;
use PDF;
use Carbon\Carbon;

class ReportsController extends Controller
{
        public function sms_flexi_reports(){
            $q_start_date = Carbon::now()->subDay();
            $q_end_date = Carbon::now();
            // dd($q_end_date);

            $sms_user = SmsCampaign_24h::groupBy('user_id')
                                        ->get();
            
            $flexi_user = LoadCampaign30day::groupBy('user_id')
                                        ->where('created_at','>', $q_start_date)
                                        ->where('created_at','<', $q_end_date)
                                        ->get();
            return view('admin.reports.sms_flexi_reports',compact('sms_user','flexi_user'));
        }

        public function operator_reports(Request $request){
            // dd($request->all());
            // dd($request->operator_id);
            // $date = Carbon::now()->subDays(7);
            // dd($date);
            if ($request->has('start_date') && $request->has('end_date')) {
                $start = $request->start_date;
                $end = $request->end_date;
                $a = Carbon::parse($request->start_date);
                $b = Carbon::parse($request->end_date);
                $q_start_date = $request->start_date." 00:00:00";
                $q_end_date = $request->end_date." 23:59:59";
            }else{
                $start = Carbon::now()->subDays(7)->format('Y-m-d');
                $end = Carbon::now()->format('Y-m-d');
                $a = Carbon::parse(now()->subDays(7)->format('Y-m-d'));
                $b = Carbon::parse(now()->format('Y-m-d'));
                $q_start_date = Carbon::now()->subDays(7);
                $q_end_date = Carbon::now();
            }
            $days = $a->diffInDays($b);
            $sms_report = SmsCampaign::with('operator')
                                     ->select(DB::raw("count(*) as total,operator_id"),DB::raw("sum(sc_sms_cost) as total_cost"))
                                     ->where('sc_sms_type',2)
                                     ->where('created_at','>=',$q_start_date)
                                     ->where('created_at','<=',$q_end_date)
                                     ->groupBy('operator_id')
                                     ->get();
                                    //  dd($sms_report);
            $nonMaskingReport = SmsCampaignId::where('sci_sms_type', 1)
                                                ->whereIn('sci_sender_operator', [1,2,3,4])
                                                ->select('sci_sender_operator',DB::raw("sum(sci_total_submitted) as total"),DB::raw("sum(sci_total_cost) as total_cost"))
                                                ->where('sci_targeted_time', '>=', $q_start_date)
                                                ->where('sci_targeted_time', '<=', $q_end_date)
                                                ->where(function ($query) {
                                                    $query->where('sci_from_api',1);
                                                    $query->orWhere('sci_from_api',NULL);
                                                })
                                                ->groupBy('sci_sender_operator')
                                                ->get();

            $flexi_report = LoadCampaign30day::select(DB::raw('count(*) as total,operator_id'),DB::raw('sum(campaign_price) as total_cost'))
                                    ->where('created_at', '>=', $q_start_date)
                                    ->where('created_at', '<=', $q_end_date)
                                    ->groupBy('operator_id')
                                    ->get();
                                    //  dd($nonMaskingReport);
            return view('admin.reports.operator_reports',compact('sms_report','flexi_report','nonMaskingReport','start','end','days'));
        }
        
        
        public function user_reports(Request $request){
            // dd($request->all());
            // dd($request->operator_id);
            $userGet = $request->user_id;
            $users = User::where('role',5)->get();

            $date = Carbon::now()->subDays(7);
            // dd($date);
            if ($request->has('start_date') && $request->has('end_date')) {
                $start = $request->start_date;
                $end = $request->end_date;
                $a = Carbon::parse($request->start_date);
                $b = Carbon::parse($request->end_date);
                $q_start_date = $request->start_date." 00:00:00";
                $q_end_date = $request->end_date." 23:59:59";
            }else{
                $start = Carbon::now()->subDays(7)->format('Y-m-d');
                $end = Carbon::now()->format('Y-m-d');
                $a = Carbon::parse(now()->subDays(7)->format('Y-m-d'));
                $b = Carbon::parse(now()->format('Y-m-d'));
                $q_start_date = Carbon::now()->subDays(7);
                $q_end_date = Carbon::now();
            }
            
            $userReports = SmsDesktopCampaignId::where('sdci_sms_type', 1)
                                                ->where('user_id',$userGet)
                                                ->select(DB::raw("sum(sdci_total_submitted) as total"),DB::raw("sum(sdci_total_cost) as total_cost"))
                                                ->where('sdci_targeted_time', '>=', $q_start_date)
                                                ->where('sdci_targeted_time', '<=', $q_end_date)
                                                ->groupBy('user_id')
                                                
                                                ->get();
            // dd($nonMaskingReport);

            // $flexi_report = LoadCampaign30day::select(DB::raw('count(*) as total,operator_id'),DB::raw('sum(campaign_price) as total_cost'))
            //                         ->where('created_at', '>=', $q_start_date)
            //                         ->where('created_at', '<=', $q_end_date)
            //                         ->groupBy('operator_id')
            //                         ->get();
            //                         //  dd($nonMaskingReport);
            return view('admin.reports.user_reports',compact('userReports','start','end','users','userGet'));
        }
        
        public function reportsPdf(Request $request){
            $userGet = $request->user;
            

            $date = Carbon::now()->subDays(7);
            // dd($date);
            if ($request->has('start_date') && $request->has('end_date')) {
                $start = $request->start_date;
                $end = $request->end_date;
                $a = Carbon::parse($request->start_date);
                $b = Carbon::parse($request->end_date);
                $q_start_date = $request->start_date." 00:00:00";
                $q_end_date = $request->end_date." 23:59:59";
            }else{
                $start = Carbon::now()->subDays(7)->format('Y-m-d');
                $end = Carbon::now()->format('Y-m-d');
                $a = Carbon::parse(now()->subDays(7)->format('Y-m-d'));
                $b = Carbon::parse(now()->format('Y-m-d'));
                $q_start_date = Carbon::now()->subDays(7);
                $q_end_date = Carbon::now();
            }
            
            $userReports = SmsDesktopCampaignId::where('sdci_sms_type', 1)
                                                ->where('user_id',$userGet)
                                                ->select(DB::raw("sum(sdci_total_submitted) as total"),DB::raw("sum(sdci_total_cost) as total_cost"))
                                                ->where('sdci_targeted_time', '>=', $q_start_date)
                                                ->where('sdci_targeted_time', '<=', $q_end_date)
                                                ->groupBy('user_id')
                                                
                                                ->get();
            // return view('admin.reports.reports-pdf',compact('userReports','userGet'));
        
            $pdf = PDF::loadView('admin.reports.reports-pdf',compact('userReports','userGet'));
            return $pdf->download('sms-bill-report.pdf');
        }
        

        

}
