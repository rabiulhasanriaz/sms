@extends('reseller.master')

@section('dashboard_menu_class','active')

@section('content')
<div class="right_col" role="main">
    <div class="">
        @include('reseller.partials.session_messages')
        <div class="page-title">
            <div class="title_left">
                <h3>Employee</h3>
            </div>
    
            <div class="title_right">
                <div class="col-md-5 col-sm-5  form-group pull-right top_search">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search for...">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button">Go!</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Employee Edit</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a class="dropdown-item" href="#">Settings 1</a>
                                    </li>
                                    <li><a class="dropdown-item" href="#">Settings 2</a>
                                    </li>
                                </ul>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-10 offset-sm-2">
                                @include('reseller.partials.session_messages')

                                <form action="{{ route('reseller.employee.update', $employee->id) }}" method="post" class="form-horizontal" role="form"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="field item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-company-1"> Employee name : </label>
                                        <div class="col-md-6 col-sm-6">
                                            <input type="text" id="employeeName" placeholder="Employee name" name="employee_name"
                                                class="form-control" required="" value="{{ $employee->name }}"/>
                                            <span class="help-inline col-xs-12 col-sm-7">
                                                <span class="middle text-danger" id="employeeShow"> ** </span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="field item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3  label-align" for="employeeEmail"> Email : </label>
                                        <div class="col-md-6 col-sm-6">
                                            <input type="text" id="employeeEmail" placeholder="Employee Email" name="employee_email"
                                                class="form-control" required="" value="{{ $employee->email }}"/>
                                            <span class="help-inline col-xs-12 col-sm-7">
                                                <span class="middle text-danger" id="employeeShow"> ** </span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="field item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-phone-1"> Phone : </label>
                                        <div class="col-md-6 col-sm-6">
                                            <input type="text" id="mobileNumber" placeholder="Phone" name="employee_phone"
                                                class="form-control input-mask-phone" onkeyup="checkPhoneExistence(this.value)"
                                                value="{{ $employee->phone }}" data-mask="___________" required=""/>
                                            <span class="help-inline col-xs-12 col-sm-7">
                                                <span class="middle text-danger" id="status"> ** </span>
                                                <span class="invalid-phone text-danger"></span>
                                                <span class="valid-phone text-success"></span>
                                                
                                            </span>
                                        </div>
                                    </div>

                                    <div class="field item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3  label-align" for="employeeEmail"> Commision : </label>
                                        <div class="col-md-6 col-sm-6">
                                            <input type="text" id="employeeCommision" placeholder="Employee Commission" name="employee_commision"
                                                class="form-control" required="" value="{{ $employee->commission }}"/>
                                            <span class="help-inline col-xs-12 col-sm-7">
                                                <span class="middle text-danger" id="employeeShow"> ** </span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="field item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3  label-align" for="employeeEmail"> Password : </label>
                                        <div class="col-md-6 col-sm-6">
                                            <input type="password" id="employeePassword" placeholder="Employee Password" name="employee_password"
                                                class="form-control" required="" value=""/>
                                            <span class="help-inline col-xs-12 col-sm-7">
                                                <span class="middle text-danger" id="employeeShow"> ** </span>
                                            </span>
                                        </div>
                                    </div>


                                    
                                    <div class="field item form-group" id="employee_logo" style="">
                                        <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-image-1"> Logo : </label>
                                        <div class="col-md-6 col-sm-6">
                                            <input type="file" name="image" id="form-image-1">
                                        </div>
                                    </div>

                                    <div class="clearfix form-group">
                                        <div class="col-md-6 offset-md-3">
                                            <input type="submit" class="btn btn-info" value="Update">
                                            &nbsp; &nbsp; &nbsp;
                                            <button class="btn btn-danger" type="reset">
                                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                                Reset
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div><!-- /.col -->

        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js/data-mask.js" type="text/javascript"></script>
    @include('reseller.ajax.check_existance')
@endsection
