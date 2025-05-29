<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Model\User;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function export() 
    {
        
        return Excel::download(new UsersExport, 'users.xlsx')->withHeadings('#', 'Name', 'E-mail');
        
    }
}
