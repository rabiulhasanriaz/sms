@extends('admin.master')

@section('extra_operation_menu_class','open')
@section('delete_data_menu_class','active')
@section('content')
<div class="right_col" role="main">
	<div class="">
	  <div class="page-title">
		@include('admin.partials.session_messages')
		<div class="title_left">
		  <h3>Delete Data </h3>
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
			  <h2>Delete Data </h2>
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
					  <div class="card-box ">
						<div class="alert alert-danger alert-sm">
							<strong>Importants ! </strong>
							This operation will delete all sms information before 
								<span class="badge" style="font-weight: bold">
									<!-- {{ date('Y').' - '.(intval((date('m')))-1).' - '.date('d') }} -->
									{{ Carbon\Carbon::now()->subMonth(1)->format('Y - M - d - h : i : s a') }}
								</span> 
					
						</div>
					
						@if( session()->has('delete_info') )
							<div class="alert alert-info">
								<span>{{ session('delete_info') }}</span>
							</div>
						@endif
					
						@php
							
							$total_dynamic_sms = App\Model\SmsDesktop::count();
							
							
							$has_to_delete_dynamic_sms = App\Model\SmsDesktop::where('updated_at','<', Carbon\Carbon::now()->subMonth(1) )->count();
					
							
							$available_dynamic_sms = $total_dynamic_sms - $has_to_delete_dynamic_sms;
						@endphp
					
						<div class="row">
							<div class="col-md-4 col-md-offset-4" style="border: 1px solid black; border-radius: 3px; padding: 20px;">
								<ul>
									
									
								
					
									
					
									<li>{{ $total_dynamic_sms }} Dynamic sms information is existing now </li>
									
									<li>{{ $has_to_delete_dynamic_sms }} Dynamic sms information will be deleted. </li>
					
									<li>After deleting {{ $available_dynamic_sms }} Dynamic sms information will be exist.</li>
								</ul>
								
								<form method="POST" action="{{ route('admin.deleteDataBeforeOneMonth') }}">
								@csrf
									<input type="submit" type="button" class="btn btn-danger btn-sm pull-right" value="Delete" name="delete_data" onclick="return confirm('Are you sure want to delete ?');">
								</form>	
							</div>
					
							
						</div>
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
