@extends('user.master')
@section('main_content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left w-100">
                @include('user.partials.session_messages')
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <form action="{{ route('user.changeApi') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 api-urltitle">
                                <h3>Text SMS API</h3>
                                <span>Your API Key : <b> {{ Auth::user()->userDetail->api_key }} </b></span> &nbsp;&nbsp;<button class="btn btn-sm btn-default btn-danger" name="apikey_create">Change API Key</button>
                            </div><br>
                            <div class="space-10"></div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 api-urltitle-contain ml-2 mt-2">
                    <strong>API URL (GET & POST) : </strong>
                    <span>https://{{ $domain_url }}/api/v2/send?api_key=(API KEY)&mobileno=(contact)&msg=(Message)</span>
               </div>
               <br>
            </div>

            <div class="clearfix"></div>
            <br>
            <div class="row">
                <div class="col-md-12 ">
                    <div class="x_panel">
                        <div class="x_title">

                            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                <h4>Credit Balance API</h4>
                                <table class="table table-responsive table-bordered" id="view_archived_report">
                                    <thead>
                                    <tr>
                                        <th style="width: 120px;">Parameter Name</th>
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
                                        <strong>API URL :</strong> https://{{  $domain_url }}/api/v2/balance?api_key=(API KEY) <br>
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
                                        <td>10000</td>
                                        <td>Message Sent successfully</td>
                                    </tr>
                                    <tr>
                                        <td>10010</td>
                                        <td>missing api key</td>
                                    </tr>
                                    <tr>
                                        <td>10020</td>
                                        <td>missing contact number</td>
                                    </tr>
                                   
                                    <tr>
                                        <td>10040</td>
                                        <td>Invalid api key</td>
                                    </tr>
                                    <tr>
                                        <td>10050</td>
                                        <td>Your account was suspended</td>
                                    </tr>
                                    <tr>
                                        <td>10060</td>
                                        <td>Your account was expired</td>
                                    </tr>
                                    <tr>
                                        <td>10070</td>
                                        <td>Only a user can send sms</td>
                                    </tr>
                                    
                                   
                                    <tr>
                                        <td>10110</td>
                                        <td>All numbers are invalid</td>
                                    </tr>
                                    <tr>
                                        <td>10120</td>
                                        <td>insufficient balance</td>
                                    </tr>
                                    <tr>
                                        <td>10130</td>
                                        <td>reseller insufficient balance</td>
                                    </tr>
                                    <tr>
                                        <td>10170</td>
                                        <td>You are not a user</td>
                                    </tr>


                                </table>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
<!--<script src="https://code.jquery.com/jquery-3.3.1.js"></script>-->
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
