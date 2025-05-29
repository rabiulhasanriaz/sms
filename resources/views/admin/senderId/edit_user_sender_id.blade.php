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

                <form action="{{ route('admin.senderID.userSenderID.update', [$preIds->id]) }}" method="post" class="form-horizontal" role="form">
                    @csrf
                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 col-lg-offset-2 col-md-offset-2">
                        <!-- PAGE CONTENT BEGINS -->

                        <div class="form-group">
                            <label for="form-field-select-3"> SenderID </label>
                            <br />
                            <select class="chosen-select form-control" id="form-field-select-3" data-placeholder="SenderId chose.." name="sender_id" required="">
                                <option value="">  </option>
                                @foreach($senders as $sender)
                                    <option value="{{ $sender->id }}" @if($sender->id==$preIds->sender_id) selected @endif>{{ $sender->sir_sender_id }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="form-field-select-3"> Company name </label>
                            <br />
                            <select class="chosen-select form-control" id="form-field-select-3" data-placeholder="Company name.." name="user_id" required="">
                                <option value="">  </option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @if($user->id==$preIds->user_id) selected @endif> {{ $user->company_name }} -
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


    </div><!-- /.row -->


@endsection






@section('custom_style')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css" />
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
    <script type="text/javascript">
        $('.chosen-select').chosen({allow_single_deselect:true});
    </script>
@endsection