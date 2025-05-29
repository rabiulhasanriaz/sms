@extends('admin.master')

@section('account_menu_class','open')
@section('route_register_class', 'active')
@section('content')
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        @include('admin.partials.all_error_messages')
        @include('admin.partials.session_messages')
        <div class="title_left">
          <h3>Route </h3>
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
              <h2>Route  <small>Permission</small></h2>
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
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">
                
                            <form action="{{ route('admin.english.route-register-store') }}" method="post" class="form-horizontal"
                                  role="form">
                
                                @csrf
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-2 col-md-offset-2 offset-md-3">
                                    <!-- PAGE CONTENT BEGINS -->
                                    <div class="form-group">
                                        <label for="form-field-select-3">Route Name</label>
                
                                        <input type="text" name="route_name" value="{{ old('route_name') }}"
                                               class="form-control" placeholder="Route Name"
                                               maxlength="100" required="">
                                        
                                    </div>
                
                
                                    <div class="form-group">
                                        <label for="form-field-select-3"> User name</label>
                
                                        <input type="text" name="api_username" value="{{ old('api_username') }}"
                                               class="form-control" placeholder="Api user name"
                                               maxlength="100" required="">
                                        
                                    </div>
                
                                    <div class="form-group">
                                        <label for="form-field-select-3"> Password</label>
                
                                        <input type="text" name="api_password" value="{{ old('api_password') }}"
                                               class="form-control" placeholder="Api password"
                                               maxlength="100" required="">
                                        
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
                    <div class="offset-md-3">
                        <table id="example" class="display nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Route Name</th>
                                    <th>User Name</th>
                                    <th>Password</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($sl=0)
                                @foreach ($routes as $route)
                                <tr>
                                    <td>{{ ++$sl }}</td>
                                    <td>{{ $route->route_name }}</td>
                                    <td>{{ $route->user_name }}</td>
                                    <td>{{ $route->password }}</td>
                                    <td>
                                        @if ($route->status == 1)
                                            <span class="text-success">Active</span>
                                        @else
                                            <span class="text-danger">In-Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.english.route-edit',$route->id) }}" class="btn btn-xs btn-info">Edit</a>
                                        <a href="{{ route('admin.english.route-delete',$route->id) }}" onclick="return confirm('Are you sure to delete this route?')" class="btn btn-xs btn-danger">Delete</a>
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
@endsection

@section('custom_style')
    <link href="{{ asset('assets') }}/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" />
	<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.7/css/rowReorder.dataTables.min.css" />
	<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.dataTables.min.css" />
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js//moment.min.js"></script>
    <script src="{{ asset('assets') }}/js//bootstrap-datetimepicker.min.js"></script>
    <script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
	<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/rowreorder/1.2.7/js/dataTables.rowReorder.min.js"></script>
	<script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
	<script type="text/javascript">
       $(document).ready(function() {
            var table = $('#example').DataTable( {
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
                responsive: true
            } );
        } );
	</script>
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