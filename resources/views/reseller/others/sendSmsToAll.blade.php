@extends('reseller.master')


@section('send_sms_to_all_users', 'open')

@section('content')
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        @include('reseller.partials.session_messages')
        <div class="title_left">
          <h3>Send sms to all user and resellers </h3>
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
              <h2>Send sms to all user and resellers</h2>
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
                        <div class="row">
                            <div class="col-sm-6">
                                @include('reseller.partials.session_messages')
                        
                                <div id="send-sms-content">
                                    <form action="{{ route('reseller.sendSmsToAll') }}" method="POST">
                                        @csrf
                                        <label>Create / Paste your Message here</label><br />
                                        <textarea class="count_me form-control" name="message" id="message" required=""
                                                  style="min-height: 120px;"></textarea>
                        
                        
                                        <div class="block">
                                            <div class=""><span>CHECK YOUR SMS COUNT</span><span style="color: red;"> ( English : 160 character, bangla sms(max character=315))</span></div>
                                            <div class="">
                                                <div style=""><span class="charleft contacts-count">&nbsp;</span><span
                                                            class="parts-count"></span></div>
                                            </div>
                                        </div>
                                        
                                        <input type="submit" type="button" class="btn btn-success" name="" value="Send">
                                    
                                </div>
                            </div><!-- /.col -->
                        
                            <div class="col-sm-3">
                                <br />
                                <div style="border: 1px solid gray; background: rgba(255,255,255, 0.1); padding: 10px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="reseller_user" id="reseller" value="{{ $numbers_reseller }}">
                                        <label class="form-check-label" for="reseller">
                                            Reseller ({{ $total_reseller }})
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="reseller_user" id="user" value="{{ $numbers_user }}">
                                        <label class="form-check-label" for="user">
                                            User ({{ $total_user }})
                                        </label>
                                    </div> 
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="reseller_user" id="total" value="{{ $numbers_total }}">
                                        <label class="form-check-label" for="total">
                                            Total ({{ $total_reseller + $total_user }})
                                        </label>
                                    </div>
                                    With this operation , you will be able to send a message to all of you Clients.
                                </div>
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
    </div>
@endsection

@section('custom_style')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css"/>
@endsection


@section('custom_script')
@include('reseller.ajax.employee')
    
    <script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.textareaCounter.plugin.js"></script>
    <script src="{{ asset('assets') }}/js/text-area-counter.js"></script>

    <script type="text/javascript">
        $('.chosen-select').chosen({allow_single_deselect: true});
        $(document).ready(function () {
            count_textarea('#send-sms-content');
        });
    </script>

    

@endsection