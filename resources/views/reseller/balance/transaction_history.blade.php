@extends('reseller.master')

@section('user_statement_menu_class','active')
@section('acc_details_menu_class','open')

@section('content')
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        @include('reseller.partials.session_messages')
        <div class="title_left">
          <h3>User Statements </h3>
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
              <h2>Transaction  <small>History</small></h2>
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
                        <table id="transiation-history-table" class="table table-bordered" style="background: #fff;">
							<thead>
								<tr>
									<th>SL</th>
									<th>Company name</th>
									<th>Name</th>
									<th>Mobile No.</th>
									<th>Credit </th>
									<th>Debit </th>
									<th>Available balance</th>
									
								</tr>
							</thead>
				
							<tbody>
							@php($serial=1)
							@php($total_credit=0)
							@php($total_debit=0)
							@php($total_available_balance=0)
							@foreach($resellers as $reseller)
								@php($total_credit += BalanceHelper::user_total_credit($reseller->id))
								@php($total_debit += BalanceHelper::user_total_debit($reseller->id))
								@php($total_available_balance += BalanceHelper::user_available_balance($reseller->id))
								<tr>
									<td>{{ $serial++ }}</td>
									<td>{{ $reseller->company_name }}</td>
									<td>{{ $reseller->userDetail['name'] }}</td>
									<td>{{ $reseller->cellphone }}</td>
									<td class="text-right">{{ number_format(BalanceHelper::user_total_credit($reseller->id), 2) }}</td>
									<td class="text-right">{{ number_format(BalanceHelper::user_total_debit($reseller->id), 2) }}</td>
									<td class="text-right">{{ number_format(BalanceHelper::user_available_balance($reseller->id), 2) }}</td>
									{{--
									<td>
										<a href="{{ route('reseller.user.transactionHistory', $reseller->id) }}" class="btn btn-xs btn-primary">Details</a>
									</td>
									--}}
								</tr>
							@endforeach
							</tbody>
								<tfoot>
									<tr bgcolor="#dcc">
										<th class="text-right" colspan="4">Total Amount:</th>
										
										<th class="text-right">{{ number_format($total_credit,2) }}</th>
										<th class="text-right">{{ number_format($total_debit,2) }}</th>
										<th class="text-right">{{ number_format($total_available_balance,2) }}</th>
										
									</tr>
								</tfoot>
				
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
    {{-- <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
    $('#employee-list-table').DataTable();
    </script> --}}
	<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.7/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
    <script>
    $(document).ready(function() {
    var table = $('#transiation-history-table').DataTable( {
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true
    } );
    } );
    </script>
@endsection

{{-- @section('custom_script')
	<script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
	<script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
	<script type="text/javascript">
	$('#transiation-history-table').DataTable();
	</script>
@endsection --}}
