
@extends('admin.master')
@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Archived SMS</h3>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <form action="" method="get">
                                <input type="hidden" name="_token" value="" id="_token">
                                <div class="col-xs-3">
                                <input type="text" name="start_date" data-date-format="yyyy-mm-dd" autocomplete="off" value="{{ $start }}" class="form-control" id="start" placeholder="Enter Start Date" >
                                </div>
                                <div class="col-xs-3">
                                <input type="text" name="end_date" data-date-format="yyyy-mm-dd" autocomplete="off" value="{{ $end }}" class="form-control" id="end" placeholder="Enter End Date" >
                                </div>
                                <div class="col-xs-3">
                                <select class="select2"
                                        data-placeholder="Company name.." name="user_id" id="userId" required="">
                                    <option value=""></option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ ($user->id == $userGet) ? 'selected' : '' }}> {{ $user->company_name }}- ( {{ $user->cellphone }}
                                            )
                                        </option>
                                    @endforeach
                                </select>
                              </div>
                                <div class="col-xs-3" style="margin-top: 5px;">
                                <button type="submit" class="btn btn-info btn-sm" name="searchbtn">Search</button>
                                <button type="button" onclick="downloadReport()" class="btn btn-danger btn-sm">Download</button>
                                </div>
                            </form>
                            <br>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                    <table id="view_archived_report" class="table table-striped table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Submitted</th>
                                            <th>Charge</th>
                                            
                                        </tr>
                                        </thead>

                                        <tbody>
                                        @php
                                        ($serial=1)
                                        @endphp
                                        @foreach($userReports as $userReport)
                                            <tr>
                                                <td>{{ $serial++ }}</td>
                                                <td class="text-center">{{ $userReport->total }}</td>
                                                <td class="text-right"><i class="fa fa-euro"></i> {{ number_format($userReport->total_cost, 5) }}</td>
                                                
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>

                                    <!-- ------model view start-->
                                    

                                    

                                </div><!-- /.col -->
                            </div><!-- /.row -->

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('custom_style')
<link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap-datepicker3.min.css"/>
<link href="{{ asset('assets/datatable/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/datatable/rowReorder.dataTables.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/datatable/responsive.dataTables.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        @media(max-width:575px){
            .abcd{
                width: 130px;
            }
        }

        .modal-lg{
            max-width: 80% !important;
        }
    </style>
@endsection
@section('custom_script')
    @include('user.ajax.dynamic.ajax.view_archived_report')
    {{-- <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script> --}}
    <!-- <script src="https://code.jquery.com/jquery-3.3.1.js"></script> -->

    {{-- https://code.jquery.com/jquery-3.5.1.js --}}



    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <script type="text/javascript">
        // $('#reseller_list').DataTable();
        $(document).ready(function() {
            $('.select2').select2();
        });
        $(document).ready(function() {
        var table = $('#view_archived_report').DataTable( {
            responsive: true,
            
        } );
    } );
    </script>
    <script src="{{ asset('assets') }}/js/bootstrap-datepicker.min.js"></script>

    <script>
        // $('#view_archived_report').DataTable();
        $(document).ready(function () {
            $('#start').datepicker({
                autoclose: true,
                todayHighlight: true
            });
            $('#end').datepicker({
                autoclose: true,
                todayHighlight: true
            });
        });
    </script>
    <script type="text/javascript">

        function api_reports(start_date, end_date) {

          let url = "{{ route('user.dynamic-reports.api-report-ajax-dynamic') }}";
          var _token=$("#_token").val();
          $.ajax({
            type: "GET",
            url: url,
            data: { _token:_token, start_date:start_date, end_date:end_date},
            success: function (result) {
             $("#apiReportDetails").html(result);
             $("#api_modal").modal("show");
            }
          });
        }

        function downloadReport() {
            let startDate = $("#start").val();
            let endDate = $("#end").val();
            let user = $("#userId").val();
            let route = "{!! route('admin.reports.user-reports-pdf') !!}?start_date="+startDate+"&end_date="+endDate+"&user="+user;

            window.open(route, '_blank');
        }
      </script>
@endsection
