<?php

namespace App\Http\Controllers\Admin;

use App\Model\AccUserCreditHistory;
use App\Model\SmsCamPending;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    //

    public function index(){
        $data['active_user'] = User::where('status', '1')->whereIn('role', [4,5])->count();
        $data['suspend_user'] = User::where('status', '2')->whereIn('role', [4,5])->count();

        $data['last_month_sms'] = AccUserCreditHistory::where('created_at', '>', Carbon::now()->subMonth(1))->sum('uch_sms_count');
        $data['total_sms'] = AccUserCreditHistory::sum('uch_sms_count');
        $dateS = Carbon::now()->startOfMonth()->subMonth(11);
        $dateE = Carbon::now();
        $data['monthly_sms'] = AccUserCreditHistory::select(DB::raw('sum(uch_sms_count) as total_sms'), DB::raw('sum(uch_sms_cost) as total_sms_cost'),  DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
            ->whereBetween('created_at',[$dateS,$dateE])
            ->groupby('year','month')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.index')->with('data', $data);
    }

    public function loggedInUsers()
    {
        $logged_users = User::whereIn('login_status', [1, 2])
            ->where('last_active_time', '>=', Carbon::now()->subMinutes(2))
            ->where('id', '!=', auth()->id())
            ->get();
        return view('admin.allLoggedUsers', ['logged_users' => $logged_users]);
    }

    public function getCount()
    {
        return SmsCamPending::all()->count();
    }
    public function getCost(){
        $cost = SmsCamPending::all()->sum('scp_sms_cost');
        $total = number_format($cost,2);
        return $total;
    }
}
