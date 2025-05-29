<form action="{{ route('user.dynamic-sms.storeGroupContactSmsModem') }}" id="group_contact_sms_send" method="post"
      class="form-horizontal" role="form">
    @csrf
    {{-- <div class="form-group">
        <label for="form-field-select-3"> Sender ID <span style="color: red;">*</span></label>
        <br/>
        <select class="chosen-select form-control" id="form-field-select-3" data-placeholder="Select Sender ID.."
                name="sender_id" required="">
            <option value=""></option>
            @foreach(Auth::user()->senderIds as $senderId)
                <option
                    value="{{$senderId->sender->id}}" @if(old('sender_id')==true) {{  (@old('sender_id')==@$senderId->sender->id)? 'selected':'' }}@else{{ (@$defaultSenderId->sender_id==@$senderId->sender->id)? 'selected':'' }}@endif>{{$senderId->sender->sir_sender_id}}
                </option>
            @endforeach
        </select>
    </div> --}}

    <div class="form-group">
        <label for="form-field-select-3"> Select Contact Group <span style="color: red;">*</span> </label>
        <br/>
        <select class="chosen-select form-control" id="form-field-select-3" data-placeholder="Contact Group"
                name="group_name" required="">
            <option value=""></option>
            @foreach($phonebookCategories as $phonebookCategory)
                <option value="{{$phonebookCategory->id}}">
                    {{$phonebookCategory->name}} ({{\App\Model\PhonebookContact::where('category_id',$phonebookCategory->id)->count()}})
                </option>
            @endforeach
        </select>
    </div>


    <div class="form-group">
        <label>Select SMS Type
            <span class="required"> * </span>
        </label>
        <div class="mt-radio-inline">
            <label class="mt-radio">
                <input type="radio" name="recipientsmsRadios" id="recipientsmsRadiosText" class="ace" value="text"
                       checked="">
                <span class="lbl"></span> Text
            </label> &nbsp;
            <label class="mt-radio">
                <input type="radio" name="recipientsmsRadios" id="recipientsmsRadiosFlash" class="ace" value="flash"
                       disabled>
                <span class="lbl"></span> Flash
            </label> &nbsp;
            <label class="mt-radio">
                <input type="radio" name="recipientsmsRadios" id="recipientsmsRadiosUnicodeFlash" class="ace"
                       value="flashunicode" disabled>
                <span class="lbl"></span> Flash Unicode
            </label> &nbsp;
            <label class="mt-radio">
                <input type="radio" name="recipientsmsRadios" id="recipientsmsRadiosUnicode" class="ace"
                       value="unicode">
                <span class="lbl"></span> Unicode
            </label>
        </div>
    </div>

    <div class="form-group">
        <label>Enter SMS Content
            <span class="required" style="color: red;"> * </span>
            &nbsp;
            <!--<a href="#send_sms_template_modal" role="button" data-toggle="modal" class="">-->
	           <!-- <span class="text-danger"><b>Use Template</b>-->
	           <!-- </span>-->
            <!--</a><a href="http://unicodeconverter.info/avro-type.php?pgn=2.1" class="" style="margin-left: 5px" target="_blank">(বাংলা লিখতে এখানে ক্লিক করুন)</a>-->
        </label>
        <textarea class="count_me form-control" name="message" id="message" required=""
                  style="min-height: 120px;"></textarea>
        <div class="row">
            <div class="col-md-5"><span>CHECK YOUR SMS COUNT</span>
            </div>
            <div class="col-md-7">
                <div style="float: right">
                    <span class="charleft contacts-count" style="color: black;">&nbsp;</span>
                    <span class="parts-count" style="color: black;"></span>
                </div>
            </div>
        </div>
    </div>


    <div class="form-group">
        <label for="target"> Schedule SMS <span style="color: red;">*</span> </label>
        <br>
        <span>
			<input type="radio" class="ace send_now_checkbox" id="send_group_now_checkbox" name="schedule"
                   onchange="hide_show_target_time('#group-contact-content')" value="1" checked="">
			<label class="lbl" for="send_group_now_checkbox">  Send Now  </label>
		</span> &nbsp;&nbsp;
  <!--      <span>-->
		<!--	<input type="radio" class="ace send_later_checkbox" id="send_group_later_checkbox" name="schedule"-->
  <!--                 onchange="hide_show_target_time('#group-contact-content')" value="2">-->
		<!--	<label class="lbl" for="send_group_later_checkbox"> Send Later </label>-->
		<!--</span>-->
    </div>

    <div class="form-group target_time" id="target_time" style="display:none;">
        <label for="target_time"> Target time </label>
        <div class='input-group date' id='datetimepicker2'>
            <input type="text" name="target_time" id="date-timepicker" class="form-control date-timepicker"
                   placeholder="m-d-yyyy">
            <span class="input-group-addon">
				<span class="glyphicon glyphicon-calendar"></span>
			</span>
        </div>
    </div>

    <div class="form-group">
        <label for="c_tittle"> Campaign Title
            {{--            <span style="color: red;"></span>--}}
            @if($errors->has('campaign_title'))
                &nbsp;&nbsp;<span class="text-danger text-lg-left pull-right">{{$errors->first('campaign_title')}}</span>
            @endif
        </label>
        <input type="text" id="c_title" value="" class="form-control input-sm" name="campaign_title" placeholder="Campaign Name">
    </div>

    <div class="clearfix form-group">
        <button onSubmit="submitGropuForm()" id="submit_group_btn" type="submit" class="btn btn-info">Send SMS</button>
        &nbsp; &nbsp; &nbsp;
        <button class="btn btn-danger" type="reset">
            <i class="ace-icon fa fa-undo bigger-110"></i>
            Reset
        </button>
    </div>
</form>

<script>
    function submitGropuForm() {
        $("#submit_group_btn").attr('disabled', true);
        $("#group_contact_sms_send").submit();
        $("#submit_group_btn").attr('disabled', false);
    }
</script>
