@extends('admin.master')

@section('account_menu_class','open')
@section('assign_route_class', 'active')
@section('content')
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        @include('admin.partials.all_error_messages')
        @include('admin.partials.session_messages')
        <div class="title_left">
          <h3>Assign Route </h3>
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
              <h2>Assign  <small>Route</small></h2>
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
                        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 offset-md-3 bg-container">
                
                            <form action="{{ route('admin.english.assign-route-store') }}" method="post" class="form-horizontal"
                                  role="form">
                
                                @csrf
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-3 col-md-offset-3">
                                    <!-- PAGE CONTENT BEGINS -->
                                    <div class="form-group">
                                        <label for="form-field-select-3"> Route </label>
                                            <br/>
                                            <select class="chosen-select form-control" id="form-field-select-3"
                                                    data-placeholder="Operator name.." name="route_name" required="">
                                                <option value=""></option>
                                                @foreach ($routes as $route)
                                                    <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                                                @endforeach
                                            </select>
                                        
                                    </div>
                
                
                                    <div class="form-group">
                                        <label for="form-field-select-3"> User name</label>
                                        <br/>
                                        <select class="select2 form-control" id="form-field-select-3"
                                                data-placeholder="Select User" name="user_name" required="">
                                            <option value=""></option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->company_name }}</option>
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
                        </div><!-- end bg-container-->
                    </div>
                </div>
                <div class="row">
                    <table id="example" class="display nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Route Name</th>
                                <th>User</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($sl=0)
                            @foreach ($assinged_routes as $route)
                            <tr>
                                <td>{{ ++$sl }}</td>
                                <td>{{ $route->routeDetail->route_name }}</td>
                                <td>{{ $route->userDetail->company_name }}</td>
                                <td>
                                    @if ($route->status == 1)
                                        <span class="text-success">Active</span>
                                    @else
                                        <span class="text-danger">In-Active</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.english.assign-route-edit',$route->id) }}" class="btn btn-xs btn-info">Edit</a>
                                    <a href="{{ route('admin.english.assign-route-delete',$route->id) }}" onclick="return confirm('Are you sure to delete this Assigned route?')" class="btn btn-xs btn-danger">Delete</a>
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
@endsection

@section('custom_style')
    <link href="{{ asset('assets') }}/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css"/>
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js//moment.min.js"></script>
    <script src="{{ asset('assets') }}/js//bootstrap-datetimepicker.min.js"></script>
    <script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script type="text/javascript">
     $(document).ready(function() {
            $('.select2').select2();
        });
        $(function () {
            $('#datetimepicker1').datetimepicker();
        });


        $('.chosen-select').chosen({allow_single_deselect: true});
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
    </script>
    @include('admin.ajax.check_customer_available_balance')
@endsection