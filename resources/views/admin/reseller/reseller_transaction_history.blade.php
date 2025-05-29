@extends('admin.master')

@section('reseller_menu_class','open')
@section('reseller_list_menu_class', 'active')
@section('content')
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        @include('reseller.partials.session_messages')
        <div class="title_left">
          <h3>Transaction History </h3>
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
              <h2>Transaction <small>History</small></h2>
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
            
                        <div>
                            <div id="user-profile-1" class="user-profile row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="profile-user-info profile-user-info-striped">
                                        <div class="col-md-12">
                                            <h2 class="text-center text-primary"><span><img
                                                            src="{{ OtherHelpers::user_logo($user->userDetail['logo'])  }}"
                                                            style="height: 40px;"></span> <span>{{ $user->name }}</span></h2>
                                            <h3 class="text-center text-primary"> Transaction History </h3>
                                        </div>
            
                                        <div class="col-md-12">
                                            <table class="table table-responsive table-bordered table-hover"
                                            id="transaction_history">
                                         <thead>
                                         <tr class="bg-info">
                                             <th>SL</th>
                                             <th>Payment Referance</th>
                                             <th>Submit date</th>
                                             <th>Credit</th>
                                             <th>Debit</th>
                                             <th>Balance</th>
                                         </tr>
                                         </thead>
     
                                         <tbody>
                                         @php($available_balance=0)
                                         @php($total_credit=0)
                                         @php($total_debit=0)
                                         @php($serial=1)
                                         @foreach($SmsBalances as $smsBalance)
                                             @php($available_balance= $available_balance+$smsBalance->asb_credit-$smsBalance->asb_debit)
                                             @php($total_credit=$total_credit+$smsBalance->asb_credit)
                                             @php($total_debit=$total_debit+$smsBalance->asb_debit)
                                             <tr>
                                                 <td>{{ $serial++ }}</td>
                                                 <td>{{ $smsBalance->asb_pay_ref }}</td>
                                                 <td>{{ $smsBalance->created_at->format('j M, Y') }}</td>
                                                 <td class="text-right">{{ $smsBalance->asb_credit }}</td>
                                                 <td class="text-right">{{ $smsBalance->asb_debit }}</td>
                                                 <td class="text-right">{{ $available_balance }}</td>
                                             </tr>
                                         @endforeach
                                         </tbody>
     
     
                                         <tfoot>
                                         <tr>
                                             <th colspan="3">Total:</th>
                                             <th class="text-right">={{ number_format($total_credit,2) }}</th>
                                             <th class="text-right">= {{ number_format($total_debit,2) }}</th>
                                             <th class="text-right">= {{ number_format($available_balance,2) }}</th>
                                         </tr>
                                         </tfoot>
                                     </table>
                                      </div>
                                    </div>
                                    <div class="space-20"></div>
            
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
@section('custom_style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.7/css/rowReorder.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.dataTables.min.css">
@endsection
@section('custom_script')
    {{-- <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
    $('#employee-list-table').DataTable();
    </script> --}}
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.7/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
    <script>
    $(document).ready(function() {
    var table = $('#transaction_history').DataTable( {
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true
    } );
    } );
    </script>
@endsection
