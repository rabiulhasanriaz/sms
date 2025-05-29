@extends('user.master')
@section('main_content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Phonebook Group List</h3>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            {{--session and error messages--}}
                            @include('admin.partials.all_error_messages')
                            @include('admin.partials.session_messages')
                            <div class="ajax_error" style="display: none">
                                <div class="alert alert-danger" id="report-alert">
                                    <button type="button" class="close" data-dismiss="alert"><span style="font-size: 20px;">x</span>
                                    </button>
                                    <span class="error_messages"></span>
                                </div>
                            </div>
                            <div class="ajax_success" style="display: none">
                                <div class="alert alert-success" id="report-alert">
                                    <button type="button" class="close" data-dismiss="alert"><span style="font-size: 20px;">x</span>
                                    </button>
                                    <span class="success_messages"></span>
                                </div>
                            </div>


                            <a href="#add_single_contact_modal" role="button" data-toggle="modal"
                            class="btn btn-danger btn-sm pull-right">&nbsp; Add Single Contact &nbsp;</a>
                            <a href="#import_contact_modal" role="button" data-toggle="modal"
                            class="btn btn-success btn-sm pull-right">&nbsp; Import Contact &nbsp;</a>
                            <a href="#add_new_category_modal" role="button" data-toggle="modal"
                            class="btn btn-primary btn-sm pull-right">&nbsp;
                                Add new group &nbsp;</a>


                            {{--contact group table section start--}}
                            <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12">

                                <table id="contact_group_table" class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Group name</th>
                                        <th>Contact</th>
                                        <th>Date</th>
                                        <th> Action</th>
                                        <th> System</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($serial=1)
                                    @foreach($contact_groups as $contact_group)
                                        <tr>
                                            <td>{{ $serial++ }}</td>
                                            <td>{{ @$contact_group->name }}</td>
                                            <td id="count_total_number{{@$contact_group->id}}">
                                                {{--{{ @$contact_group->Contacts->count() }}--}}
                                            {{\App\Model\PhonebookContact::where('category_id',$contact_group->id)->count()}}
                                            </td>
                                            <td>{{ @$contact_group->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <label>
                                                    <a href="#edit_category_modal" onclick="get_group_name('{{$contact_group->id}}', '{{$contact_group->name}}')" role="button" data-toggle="modal" class="btn-none-edit pass_id">
                                                        Edit </a>
                                                </label>
                                                | <a href="{{ route('user.phonebook.deleteCategory', $contact_group->id) }}" class="btn-none-delete"
                                                    onclick="return confirm('Are you sure you want to delete ?');"> Delete </a>
                                            </td>
                                            <td><a class="btn-none-details"
                                                href="{{ route('user.phonebook.show', $contact_group->id) }}">Show</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{--contact group table section end--}}


                            {{--start add new group --}}
                            <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12" style="background: #f8f8f8;">
                                <form action="{{ route('user.phonebook.storeCategory')  }}" method="post">
                                    @csrf
                                    <div id="add_new_category_modal" class="modal fade" tabindex="-1" style="display: none;">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myModalLabel">Add Group</h4>
                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Group name :
                                                    <input type="text" name="category_name" class="form-control" id=""
                                                        required="">
                                                    <br>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-sm btn-primary pull-right" type="submit">
                                                        <i class="fa-check-square-o fa fa-times"></i>Submit
                                                    </button> &nbsp;&nbsp;
                                                    <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                                                        <i class="ace-icon fa fa-times"></i>Close
                                                    </button>
                                                </div>
                                            </div><!-- /.modal-content -->
                                            <div id="aside-inside-modal"
                                                class="modal aside aside-contained aside-bottom aside-hz aside-dark aside-hidden no-backdrop"
                                                data-placement="bottom" data-background="true" data-backdrop="false" tabindex="-1">

                                            </div>
                                        </div><!-- /.modal-dialog -->
                                    </div>
                                </form>
                            </div>

                            <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">Today Report Details</h4>
                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                    </button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="SmsInformation"></div>
                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                                </div>
                            </div>
                            {{--end add new group section--}}


                            {{--start edit group --}}
                            <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12" style="background: #f8f8f8;">
                                <form action="{{ route('user.phonebook.updateCategory')  }}" method="post">
                                    @csrf
                                    <div id="edit_category_modal" class="modal fade" tabindex="-1" style="display: none;">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                                    </button>
                                                    <h3 class="smaller lighter blue no-margin text-primary"> Edit Group </h3>
                                                </div>
                                                <div class="modal-body">
                                                    Group name :
                                                    <input type="text" name="category_name" class="form-control" id="group_name"
                                                        required="">
                                                    <input type="hidden" name="group_id" id="group_id" value="">
                                                    <br>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-sm btn-primary pull-right" type="submit">
                                                        <i class="fa-check-square-o fa fa-times"></i>Update
                                                    </button> &nbsp;&nbsp;
                                                    <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                                                        <i class="ace-icon fa fa-times"></i>Close
                                                    </button>
                                                </div>
                                            </div><!-- /.modal-content -->
                                            <div id="aside-inside-modal"
                                                class="modal aside aside-contained aside-bottom aside-hz aside-dark aside-hidden no-backdrop"
                                                data-placement="bottom" data-background="true" data-backdrop="false" tabindex="-1">
                                            </div>
                                        </div><!-- /.modal-dialog -->
                                    </div>
                                </form>
                            </div>
                            {{--end edit group section--}}



                            <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12" style="background: #f8f8f8;">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-lg-12">
                                    {{--start import contact section--}}
                                    <form action="{{ route('user.phonebook.importContact') }}" method="post" id="import_contact_form" enctype="multipart/form-data">
                                        @csrf
                                        <!-- /.modal-dialog  start-->
                                        <div id="import_contact_modal" class="modal fade" tabindex="-1" style="display: none;">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="myModalLabel">Import Contact</h4>
                                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="group_id">Groups</label>
                                                            <select name="group_id" id="group_id" class="chosen-select form-control" required="">
                                                                <option value="">Nothing Selected</option>
                                                                @foreach($contact_groups as $contact_group)
                                                                    <option value="{{ $contact_group->id }}"> {{ $contact_group->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Import Contact <span class="text-primary">(only .xls, .xlsx, .csv, .txt file accept)</span></label>
                                                            <input type="file" name="contact_file" accept=".xls, .xlsx, .csv, .txt" required="">
                                                        </div>
                                                        <br>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-sm btn-primary pull-right"
                                                                name="group_fileUpload_submit">
                                                            <i class="fa-check-square-o fa fa-times"></i>Submit
                                                        </button> &nbsp;&nbsp;
                                                        <button class="btn btn-sm btn-danger pull-right modal_close" data-dismiss="modal">
                                                            <i class="ace-icon fa fa-times"></i>Close
                                                        </button>
                                                    </div>
                                                </div><!-- /.modal-content -->
                                                <div id="aside-inside-modal"
                                                    class="modal aside aside-contained aside-bottom aside-hz aside-dark aside-hidden no-backdrop"
                                                    data-placement="bottom" data-background="true" data-backdrop="false" tabindex="-1">
                                                </div>
                                            </div><!-- /.modal-dialog -->
                                        </div>
                                    </form>
                                    {{--start import contact section--}}

                                    <!-- -----------Single  contact model- start--------- -->
                                    <form action="{{ route('user.phonebook.storeContact') }}" method="post">
                                        @csrf
                                        <div id="add_single_contact_modal" class="modal fade" tabindex="-1" style="display: none;">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="myModalLabel">Add Contact</h4>
                                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="contact_number">Contact No </label>
                                                            <input type="text" name="contact_number" id="contact_number"
                                                                class="form-control" required="">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="">Name </label>
                                                            <input type="text" name="contact_name" id="valuePass" class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="">Designation </label>
                                                            <input type="text" name="designation" class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Groups</label>
                                                            <select name="category_id" class="chosen-select form-control"  required="">
                                                                <option value="">Nothing Selected</option>
                                                                @foreach($contact_groups as $contact_group)
                                                                    <option value="{{ $contact_group->id }}">{{ $contact_group->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Status</label><br>
                                                            <label>
                                                                <input type="radio" class="ace" name="contactStatus" value="1"
                                                                    required="" checked="">
                                                                <span class="lbl">  Enabled </span>
                                                            </label>
                                                            <label>
                                                                <input type="radio" class="ace" name="contactStatus" value="2"
                                                                    required="">
                                                                <span class="lbl"> Disabled </span>
                                                            </label>
                                                        </div>
                                                        <br>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button id="button_SingleDisNone" class="btn btn-sm btn-primary pull-right"
                                                                name="single_Contact_submit">
                                                            <i class="fa-check-square-o fa fa-times"></i>Submit
                                                        </button> &nbsp;&nbsp;
                                                        <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                                                            <i class="ace-icon fa fa-times"></i>Close
                                                        </button>
                                                    </div>
                                                </div><!-- /.modal-content -->
                                                <div id="aside-inside-modal"
                                                    class="modal aside aside-contained aside-bottom aside-hz aside-dark aside-hidden no-backdrop"
                                                    data-placement="bottom" data-background="true" data-backdrop="false" tabindex="-1">
                                                </div>
                                            </div><!-- /.modal-dialog -->
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('custom_style')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css" />
    <style>
        .chosen-container { width: 100% !important; }
        .tab-content { border: none !important; }
    </style>
    <link href="{{ asset('assets/datatable/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/datatable/rowReorder.dataTables.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/datatable/responsive.dataTables.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        @media(max-width:575px){
            .abcd{
                width: 130px;
            }
        }

    </style>
@endsection


@section('custom_script')
    <script src="{{ asset('assets') }}/js/ajax_import_contact.js"></script>
    {{-- <script src="https://code.jquery.com/jquery-3.3.1.js"></script> --}}
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript">
        // $('#reseller_list').DataTable();
        $(document).ready(function() {
        var table = $('#contact_group_table').DataTable( {
            responsive: true,
            columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 4 },
                    { responsivePriority: 4, targets: 2 },
                    { responsivePriority: 5, targets: 3 },
            ]
        } );
    } );
    </script>

    <script type="text/javascript">
        // $('.chosen-select').chosen({allow_single_deselect:true});
        // $('#contact_group_table').DataTable();

        function get_group_name(group_id, group_name){
            $("#group_name").val(group_name);
            $("#group_id").val(group_id);
        }


        import_contact_form_submit('import_contact_form','{{ route('user.phonebook.importContact') }}', 'import_contact_modal');
    </script>
@endsection
