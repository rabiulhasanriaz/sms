@extends('admin.master')

@section('template_menu_class','open')
@section('template_assign_class', 'active')
@section('content')
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        @include('admin.partials.all_error_messages')
        @include('admin.partials.session_messages')
        

        <div class="title_left">
          <h3>Template </h3>
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
              <h2>Template  <small>Create</small></h2>
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
            
            @php
                $userTemp = explode(',',$userData->template_permission);
                
            @endphp

      <div class="x_content">
            <div class="row">
                <div class="col-sm-12">
                  <div class="card-box">
        <form action="{{ route('admin.template.template-user-give',$user->id) }}" method="post">
            @csrf
            <table id="example" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($templates as $temp)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $temp->template_title }}</td>
                            <td>{{ $temp->template_content }}</td>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="temps[]" {{ in_array($temp->id,$userTemp) ? 'checked' : '' }} value="{{ $temp->id }}" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">
                                     
                                    </label>
                                  </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="submit" class="btn btn-info btn-xs">Submit</button>
        </form>
          
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