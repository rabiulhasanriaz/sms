@extends('reseller.master')

@section('user_list_menu_class','current-page')
@section('user_menu_class','open')
@section('content')
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        @include('admin.partials.session_messages')
        <div class="title_left">
          <h3>User </h3>
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
              <h2>User  <small>List</small></h2>
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
                      <div class="card-box">
                        <table id="user-list-table" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th style="width: 20px;">SL</th>
                                <th class="abcd">Company name</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th class="">Phone</th>
                                <th class="">Customar</th>
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
                                    <td class=""><a href="tel:{{ $user->cellphone }}">{{ $user->cellphone }}</a></td>
                                    <td class="">
                                        <p style='color:green;'>
                                            @if($user->role==4)
                                                Reseller
                                            @else
                                                User
                                            @endif
                                        </p>
                                    </td>
                                    <td>
                                        

                                        <div class="btn-group">
                                            <button type="button" class="btn btn-info">Action</button>
                                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu" style="margin-left: -40px;">
                                                <li>
                                                <a href="{{ route('reseller.user.priceView', $user->id) }}">Price View
                                                </a>
                                                </li>
            
                                                <li class="divider"></li>
            
                                                <li>
                                                <a href="{{ route('reseller.user.transactionHistory', $user->id) }}">Account
                                                </a>
                                                </li>
            
                                                <li class="divider"></li>
                                                @if( $user->status == 1 )
                                                <li>
                                                <a href="{{ route('reseller.user.suspend', $user->id) }}" onclick="return confirm('Are you sure ?');">Suspend
                                                </a>
                                                </li>
                                                @else
                                                <li>
                                                <a href="{{ route('reseller.user.active', $user->id) }}" onclick="return confirm('Are you sure ?');">Re-Active
                                                </a>
                                                </li>
                                                @endif
                                                <li class="divider"></li>
                                                <li><a href="{{ route('reseller.user.edit', $user->id) }}">Customer Edit</a></li>
                                                <li class="divider"></li>
                                                <li><a href="{{ route('reseller.user.goToThisAccount', $user->id) }}">Go to this account</a></li>
                                            </ul>
                                        </div>
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
    var table = $('#user-list-table').DataTable( {
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true
    } );
} );
</script>
@endsection
