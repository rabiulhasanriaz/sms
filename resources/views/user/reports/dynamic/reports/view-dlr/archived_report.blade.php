
@extends('user.master')
@section('main_content')
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
                                <input type="text" name="start_date" data-date-format="yyyy-mm-dd" autocomplete="off" value="{{ $start_date }}" class="form-control" id="start" placeholder="Enter Start Date" >
                                </div>
                                <div class="col-xs-3">
                                <input type="text" name="end_date" data-date-format="yyyy-mm-dd" autocomplete="off" value="{{ $end_date }}" class="form-control" id="end" placeholder="Enter End Date" >
                                </div>
                                <div class="col-xs-3">
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
                                            <th>Campaign Title</th>
                                            <th class="hidden-600">Submit time</th>
                                            <th>Submitted</th>
                                            <th>Total sent</th>
                                            <th>Charge</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        @php
                                        ($serial=1)
                                        @endphp
                                        @empty(!$archived_campaigns_by_api)

                                        <tr>
                                            <td>{{ $serial++ }}</td>
                                            <td> API </td>
                                            <td></td>
                                            <td class="text-center">{{ $archived_campaigns_by_api->sub_sum }}</td>
                                            <td class="text-center">{{ $archived_campaigns_by_api->sub_sum }}</td>
                                            <td class="text-right">EURO {{ number_format($archived_campaigns_by_api->sum, 5) }}</td>
                                            <td>
                                                <label>
                                                    <a href="" data-toggle="modal" data-target="#api_modal" onclick="api_reports('{{ $start_date }}', '{{ $end_date }}')">
                                                        View
                                                    </a>
                                                </label>
                                                |
                                                <label>

                                                    <label>
                                                        <a href="" target="_blank" class="btn-none-download"> Download </a>
                                                    </label>
                                                </label>
                                            </td>
                                        </tr>
                                        @endempty
                                        @foreach($archived_campaigns as $archived_campaign)
                                            <tr>
                                                <td>{{ $serial++ }}</td>
                                                <td title="{{ $archived_campaign->sdci_campaign_id }}">{{ $archived_campaign->sdci_campaign_title }}</td>
                                                <td>
                                                    @if(!empty($archived_campaign->sdci_targeted_time))
                                                        {{ $archived_campaign->sdci_targeted_time->format('H:i a, d-M-Y') }}
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $archived_campaign->sdci_total_submitted }}</td>
                                                <td class="text-center">{{ $archived_campaign->sdci_total_submitted }}</td>
                                                <td class="text-right">EURO {{ number_format($archived_campaign->sdci_total_cost, 5) }}
                                                    @foreach ($cost as $sms)
                                                    @if ($archived_campaign->id == $sms->campaign_id)
                                                    ({{ $sms->sd_sms_cost }})
                                                    @endif
                                                    @endforeach

                                                </td>
                                                <td>
                                                    <label>
                                                        <a href="#" onclick="show_archived_details('{{$archived_campaign->id}}')"
                                                            role="button" data-toggle="modal"
                                                            class="btn-none-edit CampaignId_one" data-target=".bs-example-modal-lg"> View </a>
                                                    </label>
                                                    |
                                                    <label>

                                                        <label>
                                                            <a href="{{ route('user.dynamic-reports.download_dynamic_archived_report', $archived_campaign->id) }}" target="_blank" class="btn-none-download"> Download </a>
                                                        </label>
                                                    </label>
                                                </td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>

                                    <!-- ------model view start-->
                                    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                            <h4 class="modal-title" id="myModalLabel">Today Report Details</h4>
                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                            </button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="SmsInformation"></div>
                                            </div>
                                            <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                        </div>
                                    </div>


                                    <div class="modal fade" id="api_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 2000;">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Api Report Detail</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body" id="apiReportDetails">

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                            </div>
                                        </div>
                                        </div>

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
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#reseller_list').DataTable();
        $(document).ready(function() {
        var table = $('#view_archived_report').DataTable( {
            responsive: true,
            columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 2 },
                    { responsivePriority: 3, targets: 4 },
                    { responsivePriority: 4, targets: 1 },
                    { responsivePriority: 5, targets: 3 },
            ]
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
            let route = "{!! route('user.dynamic-reports.api-reports-download') !!}?start_date="+startDate+"&end_date="+endDate;
            window.open(route, '_blank');
        }
      </script>
@endsection
