        @php($permission = $user->permission))
		@if ($permission == 1)
			@php($sms_permission = true)
		@else
			@php($sms_permission = false)	
		@endif
@extends('reseller.master')

@section('user_list_menu_class','active')
@section('user_menu_class','open')

@section('content')
<div class="right_col" role="main">
    <div class="">
        @include('reseller.partials.session_messages')
        <div class="page-title">
            <div class="title_left">
                <h3>User</h3>
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
                        <h2>User Edit</h2>
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
                        <form action="{{ route('reseller.user.update', $user->id) }}" method="post" class="form-horizontal" role="form" enctype="multipart/form-data">
                            @csrf
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-company-1"> Company name : <span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" id="companyName" placeholder="Company name" name="company_name"
                                           class="form-control" required="" value="{{ $user->company_name }}"/>
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-field-1"> Name : <span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" id="UserName" placeholder="Name" name="user_name"
                                           class="form-control" required="" value="{{ $user->userDetail['name'] }}"/>
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-email-1"> Email : <span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="email" id="EmaileNumber" placeholder="Email" name="email"
                                           class="form-control" required="" value="{{ $user->email }}"/>
                                    
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-phone-1"> Phone : <span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" id="mobileNumber" placeholder="Phone" name="phone"
                                           class="form-control input-mask-phone" value="{{ $user->cellphone }}" required=""/>
                                    
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-pass-2"> Password : <span class="required">*</span></label>
            
                                <div class="col-md-6 col-sm-6">
                                    <input type="password" id="form-pass-2" placeholder="Password" name="password"
                                           class="form-control" value="" required=""/>
                                    
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-designation-1"> Designation
                                    : <span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" id="form-designation-1" placeholder="Designation" class="form-control"
                                           name="designation" value="{{ $user->userDetail->designation }}" required=""/>
                                   
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-address-1"> Address : <span class="required">*</span></label>
            
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" id="form-address-1" placeholder="Address" class="form-control"
                                           name="address" value="{{ $user->userDetail->address }}" required=""/>
                                    
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-designation-1"> Access type
                                    : </label>
                                <div class="col-md-6 col-sm-6">
                                    <div class="checkbox">
                                        <label>
                                            <input type="radio" class="ace" name="status" onchange="show_terget(this.value)"
                                                   value="Reseller" {{ ($user->role==4)?'checked':'' }} required="">
                                            <span class="lbl"> Reseller </span>
                                        </label>
                                        <label>
                                            <input type="radio" class="ace" name="status" onchange="show_terget(this.value)"
                                                   value="User" {{ ($user->role==5)?'checked':'' }} required="">
                                            <span class="lbl"> User </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
            
                            <div class="field item form-group" id="permission" style="display: none;">
                                <label class="col-form-label col-md-3 col-sm-3  label-align" for=""> Permission </label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="checkbox" name="permission" id="" value="1" {{ ($sms_permission)? 'checked' : '' }}> SMS
                                </div>
                            </div>
                            <div class="field item form-group" id="user_logo" style="display: none;">
                                <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-image-1"> Logo : </label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="file" name="image" id="form-image-1">
                                    <span><img src="{{ OtherHelpers::user_logo($user->userDetail->logo) }}" style="height: 60px;"></span>
                                </div>
                            </div>
                            <div class="clearfix form-group">
                                <div class="col-md-6 offset-md-3">
                                    <input type="submit" class="btn btn-primary" value="Update">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection



@section('custom_script')
    <script type="text/javascript">
        function show_terget(value) {
            if (value == 'User') {
                $('#user_logo').hide();
                $('#permission').show();           
            }
            else if (value == 'Reseller') {
                $('#user_logo').show();
                $('#permission').hide();           
            }
        }
        $("#checkAll").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
    </script>

    @include('admin.ajax.check_existence')
@endsection

