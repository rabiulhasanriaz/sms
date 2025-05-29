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

    <title>{{ env('APP_NAME') }}</title>

    <!-- Bootstrap -->
    <link href="{{asset('assets/newTemp/vendors/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{asset('assets/newTemp/vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{asset('assets/newTemp/vendors/nprogress/nprogress.css')}}" rel="stylesheet">
    <!-- iCheck -->
    <link href="{{asset('assets/newTemp/vendors/iCheck/skins/flat/green.css')}}" rel="stylesheet">
	
    <!-- bootstrap-progressbar -->
    <link href="{{asset('assets/newTemp/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css')}}" rel="stylesheet">
    <!-- JQVMap -->
    <link href="{{asset('assets/newTemp/vendors/jqvmap/dist/jqvmap.min.css')}}" rel="stylesheet"/>
    <!-- bootstrap-daterangepicker -->
    <link href="{{asset('assets/newTemp/vendors/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet">

    <!-- Custom Theme Style -->
	<link href="{{asset('assets/newTemp/build/css/custom.min.css')}}" rel="stylesheet">
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        @include('admin.partials.sidebar')

        <!-- top navigation -->
        @include('admin.partials.header')
        <!-- /top navigation -->

        <!-- page content -->
        @yield('content')
        <!-- /page content -->

        <!-- footer content -->
        @include('admin.partials.footer')
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="{{asset('assets/newTemp/vendors/jquery/dist/jquery.min.js')}}"></script>
    <!-- Bootstrap -->
    <script src="{{asset('assets/newTemp/vendors/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
    <!-- FastClick -->
    <script src="{{asset('assets/newTemp/vendors/fastclick/lib/fastclick.js')}}"></script>
    <!-- NProgress -->
    <script src="{{asset('assets/newTemp/vendors/nprogress/nprogress.js')}}"></script>
    <!-- Chart.js -->
    <script src="{{('assets/newTemp/vendors/Chart.js/dist/Chart.min.js')}}"></script>
    <!-- gauge.js -->
    <script src="{{asset('assets/newTemp/vendors/gauge.js/dist/gauge.min.js')}}"></script>
    <!-- bootstrap-progressbar -->
    <script src="{{asset('assets/newTemp/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js')}}"></script>
    <!-- iCheck -->
    <script src="{{asset('assets/newTemp/vendors/iCheck/icheck.min.js')}}"></script>
    <!-- Skycons -->
    <script src="{{asset('assets/newTemp/vendors/skycons/skycons.js')}}"></script>
    <!-- Flot -->
    <script src="{{asset('assets/newTemp/vendors/Flot/jquery.flot.js')}}"></script>
    <script src="{{asset('assets/newTemp/vendors/Flot/jquery.flot.pie.js')}}"></script>
    <script src="{{asset('assets/newTemp/vendors/Flot/jquery.flot.time.js')}}"></script>
    <script src="{{asset('assets/newTemp/vendors/Flot/jquery.flot.stack.js')}}"></script>
    <script src="{{asset('assets/newTemp/vendors/Flot/jquery.flot.resize.js')}}"></script>
    <!-- Flot plugins -->
    <script src="{{asset('assets/newTemp/vendors/flot.orderbars/js/jquery.flot.orderBars.js')}}"></script>
    <script src="{{asset('assets/newTemp/vendors/flot-spline/js/jquery.flot.spline.min.js')}}"></script>
    <script src="{{asset('assets/newTemp/vendors/flot.curvedlines/curvedLines.js')}}"></script>
    <!-- DateJS -->
    <script src="{{asset('assets/newTemp/vendors/DateJS/build/date.js')}}"></script>
    <!-- JQVMap -->
    <script src="{{asset('assets/newTemp/vendors/jqvmap/dist/jquery.vmap.js')}}"></script>
    <script src="{{asset('assets/newTemp/vendors/jqvmap/dist/maps/jquery.vmap.world.js')}}"></script>
    <script src="{{asset('assets/newTemp/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js')}}"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="{{asset('assets/newTemp/vendors/moment/min/moment.min.js')}}"></script>
    <script src="{{asset('assets/newTemp/vendors/bootstrap-daterangepicker/daterangepicker.js')}}"></script>

    <!-- Custom Theme Scripts -->
    <script src="{{asset('assets/newTemp/build/js/custom.min.js')}}"></script>
	@yield('custom_style')
	@yield('custom_script')
	
  </body>
</html>
