@extends('reseller.master')

@section('user_list_menu_class','active')
@section('user_menu_class','open')

@section('content')
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        @include('reseller.partials.session_messages')
        @include('reseller.partials.all_error_messages')
        <div class="title_left">
          <h3>Price </h3>
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
              <h2>Price  <small>Update</small></h2>
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
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            
                        <div class="col-lg-12 col-md-12 widget-container-col ui-sortable" id="widget-container-col-13">
                            <div class="widget-box transparent ui-sortable-handle" id="widget-box-13">
                                <div class="widget-header">
                                    <h4 class="widget-title lighter"> {{ $user->userDetail->company_name }}</h4>
                                    <div class="widget-toolbar no-border">
                                        <ul class="nav nav-tabs" id="myTab2">
                                            <li class="active">
                                                <a data-toggle="tab" href="#profile2" aria-expanded="true">Operator rate</a>
                                            </li>
                                            <li class="">
                                                <a data-toggle="tab" href="#home2" aria-expanded="false">Profile</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
            
                                <div class="widget-body">
                                    <div class="widget-main padding-12 no-padding-left no-padding-left">
            
                                        
            
                                        <div class="tab-content padding-4">
                                            <div id="profile2" class="tab-pane active">
                                                <div class="scroll-track" style="display: none;">
                                                    <div class="scroll-bar"></div>
                                                </div>
                                                <div class="scroll-content">
                                                    <div class="col-lg-10 col-md-10">
            
                                                        <table class="table table-bordered table-responsive">
                                                            <thead>
                                                            <tr>
                                                                <th>Country</th>
                                                                <th>Operator</th>
                                                                <th>Prefix</th>
                                                                <th>Buying Price/SMS</th>
            
                                                                <th> Selling Price</th>
                                                                <th> Active</th>
                                                            </tr>
                                                            </thead>
            
                                                            <tbody>
                                                            @foreach($smsRates as $smsRate)
                                                                <tr>
                                                                    <td> {{ $smsRate->country->country_name }}</td>
                                                                    <td> {{ $smsRate->operator->ope_operator_name }}</td>
                                                                    <td> {{ $smsRate->operator->ope_number }}</td>
                                                                    <td>EURO :{{ $smsRate->asr_dynamic }}
                                                                        </td>
                                                                    <td>
                                                                        <form action="{{ route('reseller.user.priceUpdate', $smsRate->id) }}"
                                                                              method="post" id="form_{{$smsRate->id}}">
                                                                            @csrf
                                                                            
                                                                            <input type="text" name="dynamic_price"
                                                                                    class="col-md-5"
                                                                                    value="{{ $smsRate->asr_dynamic }}" required>
                                                                        </form>
                                                                    </td>
                                                                    <td><input type="button"
                                                                               onclick="submitRateForm('form_{{$smsRate->id}}')"
                                                                               value="Save" class="btn btn-sm btn-primary"></td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
            
                                                    </div>
                                                </div>
                                            </div>
            
                                            <div id="home2" class="tab-pane">
                                                <div class="scrollable-horizontal ace-scroll" data-size="800"
                                                     style="position: relative; padding-top: 12px;">
                                                    <div style="width: 800px;">
            
                                                        <div class="col-lg-10 col-md-10">
                                                            <table class="table table-bordered">
                                                                <tr>
                                                                    <th class="col-md-3">Company name</th>
                                                                    <td> {{ $user->company_name }}</td>
                                                                </tr>
            
                                                                <tr>
                                                                    <th class="col-md-3">Name</th>
                                                                    <td> {{ $user->userDetail->name }}</td>
                                                                </tr>
            
                                                                <tr>
                                                                    <th class="col-md-3">Email</th>
                                                                    <td> {{ $user->email }}</td>
                                                                </tr>
            
                                                                <tr>
                                                                    <th class="col-md-3">Cell Phone</th>
                                                                    <td> {{ $user->cellphone }}</td>
                                                                </tr>
            
                                                                <tr>
                                                                    <th class="col-md-3">Desgination</th>
                                                                    <td> {{ $user->userDetail->designation }}</td>
                                                                </tr>
            
                                                                <tr>
                                                                    <th class="col-md-3">Address</th>
                                                                    <td> {{ $user->userDetail->address }}</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            
            
                    </div><!-- /.col -->
                </div>
            </div>
          </div>
        </div>
        
      </div>
    </div>
    </div>
@endsection

@section('custom_script')
    <script type="text/javascript">
        $('#reseller-list-table').DataTable();

        function submitRateForm(formName) {

                $("#" + formName).submit();

        }
    </script>

@endsection
