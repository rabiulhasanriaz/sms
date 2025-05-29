@extends('reseller.master')


@section('employee_menu_class','open')
@section('pay_to_employee','active')

@section('content')
<div class="right_col" role="main">
<div class="">
    @include('reseller.partials.all_error_messages')
    @include('reseller.partials.session_messages')
    <div class="page-title">
        <div class="title_left">
            <h3>Pay to Employee</h3>
        </div>

        <div class="title_right">
            <div class="col-md-5 col-sm-5  form-group pull-right top_search">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button">Go!</button>
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
                    <h2>Pay to Employee</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a class="dropdown-item" href="#">Settings 1</a>
                                </li>
                                <li><a class="dropdown-item" href="#">Settings 2</a>
                                </li>
                            </ul>
                        </li>
                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="col-sm-6 offset-sm-3">
                        <p id="CustomerBalance" class="text-info"></p>
                        <form action="{{ route('reseller.employee.pay_balance') }}" method="post" class="form-horizontal" role="form"
                        enctype="multipart/form-data">
                      @csrf
  
                      <div class="form-group">
                        <label for="form-field-select-3"> Employee name :  <span style="color: red;">**</span></label>
                        <br />    
                        <select id="form-field-select-3" name="employee_id" data-placeholder="Select an Employee"
                               class="select2 form-control"  required="" onchange="get_employee_payable_balance(this.value)" value="">
                               <option value="" hidden></option>
                               @foreach($data as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name.' - '.$employee->phone }}</option>
                                @endforeach
                               
                        </select>
                      </div>
                      <div class="form-group">
                          <label for="userName"> Amount : </label>
                          <br />
                          <input type="text" name="pay_amount" class="form-control" placeholder="0.00 tk">
                      </div>
  
                      
              
                      <div class="clearfix form-group">
                        <div class="">
                            <input type="submit" class="btn btn-info" value="Pay this amount">
                            &nbsp; &nbsp; &nbsp;
                            <button class="btn btn-danger" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                Reset
                            </button>
                        </div>
                    </div>
                  </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
</div>
@include('reseller.ajax.employee_balance')
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
</script>
@endsection