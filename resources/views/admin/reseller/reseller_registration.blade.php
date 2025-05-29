@extends('admin.master')

@section('reseller_menu_class','open')
@section('reseller_registration_menu_class', 'current-page')
@section('content')
<div class="right_col" role="main">
<div class="">
    @include('admin.partials.session_messages')
    <div class="page-title">
        <div class="title_left">
            <h3>Reseller Registration</h3>
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
                    <h2>Reseller Registration Form</small></h2>
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
                    <form action="{{ route('admin.reseller.store') }}" method="post" class="form-horizontal" role="form"
                    enctype="multipart/form-data" >
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
                                <input class="form-control" class='optional' name="reseller_name" type="text" value="{{ old('reseller_name') }}"/>
                                @if ($errors->has('reseller_name'))
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
                                <input class="form-control date" autocomplete="off" id='start' data-date-format="yyyy-mm-dd" type="text" name="dob" required='required'></div>
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
                            <label class="col-form-label col-md-3 col-sm-3  label-align">Logo<span class="required">*</span></label>
                            <div class="col-md-6 col-sm-6">
                                <input class="form-control" id='' type="file" name="image" required='required'></div>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="{{asset('assets/newTemp/vendors/validator/multifield.js')}}"></script>
    <script src="{{asset('assets/newTemp/vendors/validator/validator.js')}}"></script>
    {{-- <script src="{{ asset('assets') }}/js/data-mask.js" type="text/javascript"></script> --}}
    @include('admin.ajax.check_existence')
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
    
    
    
    <!-- Javascript functions	-->
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
	</script>

    <!--<script>-->
        // initialize a validator instance from the "FormValidator" constructor.
        // A "<form>" element is optionally passed as an argument, but is not a must
    <!--    var validator = new FormValidator({-->
    <!--        "events": ['blur', 'input', 'change']-->
    <!--    }, document.forms[0]);-->
        // on form "submit" event
    <!--    document.forms[0].onsubmit = function(e) {-->
    <!--        var submit = true,-->
    <!--            validatorResult = validator.checkAll(this);-->
    <!--        console.log(validatorResult);-->
    <!--        return !!validatorResult.valid;-->
    <!--    };-->
        // on form "reset" event
    <!--    document.forms[0].onreset = function(e) {-->
    <!--        validator.reset();-->
    <!--    };-->
        // stuff related ONLY for this demo page:
    <!--    $('.toggleValidationTooltips').change(function() {-->
    <!--        validator.settings.alerts = !this.checked;-->
    <!--        if (this.checked)-->
    <!--            $('form .alert').remove();-->
    <!--    }).prop('checked', false);-->

    <!--</script>-->
@endsection