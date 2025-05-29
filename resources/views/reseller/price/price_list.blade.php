@extends('reseller.master')

@section('price_menu_class','current-page')
@section('price_and_converage_menu_class','open')

@section('content')
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        @include('reseller.partials.session_messages')
        <div class="title_left">
          <h3>Price & Coverage </h3>
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
              <h2>Price  <small>List</small></h2>
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
                        <table class="table table-striped table-bordered table-hover" id="price_list">
							<thead>
								<tr>
									<th class="abcd">SL</th>
									<th>Country</th>
									<th>Operator</th>
									<th> Prefix </th>
									<th>Masking </th>
									<th>Non-Masking </th>
									<th>Dynamic</th>
									<th> Status </th>
								</tr>
							</thead>
				
							<tbody>
							@php($serial=1)
							@foreach($smsRates as $smsRate)
								<tr>
									<td>{{ $serial++ }}</td>
									<td>{{ $smsRate->country->country_name }}</td>
									<td>{{ $smsRate->operator->ope_operator_name }}</td>
									@php($ope_numbers = explode(',',$smsRate->operator->ope_number))
									<td>@foreach($ope_numbers as $ope_number){{ $ope_number }}<br>@endforeach</td>
									<td>{{ $smsRate->asr_masking }}</td>
									<td>{{ $smsRate->asr_nonmasking }}</td>
									<td>{{ $smsRate->asr_dynamic }}</td>
									<td>Approved</td>
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
    var table = $('#price_list').DataTable( {
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true
    } );
    } );
    </script>
@endsection

