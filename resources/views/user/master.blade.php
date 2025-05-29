<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="images/favicon.ico" type="image/ico" />


    <title>Bulk SMS</title>

    <!-- Bootstrap -->
    <link href="{{ asset('assets') }}/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('assets') }}/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{ asset('assets') }}/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- iCheck -->
    <link href="{{ asset('assets') }}/vendors/iCheck/skins/flat/green.css" rel="stylesheet">

    <!-- bootstrap-progressbar -->
    <link href="{{ asset('assets') }}/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
    <!-- JQVMap -->
    <link href="{{ asset('assets') }}/vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet"/>
    <!-- bootstrap-daterangepicker -->
    <link href="{{ asset('assets') }}/vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    @yield('custom_style')
    <!-- Custom Theme Style -->
    <link href="{{ asset('assets') }}/build/css/custom.min.css" rel="stylesheet">

    <link rel="icon" href="{{ OtherHelpers::website_logo() }}" type="/favicon.ico">
		<!--<link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap.min.css" />-->
		<link rel="stylesheet" href="{{ asset('assets') }}/font-awesome/4.5.0/css/font-awesome.min.css" />

		<!-- page specific plugin styles -->

		<!-- text fonts -->
		<link rel="stylesheet" href="{{ asset('assets') }}/css/fonts.googleapis.com.css" />

		<!-- ace styles -->
		<!--<link rel="stylesheet" href="{{ asset('assets') }}/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />-->

		<link rel="stylesheet" href="{{ asset('assets') }}/css/ace-skins.min.css" />
		<link rel="stylesheet" href="{{ asset('assets') }}/css/ace-rtl.min.css" />


	{{-- @yield('custom_style') --}}
		<link rel="stylesheet" href="{{ asset('assets') }}/css/loader.css" />
		<link rel="stylesheet" href="{{ asset('assets') }}/css/custom.css?v=1.0.1" />


		<!-- inline styles related to this page -->

		<!-- ace settings handler -->
		<!--<script src="{{ asset('assets') }}/js/ace-extra.min.js"></script>-->
  </head>

  <body class="nav-md">
    <div id="loading">
        <div id="loading-center">
            <div id="loading-center-absolute">
                <div class="object"></div>
                <div class="object"></div>
                <div class="object"></div>
                <div class="object"></div>
                <div class="object"></div>
                <div class="object"></div>
                <div class="object"></div>
                <div class="object"></div>
                <div class="object"></div>
                <div class="object"></div>
            </div>
        </div>
    </div>
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">

            @include('user.partials.sidebar')

          </div>
        </div>

        @include('user.partials.header')

        @yield('main_content')

        <!-- /page content -->

        <!-- footer content -->
        @include('user.partials.footer')
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets') }}/vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('assets') }}/vendors/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FastClick -->
    <script src="{{ asset('assets') }}/vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="{{ asset('assets') }}/vendors/nprogress/nprogress.js"></script>
    <!-- Chart.js -->
    <script src="{{ asset('assets') }}/vendors/Chart.js/dist/Chart.min.js"></script>
    <!-- gauge.js -->
    <script src="{{ asset('assets') }}/vendors/gauge.js/dist/gauge.min.js"></script>
    <!-- bootstrap-progressbar -->
    <script src="{{ asset('assets') }}/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <!-- iCheck -->
    <script src="{{ asset('assets') }}/vendors/iCheck/icheck.min.js"></script>
    <!-- Skycons -->
    <script src="{{ asset('assets') }}/vendors/skycons/skycons.js"></script>
    <!-- Flot -->
    {{-- <script src="{{ asset('assets') }}/vendors/Flot/jquery.flot.js"></script>
    <script src="{{ asset('assets') }}/vendors/Flot/jquery.flot.pie.js"></script>
    <script src="{{ asset('assets') }}/vendors/Flot/jquery.flot.time.js"></script>
    <script src="{{ asset('assets') }}/vendors/Flot/jquery.flot.stack.js"></script>
    <script src="{{ asset('assets') }}/vendors/Flot/jquery.flot.resize.js"></script> --}}
    <!-- Flot plugins -->
    {{-- <script src="{{ asset('assets') }}/vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
    <script src="{{ asset('assets') }}/vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
    <script src="{{ asset('assets') }}/vendors/flot.curvedlines/curvedLines.js"></script> --}}
    <!-- DateJS -->
    <script src="{{ asset('assets') }}/vendors/DateJS/build/date.js"></script>
    <!-- JQVMap -->
    <script src="{{ asset('assets') }}/vendors/jqvmap/dist/jquery.vmap.js"></script>
    <script src="{{ asset('assets') }}/vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
    <script src="{{ asset('assets') }}/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="{{ asset('assets') }}/vendors/moment/min/moment.min.js"></script>
    <script src="{{ asset('assets') }}/vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
    
    <!-- Custom Theme Scripts -->
    <script src="{{ asset('assets') }}/build/js/custom.min.js"></script>

    <!--<script src="{{ asset('assets') }}/js/jquery-2.1.4.min.js"></script>-->
	<!-- <![endif]-->

	<!--[if IE]>
<script src="assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->

	<script src="{{ asset('assets') }}/js/bootstrap.min.js"></script>

	<!-- page specific plugin scripts -->

	<!--[if lte IE 8]>
	  <script src="assets/js/excanvas.min.js"></script>
	<![endif]-->
	<script src="{{ asset('assets') }}/js/jquery-ui.custom.min.js"></script>
	<script src="{{ asset('assets') }}/js/jquery.ui.touch-punch.min.js"></script>
	<script src="{{ asset('assets') }}/js/jquery.easypiechart.min.js"></script>
	<script src="{{ asset('assets') }}/js/jquery.sparkline.index.min.js"></script>
	<script src="{{ asset('assets') }}/js/jquery.flot.min.js"></script>
	<script src="{{ asset('assets') }}/js/jquery.flot.pie.min.js"></script>
	<script src="{{ asset('assets') }}/js/jquery.flot.resize.min.js"></script>

	<!-- ace scripts -->
	<script src="{{ asset('assets') }}/js/ace-elements.min.js"></script>
	<script src="{{ asset('assets') }}/js/ace.min.js"></script>
    <script src="{{ asset('assets') }}/js/moment.min.js"></script>

    <script type="text/javascript">
        if('ontouchstart' in document.documentElement) document.write("<script src='{{ asset('assets') }}/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");

        $(document).ready(function () {

            let currentSecond = 0;
            let csrfToken = "{{ csrf_token() }}";
            let postUrl = "{{ route('update-login-status') }}";

            /*update active time first time visit into page*/
            $.ajax({
                type: "POST",
                url: postUrl,
                data: {_token: csrfToken},
                success: function (html) {
                }
            });
            setInterval(function () {
                currentSecond++;
                if (currentSecond <= 1201) {
                    if((currentSecond % 60) == 0) {
                        /*update active time every 1 minute max 20*/
                        $.ajax({
                            type: "POST",
                            url: postUrl,
                            data: {_token: csrfToken, currentSecond: currentSecond},
                            success: function (html) {
                            }
                        });
                    }
                }
            }, 1000);
        });
    </script>
@yield('custom_script')

  </body>
</html>


















