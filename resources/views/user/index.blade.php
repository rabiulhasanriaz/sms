@extends('user.master')

@section('dashboard_menu_class','active')
@section('main_content')
<div class="right_col" role="main" id="app" v-cloak>
    <!-- top tiles -->
    <div class="row">
        <div class="col-12 tile_count">
            <div class="col-sm-3  tile_stats_count">
                <span class="count_top"><i class="fa fa-user"></i> SMS LAST WEEK</span>
                <div class="count">@{{ last_week_sms | formatAmount}}</div>
            </div>
            <div class="col-sm-3  tile_stats_count">
                <span class="count_top"><i class="fa fa-clock-o"></i> COST LAST WEEK</span>
                <div class="count">@{{ last_week_cost | formatAmount}}</div>
            </div>
            <div class="col-sm-3  tile_stats_count">
                <span class="count_top"><i class="fa fa-user"></i> SMS IN @{{ current_month }}</span>
                <div class="count green">@{{ last_month_sms | formatAmount}}</div>
            </div>
            <div class="col-sm-3  tile_stats_count">
                <span class="count_top"><i class="fa fa-user"></i> COST IN @{{ current_month }}</span>
                <div class="count">@{{ last_month_cost | formatAmount}}</div>
            </div>
        </div>
    </div>
    <!-- /top tiles -->




    <div class="row">
      <div class="col-md-4 col-sm-4 ">
        <div class="x_panel">
          <div class="x_title">
            <h2>STATISTICS</h2>
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

              <ul class="list-unstyled timeline widget">
                <li>
                  <div class="block">
                    <div class="block_content">
                        <p>Balance EURO : @{{ balance_bd | formatAmount}}
                            <b style="font-size: 15px;"><i class="fa fa-euro"></i></b></span>
                        </p>
                    </div>
                  </div>
                </li>
                <h3 style="font-size: 25px;">LAST 4 TRANSACTIONS</h3>
                <li v-for="tran in transaction">
                    <div class="block">
                      <div class="block_content">
                        @{{ tran.created_at | formatDate }}, EURO : @{{ (tran.asb_credit - tran.asb_debit) | formatAmount }} <i class="fa fa-euro"></i>
                      </div>
                    </div>
                  </li>
              </ul>
            </div>
          </div>
        </div>
      </div>


      <div class="col-md-8 col-sm-8 ">



        <div class="row">

          <div class="col-md-12 col-sm-12 ">
            <div class="x_panel">
              <div class="x_title">
                <h2>YOU SMS HISTORY</h2>
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
                    <div class="col-sm-4 offset-sm-2">
                        <div class="widget-box transparent">
                            <div class="widget-header widget-header-flat">
                                <h4 class="widget-title lighter">
                                    Month
                                </h4>
                            </div>

                            <div class="widget-body">
                                <div class="widget-main no-padding">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thin-border-bottom">
                                        <tr>
                                            <th>
                                                <i class="ace-icon fa fa-caret-right blue"></i>Month
                                            </th>
                                        </tr>
                                        </thead>

                                        <tbody>

                                            <tr v-for="monthly in monthly_sms">

                                                <td style="text-transform: uppercase; height: 53px;">@{{ monthly.month | formatMonth }}, @{{monthly.year}}</td>
                                            </tr>


                                        </tbody>
                                    </table>
                                </div><!-- /.widget-main -->
                            </div><!-- /.widget-body -->
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="widget-box transparent">
                            <div class="widget-header widget-header-flat">
                                <h4 class="widget-title lighter">
                                    <i class="ace-icon fa fa-star orange"></i>
                                    Your SMS History
                                </h4>
                            </div>

                            <div class="widget-body">
                                <div class="widget-main no-padding">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thin-border-bottom">
                                        <tr>
                                            <th>
                                                <i class="ace-icon fa fa-caret-right blue"></i>Cost
                                            </th>

                                            <th class="">
                                                <i class="ace-icon fa fa-caret-right blue"></i>SMS Count
                                            </th>

                                        </tr>
                                        </thead>

                                        <tbody>

                                            <tr v-for="monthly in monthly_sms">


                                                <td class="hidden-480">
                                                    <span class="label label-info arrowed-right arrowed-in">@{{ sms = monthly.total_sms_cost | formatAmount}}</span>
                                                </td>

                                                <td class="hidden-480">
                                                    <span class="label label-info arrowed-right arrowed-in">@{{ monthly.total_sms | formatAmount}}</span>
                                                </td>
                                            </tr>


                                        </tbody>
                                    </table>
                                </div><!-- /.widget-main -->
                            </div><!-- /.widget-body -->
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
    <style>
        [v-cloak] { display: none; }
        .label {
            display: unset;
        }
    </style>
@endsection
@section('custom_script')

<script src="https://cdn.jsdelivr.net/npm/vue"></script>
<script src="{{ asset('assets/moment/moment.js') }}"></script>
<script>
   Vue.filter('formatDate', function(value) {
    if (value) {
        return moment(String(value)).format('MMM D,yyyy');
    }
    });
    Vue.filter('formatMonth', function(value) {
        if (value) {
            return moment(String(value)).format('MMMM');
        }
    });
    Vue.filter('formatAmount', function(value) {
        if (value) {
            return new Intl.NumberFormat().format(value);
        }
    });
    let app = new Vue({
        el: '#app',
        data: {
            balance_bd: {!! json_encode($balance_bd) !!},
            sms_credit: {!! json_encode($data['sms_credit']) !!},
            transaction: {!! json_encode($data['transactions']) !!},
            monthly_sms: {!! json_encode($data['monthly_sms']) !!},
            last_week_sms: {!! json_encode($data['last_week_sms']) !!},
            last_week_cost: {!! json_encode($data['last_week_cost']) !!},
            last_month_sms: {!! json_encode($data['last_month_sms']) !!},
            last_month_cost: {!! json_encode($data['last_month_cost']) !!},
        },
        computed: {
            current_month: function(){
                var d = new Date();
                var month = new Array();
                month[0] = "January";
                month[1] = "February";
                month[2] = "March";
                month[3] = "April";
                month[4] = "May";
                month[5] = "June";
                month[6] = "July";
                month[7] = "August";
                month[8] = "September";
                month[9] = "October";
                month[10] = "November";
                month[11] = "December";
                var name = month[d.getMonth()];
                return name;
            }
        }
    })
</script>
@endsection
