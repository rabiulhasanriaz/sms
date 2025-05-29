@extends('admin.master')

@section('sender_id_menu_class','open')
@section('delivery_sender_id_menu_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li class="active">Delivery Sender ID</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Delivery Sender ID
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Check
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>




    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 col-lg-offset-2 col-md-offset-2 containerBg">

                <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 col-lg-offset-1 col-md-offset-1">
                    <!-- PAGE CONTENT BEGINS -->
                    <div class="from-group">
                        <h3 class="text-center text-primary">Sender ID : {{$senderIdDetails->sir_sender_id}}</h3><hr>
                    </div>


                    {{--<div class="ajax_error" style="display: none">
                        <div class="alert alert-danger" id="report-alert">
                            <button type="button" class="close" data-dismiss="alert"><span style="font-size: 20px;">x</span>
                            </button>
                            <span class="error_messages"></span>
                        </div>
                    </div>
                    <div class="ajax_success" style="display: none">
                        <div class="alert alert-success" id="report-alert">
                            <button type="button" class="close" data-dismiss="alert"><span style="font-size: 20px;">x</span>
                            </button>
                            <span class="success_messages"></span>
                        </div>
                    </div>--}}


                    <form action="{{ route('admin.senderID.checkDeliverySenderID',$senderIdDetails->id) }}" method="post" id="updateDeliverySenderId" class="form-horizontal" role="form">
                        <div class="form-group wellc well-lgc" style="margin-bottom: 0px;">
                            <label for="form-field-select-3"> <b>Robi :</b></label>
                            <div class="form-group">

                                <label>
                                    <input type="radio" class="ace change_sender_btn" name="status_robi" value="2" required="" {{ ($senderIdDetails->sir_robi_confirmation==2) ? 'checked':'' }}>
                                    <span class="lbl"> Inactive</span>
                                </label>
                                <label>
                                    <input type="radio" class="ace change_sender_btn" name="status_robi" value="0" required="" {{ ($senderIdDetails->sir_robi_confirmation==0) ? 'checked':'' }}>
                                    <span class="lbl"> Processing </span>
                                </label>
                                <label>
                                    <input type="radio" class="ace change_sender_btn" name="status_robi" value="1" required="" {{ ($senderIdDetails->sir_robi_confirmation==1) ? 'checked':'' }}>
                                    <span class="lbl"> Active </span>
                                    <span><input type="text" name="mobile_number" id="robi_number" placeholder="88018-0000000" maxlength="13" required=""></span>
                                    <span><a href="javascript:;" id="message-send-Robi" class="btn btn-primary btn-sm">Check</a></span>
                                </label>
                                <button type="button" class="btn btn-primary btn-sm">Raw Url</button>
                            </div>
                        </div>

                        <div class="form-group wellc well-lgc" style="margin-bottom: 0px;">
                            <label for="form-field-select-3"> <b>Teletalk :</b></label>
                            <div class="form-group">
                                <label>
                                    <input type="radio" class="ace change_sender_btn" name="status_teletalk" value="2" required="" {{ ($senderIdDetails->sir_teletalk_confirmation==2) ? 'checked':'' }}>
                                    <span class="lbl"> Inactive</span>
                                </label>
                                <label>
                                    <input type="radio" class="ace change_sender_btn" name="status_teletalk" value="0" required="" {{ ($senderIdDetails->sir_teletalk_confirmation==0) ? 'checked':'' }}>
                                    <span class="lbl"> Processing </span>
                                </label>
                                <label>
                                    <input type="radio" class="ace change_sender_btn" name="status_teletalk" value="1" required="" {{ ($senderIdDetails->sir_teletalk_confirmation==1) ? 'checked':'' }}>
                                    <span class="lbl"> Active </span>
                                    <span><input type="text" name="mobile_number" id="teletalk_number" placeholder="88015-0000000" maxlength="13" required=""></span>
                                    <span><a href="javascript:;" id="message-send-Teletalk" class="btn btn-primary btn-sm">Check</a></span>
                                </label>
                                <button type="button" class="btn btn-primary btn-sm">Raw Url</button>
                            </div>
                        </div>

                        <div class="form-group well well-md" style="margin-bottom: 0px;">
                            <label for="form-field-select-3"> <b>Grameen :</b></label>
                            <div class="form-group">
                                <label>
                                    <input type="radio" class="ace change_sender_btn" name="status_grameen" value="2" required="" {{ ($senderIdDetails->sir_gp_confirmation==2) ? 'checked':'' }}>
                                    <span class="lbl"> Inactive</span>
                                </label>
                                <label>
                                    <input type="radio" class="ace change_sender_btn" name="status_grameen" value="0" required="" {{ ($senderIdDetails->sir_gp_confirmation==0) ? 'checked':'' }}>
                                    <span class="lbl"> Processing </span>
                                </label>
                                <label>
                                    <input type="radio" class="ace change_sender_btn" name="status_grameen" value="1" required="" {{ ($senderIdDetails->sir_gp_confirmation==1) ? 'checked':'' }}>
                                    <span class="lbl"> Active </span>
                                    <span><input type="text" name="mobile_number" id="grameen_number" placeholder="017-0000000" maxlength="13" required=""></span>
                                    <span><a href="javascript:;" id="message-send-Grameen"  class="btn btn-primary btn-sm">Check</a></span>
                                </label>
                                <button type="button" class="btn btn-primary btn-sm">Raw Url</button>
                            </div>
                        </div>

                        <div class="form-group wellc well-lgc" style="margin-bottom: 0px;margin-top: 0px;">
                            <label for="form-field-select-3">  <b>Airtel :</b></label>
                            <div class="form-group">
                                <label>
                                    <input type="radio" class="ace change_sender_btn" name="status_Airtel" value="2" required="" {{ ($senderIdDetails->sir_airtel_confirmation==2) ? 'checked':'' }}>
                                    <span class="lbl"> Inactive</span>
                                </label>
                                <label>
                                    <input type="radio" class="ace change_sender_btn" name="status_Airtel" value="0" required="" {{ ($senderIdDetails->sir_airtel_confirmation==0) ? 'checked':'' }}>
                                    <span class="lbl"> Processing </span>
                                </label>
                                <label>
                                    <input type="radio" class="ace change_sender_btn" name="status_Airtel" value="1" required="" {{ ($senderIdDetails->sir_airtel_confirmation==1) ? 'checked':'' }}>
                                    <span class="lbl"> Active </span>
                                    <span><input type="text" name="airtel_number" placeholder="88016-0000000" maxlength="13" required=""></span>
                                    <span><a href="javascript:;" name="message-send-Airtel"  class="btn btn-primary btn-sm">Check</a></span>
                                </label>
                                <button type="button" class="btn btn-primary btn-sm">Raw Url</button>
                            </div>
                        </div>

                        <div class="form-group wellc well-lgc" style="margin-bottom: 0px;">
                            <label for="form-field-select-3"> <b>BanglaLink :</b></label>
                            <div class="form-group">
                                <label>
                                    <input type="radio" class="ace change_sender_btn" name="status_BanglaLink" value="2" required="" {{ ($senderIdDetails->sir_banglalink_confirmation==2) ? 'checked':'' }}>
                                    <span class="lbl"> Inactive</span>
                                </label>
                                <label>
                                    <input type="radio" class="ace change_sender_btn" name="status_BanglaLink" value="0" required="" {{ ($senderIdDetails->sir_banglalink_confirmation==0) ? 'checked':'' }}>
                                    <span class="lbl"> Processing </span>
                                </label>
                                <label>
                                    <input type="radio" class="ace change_sender_btn" name="status_BanglaLink" value="1" required="" {{ ($senderIdDetails->sir_banglalink_confirmation==1) ? 'checked':'' }}>
                                    <span class="lbl"> Active </span>
                                    <span><input type="text" name="mobile_number" id="banglalink_number" placeholder="88019-0000000" maxlength="13" required=""></span>
                                    <span><a href="javascript:;" id="message-send-BanglaLink" class="btn btn-primary btn-sm">Check</a></span>
                                </label>
                                <button type="button" class="btn btn-primary btn-sm">Raw Url</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- /.col -->
    </div><!-- /.row -->


@endsection





@section('custom_script')
    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
        $('#delivery-sender-id-list-table').DataTable();

        changeDeliveryStatus('updateDeliverySenderId', '{{route('admin.senderID.checkDeliverySenderID',$senderIdDetails->id)}}');

        $('.change_sender_btn').change(function () {
            $('#updateDeliverySenderId').submit();
        });

        function checkPanel(btn_id, number_id, operator) {
            $("#"+btn_id).click(function () {
                let number = $('#'+number_id).val();
                let main_domain = "{!! config('app.url') !!}";
                let total_route = main_domain + "/root/senderID/delivery-senderID/check/{{$senderIdDetails->id}}/"+ operator +"/" + number;
                window.open(total_route, '_blank');
            });
        }
        //robi check
        checkPanel('message-send-Robi', 'robi_number', 'robi');
        checkPanel('message-send-Teletalk', 'teletalk_number', 'teletalk');
        checkPanel('message-send-Grameen', 'grameen_number', 'grameen');
        checkPanel('message-send-Airtel', 'airtel_number', 'airtel');
        checkPanel('message-send-BanglaLink', 'banglalink_number', 'banglalink');

        /*change delivery status*/
        function changeDeliveryStatus(form_id, form_route) {

            $(function () {
                $('#' + form_id).on('submit', function (e) {

                    var formData = new FormData($('#' + form_id)[0]);
                    e.preventDefault();
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: 'post',
                        url: form_route,
                        data: formData,
                        dataType: 'JSON',
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            $("#loading").show();
                        },
                        success: function (data) {
                            if (data.errors) {
                                $('.error_messages').empty();
                                $('.success_messages').empty();
                                $('.ajax_success').hide();
                                $('.ajax_error').show();
                                $.each(data.errors, function (key, value) {
                                    $('.error_messages').append('<p>' + value + '</p>');
                                });
                            }
                            if (data.success) {
                                $("#report-alert").hide();
                                $('.ajax_error').hide();
                                $('.error_messages').empty();
                                $('.success_messages').empty();
                                $('.ajax_success').show();
                                $('.success_messages').append('<p>' + data.success + '</p>');
                            }
                            if (data.error) {
                                $('.success_messages').empty();
                                $('.error_messages').empty();
                                $('.ajax_success').hide();
                                $('.ajax_error').show();
                                $('.error_messages').append('<p>' + data.error + '</p>');
                            }
                            // $("html, body").animate({scrollTop: 0}, 500);
                        },
                        complete: function () {
                            $("#loading").hide();
                        },
                    });

                });

            });
        }
    </script>
@endsection
