@extends('admin.master')

@section('dynamic_permission_class','current-page')
@section('content')
<div class="right_col" role="main">
<div class="">
    @if (session()->has('success'))
    <div class="x_content bs-example-popovers">
        <div class="alert alert-success alert-dismissible " role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
            </button>
            <strong>{{ session()->get('success') }}</strong>
        </div>
    </div>
        @endif
        @if (session()->has('suspend'))
        <div class="x_content bs-example-popovers">
            <div class="alert alert-danger alert-dismissible " role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                </button>
                <strong>{{ session()->get('suspend') }}</strong>
            </div>
        </div>
    @endif 
  <div class="page-title">
    <div class="title_left">
      <h3>API Permission </h3>
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
          <h2>Dynamic Permission  <small>Set</small></h2>
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
                    <th>SL</th>
                    <th>User name</th>
                    <th>Company Name</th>
                    <th>Cellphone</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                ($sl=0)
                @endphp
                @foreach ($api_user as $user)
                <tr>
                    <td>{{ ++$sl }}</td>
                    <td>{{ $user->userDetail->name }}</td>
                    <td>{{ $user->company_name }}</td>
                    <td>{{ $user->cellphone }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if ($user->userDetail->dynamic_permission == 1)
                            <span class="text-success">Active</span>
                        @elseif($user->userDetail->dynamic_permission == 0)
                        <span class="text-danger">Suspend</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.dynamic-permission-active', $user->id) }}" onclick="return confirm('Are you sure?')" style="color: green;">Active</a>
                        |
                        <a href="{{ route('admin.dynamic-permission-suspend', $user->id) }}" onclick="return confirm('Are you sure?')" style="color: red;">Suspend</a>
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