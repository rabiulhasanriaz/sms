<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Model\AccSmsBalance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PDF;

class SmsBillReportController extends Controller
{
    public function showBillReport(Request $request)
    {
        $total_balance_bd = 0;
        $debit1 = 0;
        $debit2 = 0;
        $credit = 0;
        $balance = 0;
        $balancebd = 0;
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $q_start_date = $request->start_date." 00:00:00";
            $q_end_date = $request->end_date." 23:59:59";
        } else{
            $start_date = Carbon::now()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $q_start_date = Carbon::now()->startOfDay();
            $q_end_date = Carbon::now()->endOfDay();
        }

        $transactions = AccSmsBalance::with('smsCampaignId', 'loadCampaignId')->where('asb_pay_to', Auth::id())
            ->where('asb_submit_time', '>=', $q_start_date)
            ->where('asb_submit_time', '<=', $q_end_date)
            ->get();


        $customerCredit = AccSmsBalance::where(['asb_pay_to' => Auth::id()])->where('asb_submit_time','<',$q_start_date)->sum('asb_credit');
        $customerDebit = AccSmsBalance::where(['asb_pay_to' => Auth::id()])->where('asb_submit_time','<',$q_start_date)->sum('asb_debit');
        $balancebd = $customerCredit - $customerDebit;

        return view('user.reports.bill_report', compact('transactions', 'start_date', 'end_date','balancebd'));

    }

    public function billReportDownload(Request $request)
    {
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $q_start_date = $request->start_date." 00:00:00";
            $q_end_date = $request->end_date." 23:59:59";
        } else{
            $start_date = Carbon::now()->subDays(7)->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $q_start_date = Carbon::now()->subDays(7);
            $q_end_date = Carbon::now();
        }

        $transactions = AccSmsBalance::with('smsCampaignId', 'loadCampaignId')->where('asb_pay_to', Auth::id())
            ->where('asb_submit_time', '>=', $q_start_date)
            ->where('asb_submit_time', '<=', $q_end_date)
            ->get();

        $customerCredit = AccSmsBalance::where(['asb_pay_to' => Auth::id()])->where('asb_submit_time','<',$q_start_date)->sum('asb_credit');
        $customerDebit = AccSmsBalance::where(['asb_pay_to' => Auth::id()])->where('asb_submit_time','<',$q_start_date)->sum('asb_debit');
        $balancebd = $customerCredit - $customerDebit;


        $data['transactions'] = $transactions;
        $data['balancebd'] = $balancebd;
       // return view('user.reports.bill_report_download', $data);
        $pdf = PDF::loadView('user.reports.bill_report_download', $data);
        return $pdf->download('sms-bill-report.pdf');
    }
}
