@extends('admin.master')

@section('reseller_menu_class','open')
@section('reseller_list_menu_class', 'active')
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
                        <form action="{{ route('admin.reseller.update', $userInfo->id) }}" method="post" class="form-horizontal" role="form"
							enctype="multipart/form-data">
		  
						  @csrf
		  
						  <div class="field item form-group">
							  <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-company-1"> Company name : </label>
		  
							  <div class="col-md-6 col-sm-6">
								  <input type="text" id="companyName" placeholder="Company name" name="company_name"
										 class="form-control" required="" value="{{ $userInfo->company_name }}"/>
								  <span class="help-inline col-xs-12 col-sm-7">
									  <span class="middle text-danger" id="companyShow"> ** </span>
		  
									  @if ($errors->has('company_name'))
										  <span class="text-danger">{{ $errors->first('company_name') }}</span>
									  @endif
								  </span>
							  </div>
						  </div>
						  <div class="field item form-group">
							  <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-field-1"> Name : </label>
		  
							  <div class="col-md-6 col-sm-6">
								  <input type="text" id="ResellerName" placeholder="Name" name="reseller_name"
										 class="form-control" required="" value="{{ $userInfo->userDetail['name'] }}"/>
								  <span class="help-inline col-xs-12 col-sm-7">
									  <span class="middle text-danger" id="resellerName_Show"> ** </span>
									  @if ($errors->has('reseller_name'))
										  <span class="text-danger">{{ $errors->first('reseller_name') }}</span>
									  @endif
								  </span>
							  </div>
						  </div>
						  <div class="field item form-group">
							  <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-email-1"> Email : </label>
		  
		  
							  <div class="col-md-6 col-sm-6">
								  <input type="email" id="EmaileNumber" placeholder="Email" name="email"
										 class="form-control" required="" onkeyup="checkEmailExistenceForUpdate(this.value, '{{$userInfo->id}}')" value="{{ $userInfo->email }}"/>
								  <span class="help-inline col-xs-12 col-sm-7">
									  <span class="middle text-danger" id="Emailestate"> ** </span>
									  <span class="invalid-email text-danger"></span>
									  <span class="valid-email text-success"></span>
									  @if ($errors->has('email'))
										  <span class="text-danger retErrEmail">{{ $errors->first('email') }}</span>
									  @endif
								  </span>
							  </div>
						  </div>
						  <div class="field item form-group">
							  <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-phone-1"> Phone : </label>
		  
							  <div class="col-md-6 col-sm-6">
								  <input type="text" id="mobileNumber" placeholder="Phone" name="phone"
										 class="form-control input-mask-phone" onkeyup="checkPhoneExistenceForUpdate(this.value, '{{$userInfo->id}}', event)" value="{{ $userInfo->cellphone }}" data-mask="___________" required=""/>
								  <span class="help-inline col-xs-12 col-sm-7">
									  <span class="middle text-danger" id="status"> ** </span>
									  <span class="invalid-phone text-danger"></span>
									  <span class="valid-phone text-success"></span>
									  @if ($errors->has('phone'))
										  <span class="text-danger retErrPhone">{{ $errors->first('phone') }}</span>
									  @endif
								  </span>
							  </div>
						  </div>
		  
		  
						  <div class="field item form-group">
							  <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-pass-2"> Password : </label>
		  
							  <div class="col-md-6 col-sm-6">
								  <input type="password" id="form-pass-2" placeholder="Password" name="password"
										 class="form-control" value="" required=""/>
								  <span class="help-inline col-xs-12 col-sm-7">
									  <span class="middle text-danger"> ** </span>
									  @if ($errors->has('password'))
										  <span class="text-danger">{{ $errors->first('password') }}</span>
									  @endif
								  </span>
							  </div>
						  </div>
		  
						  <div class="field item form-group">
							  <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-designation-1"> Designation
								  : </label>
		  
							  <div class="col-md-6 col-sm-6">
								  <input type="text" id="form-designation-1" placeholder="Designation" class="form-control"
										 name="designation" value="{{ $userInfo->userDetail->designation }}" />
								  <span class="help-inline col-xs-12 col-sm-7">
									  @if ($errors->has('designation'))
										  <span class="text-danger">{{ $errors->first('designation') }}</span>
									  @endif
								  </span>
							  </div>
						  </div>
		  
		  
						  <div class="field item form-group">
							  <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-address-1"> Address : </label>
		  
							  <div class="col-md-6 col-sm-6">
								  <input type="text" id="form-address-1" placeholder="Address" class="form-control"
										 name="address" value="{{ $userInfo->userDetail->address }}"/>
								  <span class="help-inline col-xs-12 col-sm-7">
									  @if ($errors->has('address'))
										  <span class="text-danger">{{ $errors->first('address') }}</span>
									  @endif
								  </span>
							  </div>
						  </div>
		  
		  
						  <div class="field item form-group">
							  <label class="col-form-label col-md-3 col-sm-3  label-align" for="form-image-1"> Logo : </label>
		  
							  <div class="col-md-6 col-sm-6">
								  <input type="file" name="image" id="form-image-1">
							  </div>
							  <img src="{{ OtherHelpers::user_logo($userInfo->userDetail->logo) }}" alt="" style="max-width:60px;">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('custom_script')
	<script src="{{ asset('assets') }}/js/data-mask.js" type="text/javascript"></script>
	@include('admin.ajax.check_existence')
@endsection