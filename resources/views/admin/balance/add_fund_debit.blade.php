@extends('admin.master')

@section('account_menu_class','open')
@section('add_fund_debit_menu_class', 'active')
@section('content')
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        @include('admin.partials.all_error_messages')
		@include('admin.partials.session_messages')
        <div class="title_left">
          <h3>Reseller </h3>
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
              <h2>Reseller  <small>List</small></h2>
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
                <div class="row">
                    <div class="col-sm-6 offset-sm-3">
                      <div class="card-box">
                        <form action="{{ route('admin.balance.debit.store') }}" method="post" class="form-horizontal" role="form">
							@csrf
							<div class="form-group">
								<label for="form-field-select-3"> Company name </label>
								<br />
								<select class="select2 form-control" id="form-field-select-3" data-placeholder="Company name.." name="user_id" required="" onchange="customer_balance(this.value)">
									<option value="">  </option>
									@foreach($resellers as $reseller)
										<option value="{{ $reseller->id }}"> {{ $reseller->company_name }}- ( {{ $reseller->cellphone }}
											)
										</option>
									@endforeach
								</select>
							</div>
			
							<div class="form-group">
								<label for="credit">Debit amount <span class="text-success" id="CustomerBalance"></span></label>
								<input type="text" name="debit_amount" id="credit" onkeyup="show_terget_time(this.value)" value="" class="form-control input-mask-numberTk" placeholder="00.00" maxlength="10" required>
							</div>
			
							<div class="form-group">
								<label for="payReference"> Payment referance :</label>
			
								<input type="text" name="payment_reference" id="payReference" value="" class="form-control" placeholder="......" maxlength="32" required>
							</div>
					
							<div class="clearfix form-group" id="submit_btn_debit">
									<input type="submit" class="btn btn-info" value="Submit" id="submitBtn" >
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
    </div>
    </div>
@endsection


@section('custom_style')
	<link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css"/>
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('custom_script')
	<script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
	<script type="text/javascript">
	  $(document).ready(function() {
            $('.select2').select2();
        });
		$('.chosen-select').chosen({allow_single_deselect: true});

        function show_terget_time(value) {

            var max_ammount = $("#balanceOfCustomer").text();
            max_ammount = parseFloat(max_ammount);
            if(value>max_ammount){
                $("#submitBtn").hide();
			}
			else{
			    $("#submitBtn").show();
			}
        }
	</script>
	@include('admin.ajax.check_customer_available_balance')
@endsection