@extends('admin.master')

@section('all_logged_in_users_menu', 'active')
@section('content')
<div class="right_col" role="main">
	<div class="">
	  <div class="page-title">
		@include('admin.partials.session_messages')
		<div class="title_left">
		  <h3>All logged users </h3>
		</div>
		<div class="title_right">
		  <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
			<div class="input-group">
			  <input type="text" class="form-control" placeholder="Search for...">
			  <span class="input-group-btn">
				<button class="btn btn-secondary" type="button">Go!</button>
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
			  <h2>Current Logged Users  <small>List</small></h2>
			  <ul class="nav navbar-right panel_toolbox">
				<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
				</li>
				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
				  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
					  <a class="dropdown-item" href="#">Settings 1</a>
					  <a class="dropdown-item" href="#">Settings 2</a>
					</div>
				</li>
				<li><a class="close-link"><i class="fa fa-close"></i></a>
				</li>
			  </ul>
			  <div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="row">
					<div class="col-sm-12">
					  <div class="card-box table-responsive">
						<table id="dynamic-table" class="table table-striped table-bordered table-hover">
							<thead>
								 <tr>
									<th>SL</th>
									<th>Company Name</th>
									<th>User Name</th>
									<th>Customer Type</th>
									<th>Email</th>
									<th>Phone</th>
									<th class="hidden-480">Balance</th>
									<th class="hidden-480">Reseller</th>
									<th>Last Login Time</th>
									<th>Action</th>
								</tr>
							</thead>
			
							<tbody>
								@foreach($logged_users as $logged_user)
									<tr>
										<td>{{ $loop->iteration }}</td>
										<td>
											@if($logged_user->login_status == 1)
												<i class="ace-icon fa fa-circle" style="color: #00ffa3"></i>
											@elseif($logged_user->login_status == 2)
												<i class="ace-icon fa fa-circle-o" style="color: #cede7c"></i>
											@endif
											{{ $logged_user->company_name }}
										</td>
										<td>{{ $logged_user->userDetail['name'] }}</td>
										<td><p style='color:#428BCA;'>
												@if(($logged_user->role==1) || ($logged_user->role==2) || ($logged_user->role==3))
													Root User {{ $logged_user->role }}
												@elseif($logged_user->role==4)
													Reseller
												@elseif($logged_user->role==5)
													User
												@endif
											</p></td>
										<td>{{ $logged_user->email }}</td>
										<td class="hidden-480">{{ $logged_user->cellphone }}</td>
										<td class="hidden-480">{{ number_format(BalanceHelper::user_available_balance($logged_user->id), 2) }} <b>৳</b></td>
										<td class="hidden-480">{{ @$logged_user->parentInfo->company_name }}</td>
										<td class="hidden-480">
											@if($logged_user->last_login_time != null)
												{{ $logged_user->last_login_time->format('Y-m-d h:i:s a') }}
											@endif
										</td>
										<td>
											<div class="widget-toolbar no-border">
												<button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"
														aria-expanded="false">
													Action
													<i class="ace-icon fa fa-chevron-down icon-on-right"></i>
												</button>
												<ul class="dropdown-menu dropdown-primary dropdown-menu-right dropdown-caret dropdown-close">
													<li>
														<a href="{{ route('admin.reseller.priceView', $logged_user->id ) }}">
															<i class="ace-icon fa fa-search-plus bigger-130"></i> Price View
														</a>
													</li>
													<li>
														<a href="{{ route('admin.reseller.transactionHistory', $logged_user->id) }}"
														   class="tooltip-error" data-rel="tooltip" title="Account Details">
															<span class="label label-sm label-primary">Account</span>
														</a>
													</li>
													<li>
														@if($logged_user->status=='1')
															<a href="{{ route('admin.reseller.suspend', $logged_user->id) }}" class="tooltip-error" data-rel="tooltip" title="Conform">
																<span class="label label-sm label-warning">Suspend</span>
															</a>
														@else
															<a href="{{ route('admin.reseller.active', $logged_user->id) }}" class="" data-rel="tooltip" title="Conform">
																<span class="label label-sm label-success">Re-Active</span>
															</a>
														@endif
													</li>
													<li class="divider"></li>
													<li>
														<a class="green" href="{{ route('admin.reseller.edit', $logged_user->id) }}">
															<i class="ace-icon fa fa-pencil bigger-130"></i> Edit
														</a>
													</li>
													<li class="divider"></li>
													<li>
														<a href="{{ route('admin.reseller.goToThisAccount', $logged_user->id) }}"
														   class="tooltip-error" data-rel="tooltip" title="Account Details">
															<span class="label label-sm label-primary">Go to this account</span>
														</a>
													</li>
												</ul>
											</div>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
			</div>
			</div>
		</div>
	  </div>
		  </div>
		</div>
		
	  </div>
	</div>
	</div>
@endsection

@section('custom_style')
	<style type="text/css">

	</style>
@endsection

@section('custom_script')
	<style type="text/css">

	</style>
@endsection


