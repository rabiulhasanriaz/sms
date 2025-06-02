@extends('admin.master')

@section('api_add_class','current-page')
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
                            <span class="input-group-btn">
            <a href="#modal_import_category_contact_from_file" class="btn btn-secondary pull-right" role="button" data-toggle="modal">ADD API</a>
          </span>

                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>API Permission  <small>Set</small></h2>
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
                                                <th>API Name</th>
                                                <th>API URL</th>
                                                <th>API Balance</th>
                                                <th>Status</th>
                                                <th>Edit</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php($sl=0)
                                            @foreach ($apis as $api)
                                                <tr>
                                                    <td>{{ ++$sl }}</td>
                                                    <td>{{ $api->api_name }}</td>
                                                    <td>{{ $api->api_url }}</td>
                                                    <td>{{ $api->api_balance }}</td>
                                                    <td>
                                                        @if ($api->api_status == 1)
                                                            <span class="text-success">Active</span>
                                                        @elseif($api->api_status == 2)
                                                            <span class="text-danger">Suspend</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.api-status-active', $api->id) }}" onclick="return confirm('Are you sure?')" style="color: green;">Active</a>
                                                        |
                                                        <a href="{{ route('admin.api-status-suspend', $api->id) }}" onclick="return confirm('Are you sure?')" style="color: red;">Suspend</a>
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

    {{--start modal section--}}
    <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12 padding">

            <!-- /.modal-dialog  start-->
            <div id="modal_import_category_contact_from_file" class="modal fade" tabindex="-1" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('admin.api-add-insert')  }}" method="POST" enctype="multipart/form-data">
                            @csrf
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                            </button>
                            <h3 class="smaller lighter blue no-margin text-primary"> API Add </h3>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>API Name </label>
                                <input type="text" class="form-control" name="api_name"  required="">
                            </div>
                            <div class="form-group">
                                <label>URL </label>
                                <input type="text" class="form-control" name="api_url" required="">
                            </div>
                            <div class="form-group">
                                <label>API Balance </label>
                                <input type="text" class="form-control" name="api_balance" required="">
                            </div>
                            <br>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-sm btn-primary pull-right">
                                <i class="fa-check-square-o fa fa-times"></i>
                                Submit
                            </button> &nbsp;&nbsp;
                            <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                                <i class="ace-icon fa fa-times"></i>
                                Close
                            </button>
                        </div>
                        </form>
                    </div><!-- /.modal-content -->
                    <div id="aside-inside-modal"
                         class="modal aside aside-contained aside-bottom aside-hz aside-dark aside-hidden no-backdrop"
                         data-placement="bottom" data-background="true" data-backdrop="false" tabindex="-1">
                    </div>
                </div><!-- /.modal-dialog -->
            </div>

        <!-- -----------end- file import modal-------- -->
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
