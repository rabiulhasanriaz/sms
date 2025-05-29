@extends('admin.master')
@section('pending_sms_menu_class','current-page')
@section('content')
<div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          <h3>SMS Pending Campaign</h3>
        </div>

        <div class="title_right">
          <div class="col-md-5 col-sm-5   form-group pull-right top_search">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Search for...">
              <span class="input-group-btn">
                <button class="btn btn-default" type="button">Go!</button>
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="clearfix"></div>

      <div class="row" style="display: block;">

        <div class="col-md-12 col-sm-12  ">
          <div class="x_panel">
            <div class="x_title">
              <h2>SMS Pending Campaign</h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                </li>
                <li><a class="close-link"><i class="fa fa-close"></i></a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>

            <div class="x_content">


              <div class="table-responsive">
                <table class="table table-striped jambo_table bulk_action">
                  <thead>
                    <tr class="headings">
                      
                      <th class="column-title">SL </th>
                      <th class="column-title">User </th>
                      <th class="column-title">Campaign Title </th>
                      <th class="column-title">Submit time </th>
                      <th class="column-title">Submitted </th>
                      <th class="column-title">Charge </th>
                      <th class="column-title">Content </th>
                      <th class="column-title no-link last"><span class="nobr">Action</span>
                      </th>
                      
                    </tr>
                  </thead>

                  <tbody>
                    @php($serial=1)
                    @foreach($pending_campaigns as $pending_campaign)
                    <tr class="even pointer">
                      <td class="a-center ">
                        {{ $serial++ }}
                      </td>
                      <td class=" ">{{ $pending_campaign->user->company_name }} / {{ $pending_campaign->user->cellphone }}</td>
                      <td title="{{ $pending_campaign->sci_campaign_id }}">{{ $pending_campaign->sdci_campaign_title }}</td>
                      <td class=" ">{{ $pending_campaign->sdci_targeted_time->format('h:i A, d-M-Y') }} <i class="success fa fa-long-arrow-up"></i></td>
                      
                      <td class=" ">{{ $pending_campaign->sdci_total_submitted }}</td>
                      <td class="a-right a-right ">BDT {{ number_format($pending_campaign->sdci_total_cost, 2) }}</td>
                      <td class=" last"><a href="#">
                        @if(count($pending_campaign->pendingSmsData) > 0)
                            <pre style="width:400px !important;">{!! $pending_campaign->pendingSmsData->first()->scp_message !!}</pre>
                        @endif
                      </a>
                      </td>
                      <td>
                        <a href="{!! route('admin.accept-campaign-sms', $pending_campaign->id) !!}" onclick="return confirm('Are you sure to accept this campaign?')" class="btn btn-xs btn-success">
                            <i class="fa fa-check"></i>
                        </a>
                        <a href="{!! route('admin.reject-campaign-sms', $pending_campaign->id) !!}" onclick="return confirm('Are you sure to reject this campaign?')" class="btn btn-xs btn-danger">
                            <i class="fa fa-trash"></i>
                        </a>
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
@endsection


@section('custom_script')
    <script type="text/javascript">
    </script>
@endsection












