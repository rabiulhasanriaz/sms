@extends('reseller.master')

@section('user_registration_menu_class','current-page')
@section('user_menu_class','open')
@section('content')
<div class="right_col" role="main">
    <div class="">
        @include('reseller.partials.session_messages')
        <div class="page-title">
            <div class="title_left">
                <h3>User Registration</h3>
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
                        <h2>User Registration Form</small></h2>
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
                        <form action="{{ route('reseller.user.store') }}" method="post" class="form-horizontal" role="form" enctype="multipart/form-data" >
                        @csrf
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">Company name<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" name="company_name" placeholder="ex. John f. Kennedy" required="required" value="{{ old('company_name') }}"/>
                                    @if ($errors->has('company_name'))
                                        <span class="text-danger">{{ $errors->first('company_name') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">Name<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" class='optional' name="user_name" type="text" value="{{ old('user_name') }}"/>
                                    @if ($errors->has('user_name'))
                                        <span class="text-danger">{{ $errors->first('user_name') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">email<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" name="email" class='email' required="required" type="email" onkeyup="checkEmailExistence(this.value)" value="{{ old('email') }}"/>
                                    <span class="invalid-email text-danger"></span>
                                    <span class="valid-email text-success"></span>
                                    @if ($errors->has('email'))
                                        <span class="text-danger retErrEmail">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">Phone<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" type="text" class='' name="phone" required='required' onkeyup="checkPhoneExistence(this.value)" value="{{ old('phone') }}"/>
                                    <span class="invalid-phone text-danger"></span>
                                    <span class="valid-phone text-success"></span>
                                    @if ($errors->has('phone'))
                                        <span class="text-danger retErrPhone">{{ $errors->first('phone') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">Password<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" type="password" id="password1" name="password" required value="{{ old('password') }}"/>
                                    
                                    <span style="position: absolute;right:15px;top:7px;" onclick="hideshow()" >
                                        <i id="slash" class="fa fa-eye-slash"></i>
                                        <i id="eye" class="fa fa-eye"></i>
                                    </span>
                                    @if ($errors->has('password'))
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">NID No. <span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" class='date' type="text" name="nid" required='required' value="{{ old('designation') }}"></div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">D.O.B<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control date" data-date-format="yyyy-mm-dd" autocomplete="off" id='start' type="text" name="dob" required='required'></div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">Designation<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" class='' type="text" name="designation" required='required' value="{{ old('designation') }}"></div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">Address<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" class='' type="text" name="address" required='required'  value="{{ old('address') }}"></div>
                            </div>
                            <div class="field item form-group">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">Access Type<span class="required">*</span></label>
                                <div class="col-md-2 col-sm-2">
                                    <input type="radio" class="ace" name="status" onchange="show_terget(this.value)" value="Reseller" required="">
                                    <span class="lbl"> Reseller </span>
                                </div>
                                <div class="col-md-2 col-sm-2">
                                    <input type="radio" id="permission_user" class="ace" name="status" onchange="show_terget(this.value)" value="User"  required="">
                                    <span class="lbl"> User </span>
                                </div>
                            </div>
                            <div class="field item form-group" id="user_logo" style="display: none;">
                                <label class="col-form-label col-md-3 col-sm-3  label-align">Logo</label>
                                <div class="col-md-6 col-sm-6">
                                    <input class="form-control" id='' type="file" name="image" >
                                </div>
                            </div>

                            <div class="field item form-group" id="permission" style="display: none;">
                                <label class="col-form-label col-md-3 col-sm-3  label-align" for=""> Permission </label>
                                <div class="col-md-6 col-sm-6">
                                    <input type="checkbox" name="permission" id="" value="1"> SMS
                                </div>
                            </div>
                            
                            <div class="ln_solid">
                                <div class="form-group">
                                    <div class="col-md-6 offset-md-3">
                                        <button type='submit' class="btn btn-primary">Submit</button>
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
@section('custom_style')
<link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap-datepicker3.min.css"/>
@endsection


@section('custom_script')
<script src="{{ asset('assets') }}/js/bootstrap-datepicker.min.js"></script>

<script>
    // $('#view_archived_report').DataTable();
    $(document).ready(function () {
        $('#start').datepicker({
            autoclose: true,
            todayHighlight: true,
        });
    });
</script>
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

    @include('admin.ajax.check_existence')
@endsection