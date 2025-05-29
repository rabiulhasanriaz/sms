@extends('reseller.master')

@section('content')
<div class="right_col" role="main">
<div class="">
    @include('reseller.partials.all_error_messages')
	@include('reseller.partials.session_messages')
    <div class="page-title">
        <div class="title_left">
            <h3>Profile</h3>
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
                    <h2>Password Change</small></h2>
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
                    <div class="col-sm-6 offset-sm-3">
                        <form action="{{ route('reseller.change-password') }}" method="post">
							@csrf
			
							<div class="form-group">
								<label for="old">Old password :</label>
								<input type="password" name="old_password" class="form-control" id="old" placeholder="Old Password" required="">
							</div>
							<div class="form-group">
								<label for="new">New password :</label>
								<input type="password" name="new_password" class="form-control" id="new" placeholder="New Password" required="">
							</div>
							<div class="form-group">
								<label for="re">Re-password :</label>
								<input type="password" name="re_password" class="form-control" id="re" placeholder="Re-Password">
							</div>
							<div class="form-group">
								<input type="submit" class="btn btn-sm btn-primary" value="Change password">
							</div>
						</form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
</div>
@endsection

