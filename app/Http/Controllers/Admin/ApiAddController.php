<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiAddController extends Controller
{
    public function api_add(){
        return view('admin.api-add');
    }
}
