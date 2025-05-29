@extends('admin.master')

@section('virtual_number_menu_class','open')
@section('virtual_number_list_menu_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.senderID.index') }}">Sender ID</a>
        </li>
        <li class="active">Virtual Number</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Virtual Number
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            List
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>

    @include('admin.partials.session_messages')

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding bg-container">
                <hr>
                <h3>Virtual number view</h3>
                <table class="table table-bordered table-responsive" id="virtual-number-table">
                    <thead>
                    <tr>
                        <th>SL</th>
                        <th>Operator Name</th>
                        <th>Date</th>
                        <th>Virtual Number Name</th>
                        <th>Load Amount</th>
                        <th>User ID</th>
                        <th>Virtual Number</th>
                        <th>System</th>
                    </tr>
                    </thead>

                    <tbody>

                    @php($serial=1)
                    @foreach($virtualNumbers as $virtualNumber)
                        <tr>
                            <td>{{ $serial++ }}</td>
                            <td>{{ $operatorName = $virtualNumber->operator->ope_operator_name }}</td>
                            <td>{{ $virtualNumber->created_at->format('j M, Y') }}</td>
                            <td>{{ $virtualNumber->sivn_name }}</td>
                            <td>{{ $virtualNumber->sivn_load_amount }}</td>
                            <td>{{ $virtualNumber->sivn_api_user_name }}</td>
                            <td>{{ $virtualNumber->sivn_number }}-><span style="color: red;background-color: #ddd;border: 2px solid green;padding: 0px 8px;font-weight: bold;">{{ $virtualNumber->$operatorName->count() }}</span></td>


                            <td>
                                <div class="hidden-sm hidden-xs action-buttons">

                                    <a class="red" onclick="return confirm('Are you sure to delete this?')" href="{{ route('admin.virtualNumber.delete', $virtualNumber->id) }}" title="Delete">
                                        <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                    </a>

                                    <a class="green" href="{{ route('admin.virtualNumber.edit', $virtualNumber->id) }}" title="Edit">
                                        <i class="ace-icon fa fa-pencil bigger-130"></i>
                                    </a>

                                    <a class="green" href="{{ route('admin.virtualNumber.balance_query', $virtualNumber->id) }}" title="Balance" target="_blank">
                                        <i class="ace-icon glyphicon glyphicon-usd"></i>
                                    </a>
                                </div>

                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div><!-- end bg-cntainer-->

            <!-- PAGE CONTENT ENDS -->
        </div><!-- /.col -->
    </div><!-- /.row -->


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
	<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#user-list-table').DataTable();

        $(document).ready(function() {
        var table = $('#virtual-number-table').DataTable( {
            responsive: true,
            columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 5 },
                    { responsivePriority: 4, targets: 2 },
                    { responsivePriority: 5, targets: 3 },
                    { responsivePriority: 6, targets: 4 },
                    { responsivePriority: 7, targets: 6 },
            ]
        } );
    } );
</script>
@endsection


{{-- @section('custom_script')
    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
        $('#virtual-number-table').DataTable();
    </script>

@endsection --}}
