@extends('reseller.master')


@section('employee_menu_class','open')
@section('employee_registration_menu_class','active')

@section('content')
<div class="right_col" role="main">
    <div class="">
        @include('reseller.partials.session_messages')
        @if($current_employee_total >= Auth::user()->employee_limit)
            <p class="alert alert-info">Your Emplyee Limit is exceed, Please contact your reseller.</p>
        @endif
        <div class="page-title">
            <div class="title_left">
                <h3>Employee Registration</h3>
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
                        <h2>Employee Registration Form</small></h2>
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
                        <form action="{{ route('reseller.employee.store') }}" method="post" class="form-horizontal" role="form" enctype="multipart/form-data" >
                        @csrf
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">Employee Name<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" class='optional' name="employee_name" type="text" value="{{ old('reseller_name') }}"/>
                                    
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">email<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" name="employee_email" class='email' required="required" type="email" value="{{ old('email') }}"/>
                                    
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">Phone<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" type="text" class='' name="employee_phone" required='required' onkeyup="checkPhoneExistence(this.value)" value="{{ old('phone') }}"/>
                                    <span class="invalid-phone text-danger"></span>
                                    <span class="valid-phone text-success"></span>
                                    
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">Comission <span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" class='date' type="text" name="employee_commision" required='required' value="{{ old('designation') }}"></div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">Password<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" type="password" id="password1" name="employee_password" required value="{{ old('password') }}"/>
                                    
                                    <span style="position: absolute;right:15px;top:7px;" onclick="hideshow()" >
                                        <i id="slash" class="fa fa-eye-slash"></i>
                                        <i id="eye" class="fa fa-eye"></i>
                                    </span>
                                    
                                </div>
                            </div>
                           
                            
                            
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">Picture<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" id='' type="file" name="image" required='required'>
                                </div>
                            </div>

                            
                            
                            <div class="ln_solid">
                                <div class="form-group">
                                    <div class="col-md-6 offset-md-3">
                                        <button type='submit' class="btn btn-primary" @if($current_employee_total >= Auth::user()->employee_limit)
                                            {{ 'disabled' }}
                                        @endif>Submit</button>
                                        <button type='reset' class="btn btn-success">Reset</button>
                                    </div>
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
    <script src="{{ asset('assets') }}/js/data-mask.js" type="text/javascript"></script>
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
    <script>
		function hideshow(){
			var password = document.getElementById("password1");
			var slash = document.getElementById("slash");
			var eye = document.getElementById("eye");
			
			if(password.type === 'password'){
				password.type = "text";
				slash.style.display = "block";
				eye.style.display = "none";
			}
			else{
				password.type = "password";
				slash.style.display = "none";
				eye.style.display = "block";
			}

        }
        
        document.forms[0].onreset = function(e) {
            validator.reset();
        };
	</script>
    @include('reseller.ajax.check_existance')
@endsection