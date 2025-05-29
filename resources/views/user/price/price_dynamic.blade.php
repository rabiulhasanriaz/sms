@extends('user.master')
@section('main_content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3> Price List</h3>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <table class="table table-striped table-bordered table-hover" id="price_list">
                                <thead>
                                    <tr>
                                        <th class="ijk">SL</th>
                                        <th>Country</th>
                                        <th class="oper">Operator</th>
                                        <th class="abcd">Dynamic </th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>
                                @php
                                ($serial=1)
                                @endphp
                                @foreach($smsRates as $smsRate)
                                    <tr>
                                        <td>{{ $serial++ }}</td>
                                        <td>{{ $smsRate->country->country_name }}</td>
                                        <td>{{ $smsRate->operator->ope_operator_name }}</td>
                                        <td>{{ $smsRate->asr_dynamic }}</td>
                                        <td>Approved</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
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
        @media(max-width:575px){
            .abcd{
                width: 130px;
            }
        }

    </style>
@endsection

@section('custom_script')
    {{-- <script src="https://code.jquery.com/jquery-3.3.1.js"></script> --}}
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#reseller_list').DataTable();
        $(document).ready(function() {
        var table = $('#price_list').DataTable( {
            responsive: true,
            columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 4 },
                    { responsivePriority: 4, targets: 2 },
                    { responsivePriority: 5, targets: 3 },
            ]
        } );
    } );
    </script>
@endsection

