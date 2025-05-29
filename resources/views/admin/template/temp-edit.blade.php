@extends('admin.master')

@section('template_menu_class','open')
@section('template_create_class', 'active')
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
            <div class="x_content">
                <div class="row">
                    <div class="col-sm-6 offset-sm-3">
                      <div class="card-box">
                        <form action="{{ route('admin.template.template-update',$temp->id) }}" method="post" class="form-horizontal"
                      role="form">
                    @csrf
                    

                    <div class="form-group">
                        <label for="credit">Title <span class="text-success" id="CustomerBalance"></span></label>
                        <input type="text" name="title" id="credit" value="{{ $temp->template_title }}" class="form-control input-mask-numberTk" placeholder="Template Content">
                        
                    </div>

                    <div class="form-group">
                        <label for="payReference"> Content :</label>
                        <input type="text" name="content" id="payReference" value="{{ $temp->template_content }}" class="form-control"
                               placeholder="Template Content">
                        
                    </div>

                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" {{ $temp->status == 1 ? 'checked' : '' }} id="inlineRadio1" value="1">
                            <label class="form-check-label" for="inlineRadio1">Active</label>
                          </div>
                          <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" {{ $temp->status == 0 ? 'checked' : '' }} id="inlineRadio2" value="0">
                            <label class="form-check-label" for="inlineRadio2">In Active</label>
                          </div>
                        
                    </div>
                    
                    

                  

                    <div class="clearfix form-group" id="submit_btn_debit">
                        <input type="submit" class="btn btn-info" value="Submit">
                    </div>
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

