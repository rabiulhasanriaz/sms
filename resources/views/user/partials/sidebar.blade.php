@php
($permission = Auth::user()->permission)
@endphp
@if ($permission == 1))
    @php
    ($sms_permission = true)
    @endphp
@else
    @php
    ($sms_permission = false)
    @endphp
@endif



<div class="navbar nav_title" style="border: 0;">
    <a href="{{ route('user.index') }}" class="site_title"><i class="fa fa-paw"></i> <span>FC SMS</span></a>
</div>

<div class="clearfix"></div>

<!-- menu profile quick info -->
<div class="profile clearfix">
    <div class="profile_pic">
        <img src="{{ OtherHelpers::user_logo(Auth::user()->userDetail->logo)  }}" alt="..." class="img-circle profile_img">
    </div>
    <div class="profile_info">
        <span>Welcome,</span>
        <h2>{{ \Illuminate\Support\Facades\Auth::user()->userDetail['name'] }}</h2>
    </div>
</div>
<!-- /menu profile quick info -->

<br />

<!-- sidebar menu -->
<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
        <h3>EURO {{ BalanceHelper::user_available_balance(Auth::id()) }} <i class="fa fa-euro" style="font-size: 10px;"></i></h3>
        <ul class="nav side-menu">

            <li class="@yield('dashboard_menu_class')"><a href="{{ route('user.index') }}"><i class="fa fa-home"></i> Dashboard </a>
            </li>

            @if ($sms_permission)
            <li><a><i class="fa fa-home"></i> Phone Book <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                  <li><a href="{{ route('user.phonebook.index') }}">Contacts & Group</a></li>
                </ul>
            </li>
            @endif

            @if ($sms_permission)
            <li><a><i class="fa fa-home"></i> Messaging <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                <li><a href="{{ route('user.dynamic-sms.send') }}">Send</a></li>
                </ul>
            </li>

            <li><a><i class="fa fa-edit"></i> Price <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                <li><a href="{{  route('user.priceListDynamic') }}">Price</a></li>
                </ul>
            </li>

            <li><a><i class="fa fa-sitemap"></i> Reports <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a>View DLR<span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ route('user.dynamic-reports.todays-sms-dynamic') }}">Today's Report</a></li>
                            <li><a href="{{ route('user.dynamic-reports.dynamic-archived-sms') }}">Total Report </a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            @endif

            <li><a><i class="fa fa-edit"></i> API <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    @if ($sms_permission)
                        <li><a href="{{ route('user.developerApi') }}"> SMS</a></li>
                    @endif
                </ul>
            </li>
        </ul>
    </div>
</div>
  <!-- /sidebar menu -->

   <!-- /menu footer buttons -->
   <div class="sidebar-footer hidden-small">
    <a data-toggle="tooltip" data-placement="top" title="Settings">
      <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
    </a>
    <a data-toggle="tooltip" data-placement="top" title="FullScreen">
      <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
    </a>
    <a data-toggle="tooltip" data-placement="top" title="Lock">
      <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
    </a>
    <a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
      <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
    </a>
  </div>
  <!-- /menu footer buttons -->
