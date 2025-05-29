@extends('admin.master')

@section('reseller_ac_limit_menu_class','open')
@section('limit_apply_menu_class', 'active')
@section('content')
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        @include('admin.partials.session_messages')
        @include('admin.partials.all_error_messages')
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
                    <div class="col-sm-12">
                      <div class="card-box table-responsive">
              <table id="example" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th class="abcd">SL</th>
                        <th class="company">Company name</th>
                        <th>User name</th>
                        <th>Email</th>
                        <th>Credit limit</th>
                        <th>Employee limit</th>
                        <th>System</th>
                    </tr>
                </thead>
                <tbody>
                    @php($serial=1)
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $serial++ }}</td>
                            <td>{{ $user->company_name }}</td>
                            <td>{{ $user->userDetail['name'] }}</td>
                            <td>{{ $user->email }}</td>
                            
                            <form action="{{ route('admin.reseller.limitUpdate', $user->id) }}" id="form_{{$user->id}}" method="post">
                                    @csrf
                                <td>
                                    <input type="text" name="balanceLimit" class="input-sm" value="{{ $user->userDetail['limit'] }}">
                                </td>
    
                                <td>
                                    <input type="text" name="employeeLimit" class="input-sm" value="{{ $user->employee_limit }}">
                                </td>
                            </form>
                           
                            <td>
                                <button class="btn btn-primary btn-xs" onclick="submitLimitForm('form_{{$user->id}}')">Submit</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
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
@endsection
@section('custom_style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.7/css/rowReorder.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.dataTables.min.css">
@endsection
@section('custom_script')
    {{-- <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script> --}}
    <script type="text/javascript">
        // $('#reseller-limit-apply-table').DataTable();
        function submitLimitForm(formName){
            if(confirm('Are you Sure')) {
                $("#" + formName).submit();
            }
        }
    </script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.7/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function() {
        var table = $('#example').DataTable( {
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true
        } );
    } );
    </script>

@endsection
