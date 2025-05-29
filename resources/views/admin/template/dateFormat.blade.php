@extends('admin.master')

@section('template_menu_class','open')
@section('dateFormat_class', 'active')
@section('content')
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        @include('admin.partials.all_error_messages')
        @include('admin.partials.session_messages')
        

        <div class="title_left">
          <h3>Date Format </h3>
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
              <h2>Date Format  <small>Create</small></h2>
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
                    <div class="col-sm-6 offset-sm-3">
                      <div class="card-box">
                        <form action="{{ route('admin.template.date-format-store') }}" method="post" class="form-horizontal"
                      role="form">
                    @csrf
                    

                    <div class="form-group">
                        <label for="credit">Format <span class="text-success" id="CustomerBalance"></span></label>
                        <input type="text" name="format" id="credit" value="{{ old('format') }}" class="form-control input-mask-numberTk" placeholder="Date Format">
                        
                    </div>
                    <div class="clearfix form-group" id="submit_btn_debit">
                        <input type="submit" class="btn btn-info" value="Submit">
                    </div>
                </form>
                    </div>
                    </div>
        </div>
      </div>


      <div class="x_content">
            <div class="row">
                <div class="col-sm-12">
                  <div class="card-box">
          <table id="example" class="display nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Format</th>
                    <th>Show</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($formates as $temp)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $temp->dateFormat }}</td>
                        <td>{{ Carbon\Carbon::now()->format($temp->dateFormat) }}</td>
                        <td>{{ $temp->status == 1? 'Active' : 'In-Active' }}</td>
                        <td class="text-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-info">Action</button>
                                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu" role="menu" style="margin-left: -40px;">
                                    <li>
                                    <a href="javascript:void(0)" onclick="openModal('{{ $temp->id }}');" >Edit
                                    </a>
                                    </li>

                                    <li class="divider"></li>

                                    <li>
                                      <a href="{{ route('admin.template.format-delete',$temp->id) }}" onclick="return confirm('Are you sure?')">Delete
                                    </a>
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
    
    <div class="modal fade" id="formatModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content" id="formatDate">

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

function openModal(value){
  let url = "{{ route('admin.template.format-ajax' ) }}";

        $.ajax({
            url:url,
            type:'get',
            data:{value:value},
            success:function(result){
              $('#formatModal').modal('show');
              $('#formatDate').html(result);
            }
        })
  
}
</script>
@endsection