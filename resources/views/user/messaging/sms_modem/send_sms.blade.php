@php
($permission = explode(',',Auth::user()->permission))
@endphp
@if (in_array(2,$permission))
    @php
    ($flexi_permission = true)
    @endphp
@else
    @php
    ($flexi_permission = false)
    @endphp
@endif
@extends('user.master')
@section('main_content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Send SMS</h3>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        @include('user.partials.session_messages')
                        @include('user.partials.all_error_messages')
                        <div class="ajax_error" style="display: none">
                            <div class="alert alert-danger" id="report-alert">
                                <button type="button" class="close"><span style="font-size: 20px;">x</span>
                                </button>
                                <span class="error_messages"></span>
                            </div>
                        </div>

                        <div class="ajax_success" style="display: none">
                            <div class="alert alert-success" id="report-alert">
                                <button type="button" class="close"><span style="font-size: 20px;">x</span>
                                </button>
                                <span class="success_messages"></span>
                            </div>
                        </div>
                        <div class="x_title">
                                <ul class="nav nav-tabs bar_tabs" id="myTab" role="tablist">
                                  <li class="nav-item">
                                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#send-sms-content" role="tab" aria-controls="home" aria-selected="true">Send SMS</a>
                                  </li>
                                  <li class="nav-item">
                                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#upload-file-content" role="tab" aria-controls="profile" aria-selected="false">Upload File</a>
                                  </li>
                                  <li class="nav-item">
                                    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#group-contact-content" role="tab" aria-controls="contact" aria-selected="false">Group Contact</a>
                                  </li>
                                </ul>
                                <div class="tab-content">
                                  <div class="tab-pane active" id="send-sms-content" role="tabpanel" aria-labelledby="home-tab">
                                    @include('user.messaging.sms_modem.smspart.send_sms_content')
                                  </div>
                                  <div class="tab-pane" id="upload-file-content" role="tabpanel" aria-labelledby="profile-tab">
                                    @include('user.messaging.sms_modem.smspart.upload_file_content')
                                  </div>
                                  <div class="tab-pane" id="group-contact-content" role="tabpanel" aria-labelledby="contact-tab">
                                    @include('user.messaging.sms_modem.smspart.group_contact_content')
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
    <link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css"/>
    <link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap-datetimepicker.min.css"/>

    <style>
        .chosen-container {
            width: 100% !important;
        }

        .tab-content {
            border: none !important;
        }
        .fade {
            opacity: 1 !important;
        }
    </style>
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.textareaCounter.plugin.js?v=1.1.1"></script>
    <script src="{{ asset('assets') }}/js/text-area-counter.js"></script>
    <script src="{{ asset('assets') }}/js/moment.min.js"></script>
    <script src="{{ asset('assets') }}/js/bootstrap-datetimepicker.min.js"></script>
    <script src="{{ asset('assets') }}/js/ajax_send_sms.js?v=1.0.0"></script>

    <!--suppress LocalVariableNamingConventionJS -->
    <script type="text/javascript">
    $("#file_preview_content").hide();
        $('.chosen-select').chosen({allow_single_deselect: true});
        $(document).ready(function () {
            count_textarea('#send-sms-content');
            count_textarea('#upload-file-content');
            count_textarea('#group-contact-content');
            count_textarea('#dynamic-sms-content');
            count_textarea('#employee-sms-content');

            $('.date-timepicker').datetimepicker();
        });

        function hide_show_target_time(check_id) {
            if ($(check_id + ' .send_now_checkbox').is(":checked")) {
                $(check_id + " .target_time").hide();
            } else if ($(check_id + ' .send_later_checkbox').is(":checked")) {
                $(check_id + " .target_time").show();
            }
        }

        function get_sms_template(id, parent_id) {
            var sms_content = $('#get_sms_template_' + id).html();
            $('#' + parent_id + ' #message').val(sms_content);
        }

        $('#id-input-file-1 , #id-input-file-2').ace_file_input({
            no_file: 'No File ...',
            btn_choose: 'Choose',
            btn_change: 'Change',
            droppable: false,
            onchange: null,
            thumbnail: false //| true | large
            //whitelist:'gif|png|jpg|jpeg'
            //blacklist:'exe|php'
            //onchange:''

        });



        $('#click1').click(function () {
            valid_dynamic_sms_file('dynamic_sms_send', '{{ route('user.sms.checkDynamicFile') }}');
        });
        $(".close").click(function () {
            $(".ajax_error").hide();
            $(".ajax_success").hide();

        });
        $("#id-input-file-2a").change(function () {
            $("#dynamic_number_column").empty();
            $(".dynamic_msg").hide();
        });

        /*getSmsField*/
        function getSmsField(string){
            let retVal = '[#'+ string +'#]';
            let preVal = $("#dynamic-sms-content .count_me").val();
            let curVal = preVal+retVal;
            $("#dynamic-sms-content .count_me").val(curVal);

        }

        /*checkUploadedFile*/
        function checkUploadedFile(){
            if(!$('#dynamic_number_column').is(':visible'))
            {
                alert('please upload file first');
            }
        }

        $('#upload_file_preview').click(function () {
            // alert('hello');
            check_upload_file('upload_file_send_sms', '{{ route('user.dynamic-sms.checkUploadFileModem') }}');
        });





        /*sms send form submit*/
        sms_send_form_submit('upload_file_send_sms', '{{ route('user.dynamic-sms.storeUploadFileSmsModem') }}');
        sms_send_form_submit('single_sms_send', '{{ route('user.dynamic-sms.storeSingleSmsModem') }}');
        sms_send_form_submit('group_contact_sms_send', '{{ route('user.dynamic-sms.storeGroupContactSmsModem') }}');
        // sms_send_form_submit('dynamic_sms_send', '{{ route('user.sms.storeDynamicSms') }}');
        // sms_send_form_submit('employee_group_contact_sms_send', '{{ route('user.sms.storeEmployeeGroupContactSms') }}');
    </script>
@endsection
