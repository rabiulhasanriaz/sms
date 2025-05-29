@extends('user.master')

@section('api_menu','open')
@section('dynamic_api_progress_menu_class','active')
@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('user.index') }}">Dashboard</a>
	</li>
	<li class="active">Application Programming Interface</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	Api
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		Api Details
	</small>
</h1>
@endsection


@section('main_content')

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

		@include('user.partials.session_messages')

		<form action="{{ route('user.changeApi') }}" method="POST" enctype="multipart/form-data">
			@csrf
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 api-urltitle">
				<h3>Text SMS API</h3>
				<span>Your API Key : <b> {{ Auth::user()->userDetail->api_key }} </b></span> &nbsp;&nbsp;<button class="btn btn-sm btn-default btn-danger" name="apikey_create">Change API Key</button>
			</div><br>
			<div class="space-10"></div>
		</form>

		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 api-urltitle-contain">
			 <strong>API URL (GET & POST) : </strong>
			 <span>http://{{ $domain_url }}/api/v2/send?api_key=(API KEY)&mobileno=(contact)&msg=(Message)</span>
		</div>
	</div>

	<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
		<h4>Credit Balance API</h4>
		<table class="table table-responsive table-bordered" id="view_archived_report">
			<thead>
			<tr>
				<th>Parameter Name</th>
				<th>Meaning/Value</th>
				<th>Description</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td>api_key</td>
				<td>API Key</td>
				<td class="abcd">Your API Key ({{ Auth::user()->userDetail->api_key }})</td>
			</tr>
			<tr>
				<td>mobileno</td>
				<td>mobile numbe</td>
				<td>Exp: 88017XXXXXXXX,88018XXXXXXXX,88019XXXXXXXX...</td>
			</tr>
			<tr>
				<td>msg</td>
				<td>SMS body</td>
				<td class="text-danger">N.B: Please use url encoding to send some special characters like &, $, @ etc</td>
			</tr>
			</tbody>
		</table>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h3>Credit Balance API</h3>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 api-urltitle-bottom">
				<br>
				<strong>API URL :</strong>  http://{{  $domain_url }}/api/v2/balance?api_key=(API KEY) <br>
				<strong>API URL :</strong>  Your API Key (<b> {{ Auth::user()->userDetail->api_key }} </b>)
			</div>
		</div><!-- /.col -->
	</div>

	<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
		<h4>Error Information</h4>
		<table class="table table-responsive table-bordered">

			<tr>
				<th>API Response Code</th>
				<th>Description</th>
			</tr>

			<tr>
				<td>445000</td>
				<td>Message Sent successfully</td>
			</tr>
			<tr>
				<td>445010</td>
				<td>missing api key</td>
			</tr>
			<tr>
				<td>445020</td>
				<td>missing contact number</td>
			</tr>
			<tr>
				<td>445030</td>
				<td>missing sender id</td>
			</tr>
			<tr>
				<td>445040</td>
				<td>Invalid api key</td>
			</tr>
			<tr>
				<td>445050</td>
				<td>Your account was suspended</td>
			</tr>
			<tr>
				<td>445060</td>
				<td>Your account was expired</td>
			</tr>
			<tr>
				<td>445070</td>
				<td>Only a user can send sms</td>
			</tr>
			<tr>
				<td>445080</td>
				<td>Invalid sender id</td>
			</tr>
			<tr>
				<td>445090</td>
				<td>You have no access to this sender id</td>
			</tr>
			<tr>
				<td>445110</td>
				<td>All numbers are invalid</td>
			</tr>
			<tr>
				<td>445120</td>
				<td>insufficient balance</td>
			</tr>
			<tr>
				<td>445130</td>
				<td>reseller insufficient balance</td>
			</tr>
			<tr>
				<td>445170</td>
				<td>You are not a user</td>
			</tr>


		</table>
	</div>


</div><!-- /.row -->

@endsection
@section('custom_style')
<link href="{{ asset('assets/datatable/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/datatable/rowReorder.dataTables.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/datatable/responsive.dataTables.min.css') }}" rel="stylesheet" type="text/css">
	<style>
		#view_archived_report_length{
			display: none;
		}
		#view_archived_report_filter{
			display: none;
		}
		#view_archived_report_info{
			display: none;
		}
		#view_archived_report_paginate{
			display: none;
		}
	</style>
@endsection
@section('custom_script')
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script type="text/javascript">
	// $('#reseller_list').DataTable();
	$(document).ready(function() {
	var table = $('#view_archived_report').DataTable( {
		responsive: true,
		
	} );
} );
</script>
@endsection



