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
              <h2>Route  <small>Edit</small></h2>
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
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 offset-md-3">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">
                
                            <form action="{{ route('admin.english.route-update',$route->id) }}" method="post" class="form-horizontal"
                                  role="form">
                
                                @csrf
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-lg-offset-3 col-md-offset-3">
                                    <!-- PAGE CONTENT BEGINS -->
                                    <div class="form-group">
                                        <label for="form-field-select-3">Route Name</label>
                
                                        <input type="text" name="route_name" value="{{ $route->route_name }}"
                                               class="form-control" placeholder="Route Name"
                                               maxlength="100" required="">
                                        
                                    </div>
                
                
                                    <div class="form-group">
                                        <label for="form-field-select-3"> User name</label>
                
                                        <input type="text" name="api_username" value="{{ $route->user_name }}"
                                               class="form-control" placeholder="Api user name"
                                               maxlength="100" required="">
                                        
                                    </div>
                
                                    <div class="form-group">
                                        <label for="form-field-select-3"> Password</label>
                
                                        <input type="text" name="api_password" value="{{ $route->password }}"
                                               class="form-control" placeholder="Api password"
                                               maxlength="100" required="">
                                        
                                    </div>
                
                                    <div class="form-group">
                                        <label for="form-field-select-3"> Status</label>
                
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="inlineRadio1" value="1" {{ ($route->status == 1)? 'checked' : '' }}>
                                            <label class="form-check-label" for="inlineRadio1" style="color: green;">Active</label>
                                          </div>
                                          <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="inlineRadio2" value="0" {{ ($route->status == 0)? 'checked' : '' }}>
                                            <label class="form-check-label" for="inlineRadio2" style="color: red;">In-Active</label>
                                          </div>
                                        
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
                
            </div>
          </div>
        </div>
        
      </div>
    </div>
    </div>
@endsection

