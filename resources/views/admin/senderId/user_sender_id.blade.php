@extends('admin.master')

@section('sender_id_menu_class','open')
@section('user_sender_id_menu_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.senderID.index') }}">Sender ID</a>
        </li>
        <li class="active">User Sender ID</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        User Sender ID
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Add
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">

                @if($errors->any())
                    <ul>
                        @foreach($errors->all() as $error)
                            <li class="text-danger">{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
                @if(session()->has('message'))
                    <span class="text-{{ session()->get('type') }}">{{ session()->get('message') }}</span>
                @endif

                <form action="{{ route('admin.senderID.userSenderID.store') }}" method="post" class="form-horizontal"
                      role="form">
                    @csrf
                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 col-lg-offset-2 col-md-offset-2">
                        <!-- PAGE CONTENT BEGINS -->

                        <div class="form-group">
                            <label for="form-field-select-3"> SenderID </label>
                            <br/>
                            <select class="chosen-select form-control" id="form-field-select-3"
                                    data-placeholder="SenderId chose.." name="senderId" required="">
                                <option value=""></option>
                                @foreach($senders as $sender)
                                    <option value="{{ $sender->id }}">{{ $sender->sir_sender_id }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="form-field-select-3"> Company name </label>
                            <br/>
                            <select class="chosen-select form-control" id="form-field-select-3"
                                    data-placeholder="Company name.." name="User_id" required="">
                                <option value=""></option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"> {{ $user->company_name }} -
                                        ( {{ $user->cellphone }} )
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="clearfix form-group">
                            <input type="submit" class="btn btn-info" value="Submit">
                            &nbsp; &nbsp; &nbsp;
                            <button class="btn btn-danger" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                Reset
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </div><!-- /.col -->


        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background: #f8f8f8;">
                <hr>
                <h3>Default SenderID Information</h3>
                <table id="user-senderID-table" class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>SL</th>
                        <th>Company name</th>
                        <th>User Id</th>
                        <th>SenderID</th>
                        <th>Create date</th>
                        <th>Status</th>
                        <th>System</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($serial=1)
                    @foreach($userSenders as $userSender)
                        <tr>
                            <td>{{ $serial++ }}</td>
                            <td>{{ @$userSender->user->company_name }}</td>
                            <td>{{ @$userSender->user->userDetail->name }}</td>
                            <td>{{ $userSender->sender->sir_sender_id }}</td>
                            <td>{{ $userSender->created_at->format('j-M-Y') }}</td>

                            @if($userSender->status==1)
                                <td>Active</td>
                            @elseif($userSender->status==2)
                                <td>Inactive</td>
                            @else
                                <td>Pending</td>
                            @endif

                            <td>
                                <a href="{{ route('admin.senderID.userSenderID.edit', [$userSender->id]) }}"
                                   class="btn-none-edit">Edit</a> |
                                <a href="{{ route('admin.senderID.userSenderID.delete', [$userSender->id]) }}" class="btn-none-delete"
                                   onclick="return confirm('Are you sure you want to delete ?');">Delete</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <!-- PAGE CONTENT ENDS -->
        </div><!-- /.col -->
    </div><!-- /.row -->


@endsection






@section('custom_style')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css"/>
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
    {{-- <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script> --}}
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

    <script type="text/javascript">
        $('.chosen-select').chosen({allow_single_deselect: true});
        // $('#user-senderID-table').DataTable();
        $(document).ready(function() {
        var table = $('#user-senderID-table').DataTable( {
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