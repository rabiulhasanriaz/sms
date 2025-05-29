@extends('user.master')
@section('main_content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Today's SMS</h3>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <table id="today_report" class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Campaign Title</th>
                                    <th>Submit time</th>
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
                                @empty(!$todays_campaigns_by_api)
                                    <tr>
                                        <td>{{ $serial++ }}</td>
                                        <td>API</td>

                                        <td></td>
                                        <td class="text-center">{{ $todays_campaigns_by_api->sub_sum }}</td>
                                        <td class="text-center">{{ $todays_campaigns_by_api->sub_sum }}</td>
                                        <td class="text-right">EURO {{ number_format($todays_campaigns_by_api->sum, 5) }}</td>
                                        <td>
                                            <label>
                                                <a href="" data-toggle="modal" data-target="#today_modal" onclick="todays_reports()">
                                                    View
                                                </a>
                                            </label>
                                            |
                                            <label>
                                                <a href="{{ route('user.dynamic-reports.download_dynamic_todays_report', $todays_campaigns_by_api->id) }}" target="_blank" class="btn-none-download"> Download </a>
                                            </label>
                                        </td>
                                    </tr>
                                @endempty
                                @foreach($todays_campaigns as $todays_campaign)
                                    <tr>
                                        <td>{{ $serial++ }}</td>
                                        <td title="{{ $todays_campaign->sdci_campaign_id }}">{{ $todays_campaign->sdci_campaign_title }}</td>
                                        <td>{{ $todays_campaign->sdci_targeted_time->format('H:i a, d-M-Y') }}</td>
                                        <td class="text-center">{{ $todays_campaign->sdci_total_submitted }}</td>
                                        <td class="text-center">{{ $todays_campaign->sdci_total_submitted }}</td>
                                        <td class="text-right">EURO {{ number_format($todays_campaign->sdci_total_cost, 5) }}</td>
                                        <td>
                                            <label>
                                                <a href="#my-modal" onclick="show_today_details('{{$todays_campaign->id}}')"
                                                   role="button" data-toggle="modal"
                                                   class="btn-none-edit CampaignId_one" data-target=".bs-example-modal-lg"> View </a>
                                            </label>
                                            |
                                            <label>
                                                <a href="{{ route('user.dynamic-reports.download_dynamic_todays_report', $todays_campaign->id) }}" target="_blank" class="btn-none-download"> Download </a>
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

                            <div class="modal fade" id="today_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 2000;">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Api Report Detail</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body" id="todayReportDetails">

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                    </div>
                                </div>
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
<link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap-datepicker3.min.css"/>
<link href="{{ asset('assets/datatable/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/datatable/rowReorder.dataTables.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/datatable/responsive.dataTables.min.css') }}" rel="stylesheet" type="text/css">
<style>
    .modal-lg{
        max-width: 80% !important;
    }
</style>
@endsection

@section('custom_script')
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script type="text/javascript">
    // $('#reseller_list').DataTable();
    $(document).ready(function() {
    var table = $('#today_report').DataTable( {
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
    @include('user.ajax.dynamic.ajax.view_todays_report')
    <script type="text/javascript">

        function todays_reports() {

          let url = "{{ route('user.dynamic-reports.today-report-ajax-dynamic') }}";
          var _token=$("#_token").val();
          $.ajax({
            type: "GET",
            url: url,
            data: { _token:_token},
            success: function (result) {
             $("#todayReportDetails").html(result);
             $("#today_modal").modal("show");
            }
          });
        }
      </script>
@endsection
