@extends('reseller.master')

@section('suspend_user_menu_class','active')
@section('user_menu_class','open')
@section('content')
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        @include('admin.partials.session_messages')
        <div class="title_left">
          <h3>User </h3>
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
              <h2>User  <small>Suspend List</small></h2>
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
                        <table id="suspend-user-list-table" class="table table-striped table-bordered table-hover">
							<thead>
							<tr>
								<th class="abcd">SL</th>
								<th>Company name</th>
								<th>Name</th>
								<th>Email</th>
								<th class="">Phone</th>
								<th class="">Customar</th>
								<th>System</th>
							</tr>
							</thead>
							<tbody>
							@php($serial=1)
							@foreach($users as $user)
								<tr>
									<td>{{ $serial++ }}</td>
									<td>{{ $user->company_name }}</td>
									<td>{{ $user->userDetail['name'] }}</td>
									<td>{{ $user->email }}</td>
									<td class="">{{ $user->cellphone }}</td>
									<td class="">
										<p style='color:green;'>
											@if($user->role==4)
												Reseller
											@else
												User
											@endif
										</p>
									</td>
									<td>
										<div class="widget-toolbar no-border" style="float: none;">
											<a class="red" href="{{ route('reseller.user.delete', $user->id) }}" onclick="return confirm('Are you sure to delete');">
												<i class="ace-icon fa fa-trash-o bigger-130"></i>
											</a>
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.7/css/rowReorder.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.dataTables.min.css">
@endsection


@section('custom_script')

<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.7/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
<script>
    $(document).ready(function() {
    var table = $('#suspend-user-list-table').DataTable( {
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true
    } );
} );
</script>
@endsection
