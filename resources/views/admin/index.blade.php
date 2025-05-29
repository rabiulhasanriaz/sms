@extends('admin.master')
@section('dashboard_menu_class','current-page')
@section('content')
<div class="right_col" role="main">
    <!-- top tiles -->
    <div class="row" >
    <div class="col-12 tile_count">
      <div class="col-md-3  tile_stats_count">
        <span class="count_top"><i class="fa fa-user"></i> Active User</span>
        <div class="count">{{ $data['active_user'] }}</div>
      </div>
      <div class="col-md-3  tile_stats_count">
        <span class="count_top"><i class="fa fa-clock-o"></i> Suspended User</span>
        <div class="count">{{ $data['suspend_user'] }}</div>
      </div>
      <div class="col-md-3  tile_stats_count">
        <span class="count_top"><i class="fa fa-user"></i> SMS IN DECEMBER</span>
        <div class="count green">{{ $data['last_month_sms'] }}</div>
      </div>
      <div class="col-md-3  tile_stats_count">
        <span class="count_top"><i class="fa fa-user"></i> Total Sms</span>
        <div class="count">{{ $data['total_sms'] }}</div>
      </div>
      
    
    </div>
  </div>
    <!-- /top tiles -->

    <div class="row">
      <div class="col-md-12 col-sm-12 ">
        <div class="dashboard_graph">

         

         
          

          <div class="clearfix"></div>
        </div>
      </div>

    </div>
    <br />

    


    <div class="row">
      


      <div class="col-md-12 col-sm-12 ">



        <div class="row">

          <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
              <div class="x_title">
                <h2>Visitors location <small>geo-presentation</small></h2>
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
                <div class="dashboard-widget-content">
                  <div class="col-md-8 hidden-small offset-md-2">
                    <table class="table table-bordered table-striped">
                        <thead class="thin-border-bottom">
                        <tr>
                            <th>
                                <i class="ace-icon fa fa-caret-right blue"></i>Month
                            </th>

                            <th>
                                <i class="ace-icon fa fa-caret-right blue"></i>Cost
                            </th>

                            <th class="">
                                <i class="ace-icon fa fa-caret-right blue"></i>SMS Count
                            </th>

                        </tr>
                        </thead>

                        <tbody>
                        @foreach($data['monthly_sms'] as $monthly_sms)
                            <tr>
                                @php($month=date("F",mktime(0,0,0,$monthly_sms->month,1,2000)))
                                <td style="text-transform: uppercase;">{{ $month }}, {{$monthly_sms->year}}</td>

                                <td class="hidden-480">
                                    <span class="label label-info arrowed-right arrowed-in">
                                        {{ number_format($monthly_sms->total_sms_cost, 2) }}
                                    </span>
                                </td>

                                <td class="hidden-480">
                                    <span class="label label-info arrowed-right arrowed-in">{{ $monthly_sms->total_sms }}</span>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
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