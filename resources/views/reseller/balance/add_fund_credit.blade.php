@extends('reseller.master')

@section('add_fund_credit_menu_class','current-page')
@section('acc_details_menu_class','open')

@section('content')
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        @include('reseller.partials.session_messages')
        <div class="title_left">
          <h3>Employee </h3>
        </div>
       
        <div class="title_right">
          <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Search for...">
              <span class="input-group-btn">
                <button class="btn btn-secondary" type="button">Go!</button>
              </span>
            </div>
          </div>
        </div>
      </div>
    
      <div class="clearfix"></div>
    
      <div class="row">
        <div class="col-md-12 col-sm-12 ">
          <div class="x_panel">
            <div class="x_title">
              <h2>Employee  <small>List </small>&nbsp;<span style="color: green;">Your Payment Balance is: {{ number_format( $paymentable_balance, 2) }}</span></h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                      <a class="dropdown-item" href="#">Settings 1</a>
                      <a class="dropdown-item" href="#">Settings 2</a>
                    </div>
                </li>
                <li><a class="close-link"><i class="fa fa-close"></i></a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
               <div class="col-sm-6 offset-sm-3">
                <form action="{{ route('reseller.balance.credit.store') }}" method="post" class="form-horizontal"
                role="form">
              @csrf
              <div class="form-group">
                  <label for="form-field-select-3"> Company name </label>
                  <br/>

                  <select id="employeeName" class="select2 form-control" name="user_id"
                         required="" onchange="user_balance(this.value)">
                         <option value="" hidden>Select an User</option>
                         @foreach($resellers as $reseller)
                             <option value="{{ $reseller->id }}"> {{ $reseller->company_name }}- ( {{ $reseller->cellphone }}
                                 )
                             </option>
                         @endforeach
                  </select>
<!-- 
                  <select class="chosen-select form-control col-xs-10 col-sm-5" id="form-field-select-3"
                          data-placeholder="Company name.." name="user_id" required=""
                          onchange="user_balance(this.value)">
                      <option value="" hidden>Select an Employee</option>
                      
                  </select> -->
              </div>

              <div class="form-group">
                  <label for="credit">Credit amount <span class="text-success"
                                                          id="CustomerBalance"></span></label>
                  <input type="text" name="credit_ammount" id="credit" value=""
                         class="form-control input-mask-numberTk" onkeyup="submit_button_control(this)"
                         placeholder="00.00" maxlength="10" required>
              </div>

              <div class="form-group">
                  <label for="payReference"> Payment reference :</label>
                  <input type="text" name="payment_reference" id="payReference" value="" class="form-control"
                         placeholder="......" maxlength="32" required>
              </div>

              <div class="form-group">
                  <label for="payMethod"> Payment method :</label>
                  <select class="form-control" name="payment_method" required=""
                          onchange="show_terget_time(this.value)">
                      <option value="">Select method</option>
                      <option value="1">Cash</option>
                      <option value="2">Bank deposit</option>
                      <option value="3">Check</option>
                  </select>
              </div>

              <div class="form-group" id="target_time" style="display: none;">
                  <label for="target"> Target time </label>
                  <div class='input-group date' id='datetimepicker2'>
                      <input type="text" name="target_time" id="datetimepicker1" type="text" class="form-control"
                             placeholder="d-m-yyyyy">
                      <span class="input-group-addon">
                      <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                  </div>
              </div>

              <div class="clearfix form-group" id="submit_btn_debit">
                  <input type="submit" id="credit_submit_btn" class="btn btn-info" value="Submit">
                  &nbsp; &nbsp; &nbsp;
                  <button class="btn btn-danger" type="reset">
                      <i class="ace-icon fa fa-undo bigger-110"></i>
                      Reset
                  </button>
              </div>
          </form>
               </div>
            </div>
          </div>
        </div>
        
      </div>
    </div>
    </div>
@endsection

@section('custom_style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection


@section('custom_script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script type="text/javascript">

        $(document).ready(function() {
            $('.select2').select2();
        });

        function show_terget_time(value) {
            if (value == '1') {
                $('#target_time').hide();
            }
            else if (value == '2') {
                $('#target_time').show();
            }
            else if (value == '3') {
                $('#target_time').show();
            }

        }


        function submit_button_control(credit) {

            var resellerBalance = parseFloat("{{$paymentable_balance}}");

            var now_balance = Number(credit.value);
            // alert(resellerBalance);
            if (resellerBalance >= now_balance && now_balance > -1) {
                $("#credit_submit_btn").css("display", "inline-block");
            } else {
                $("#credit_submit_btn").css("display", "none");
            }
        }
    </script>

    @include('admin.ajax.check_customer_available_balance')
@endsection








