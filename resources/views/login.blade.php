<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta charset="utf-8"/>
    <title>Bulk SMS Portal</title>
    <meta name="description" content="User login page"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="assets/css/fonts.googleapis.com.css"/>
    <link rel="stylesheet" href="assets/css/ace.min.css"/>
    <link rel="stylesheet" href="assets/css/ace-rtl.min.css"/>
    <!-- <link rel="stylesheet" href="./assets/css/customice.css"/> -->
    <link rel="icon" href="" type="/favicon.ico">
    <meta http-equiv="refresh" content="1200">
    <style>
        body {
    margin: 0;
    padding: 0;
    font-family: sans-serif;
    background: linear-gradient(to right, #b92b27, #1565c0)
}

.box {
    padding: 40px;
    background: #191919;
    text-align: center;
    transition: 0.25s;
    margin-top: 100px;
}

.box input[type="text"],
.box input[type="password"] {
    border: 0;
    background: none;
    display: block;
    margin: 20px auto;
    text-align: center;
    border: 2px solid #3498db;
    padding: 10px 10px;
    width: 250px;
    outline: none;
    color: white;
    border-radius: 24px;
    transition: 0.25s
}

.box h1 {
    color: white;
    text-transform: uppercase;
    font-weight: 500
}

.box input[type="text"]:focus,
.box input[type="password"]:focus {
    width: 300px;
    border-color: #2ecc71
}

.box input[type="submit"] {
    border: 0;
    background: none;
    display: block;
    margin: 20px auto;
    text-align: center;
    border: 2px solid #2ecc71;
    padding: 14px 40px;
    outline: none;
    color: white;
    border-radius: 24px;
    transition: 0.25s;
    cursor: pointer
}

.box input[type="submit"]:hover {
    background: #2ecc71
}

.forgot {
    text-decoration: underline
}

ul.social-network {
    list-style: none;
    display: inline;
    margin-left: 0 !important;
    padding: 0
}

ul.social-network li {
    display: inline;
    margin: 0 5px
}

.social-network a.icoFacebook:hover {
    background-color: #3B5998
}

.social-network a.icoTwitter:hover {
    background-color: #33ccff
}

.social-network a.icoGoogle:hover {
    background-color: #BD3518
}

.social-network a.icoFacebook:hover i,
.social-network a.icoTwitter:hover i,
.social-network a.icoGoogle:hover i {
    color: #fff
}

a.socialIcon:hover,
.socialHoverClass {
    color: #44BCDD
}

.social-circle li a {
    display: inline-block;
    position: relative;
    margin: 0 auto 0 auto;
    border-radius: 50%;
    text-align: center;
    width: 50px;
    height: 50px;
    font-size: 20px
}

.social-circle li i {
    margin: 0;
    line-height: 50px;
    text-align: center
}

.social-circle li a:hover i,
.triggeredHover {
    transform: rotate(360deg);
    transition: all 0.2s
}

.social-circle i {
    color: #fff;
    transition: all 0.8s;
    transition: all 0.8s
}
    </style>

</head>

<body class="login-layout">
    {{-- <div>
        <img src="/assets/uploads/login_bg.png" style="z-index: -999; position: absolute; width: 100%; height: 100%;">
    </div> --}}
<div class="main-container">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="card box">
                    @if(session()->has('message'))
                        <h5 class="header blue lighter bigger">
                            <p class="text-center text-danger">{{ session()->get('message') }}</p>
                        </h5>
                    <div class="space-6"></div>
                    @endif
                    <form action="{{ route('auth.login') }}" method="post">
                        @csrf
                        <h1>Login</h1>
                        <p class="text-muted"> Please enter your login Email/Phone and password!</p> 
                        <input type="text" name="email" placeholder="Email/Phone"> 
                        @if ($errors->has('email'))
                            <div class="text-danger">{{ $errors->first('email') }}</div>
                        @endif
                        <input type="password" name="password" placeholder="Password"> 
                        @if($errors->has('password'))
                            <div class="text-danger">{{ $errors->first('password') }}</div>
                        @endif
                        <input type="submit" name="" value="Login">
                        {{-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#forgot_password">
                            Forgot Password?
                        </button> --}}
                        <a class="forgot text-muted" data-toggle="modal" href="#forgot_password" style="color: #b92b27;">Forgot password?</a> 
                    </form>
                    
                </div>
            </div>
        </div>
    </div><!-- /.main-content -->
</div><!-- /.main-container -->

<div class="modal" id="forgot_password" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Forgot Password</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form action="{{ route('auth.forgot_password') }}" method="POST">
                @csrf
                <fieldset>
                    <label class="block clearfix">
                        <span class="block input-icon input-icon-right">
                            <input type="text" name="verification_number"
                                   class="form-control no-spin" placeholder="Phone"
                                   maxlength="13" required="" autofocus />
                            <i class="ace-icon fa fa-envelope"></i>
                        </span>
                    </label>

                    <div class="clearfix">
                        <input type="submit" value="Send Me!"
                               class="width-25 pull-right btn btn-xs btn-danger">
                    </div>
                </fieldset>
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

<!-- basic scripts -->

<!--[if !IE]> -->
<script src="assets/js/jquery-2.1.4.min.js"></script>
<script src="{{asset('assets/newTemp/vendors/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>


<!-- <![endif]-->

<!--[if IE]>
<script src="assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->
<script type="text/javascript">
    if ('ontouchstart' in document.documentElement) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
</script>

<!-- inline scripts related to this page -->
<script type="text/javascript">
    jQuery(function ($) {
        $(document).on('click', '.toolbar a[data-target]', function (e) {
            e.preventDefault();
            var target = $(this).data('target');
            $('.widget-box.visible').removeClass('visible');//hide others
            $(target).addClass('visible');//show target
        });
    });


    //you don't need this, just used for changing background
    jQuery(function ($) {
        $('#btn-login-dark').on('click', function (e) {
            $('body').attr('class', 'login-layout');
            $('#id-text2').attr('class', 'white');
            $('#id-company-text').attr('class', 'blue');

            e.preventDefault();
        });
        $('#btn-login-light').on('click', function (e) {
            $('body').attr('class', 'login-layout light-login');
            $('#id-text2').attr('class', 'grey');
            $('#id-company-text').attr('class', 'blue');

            e.preventDefault();
        });
        $('#btn-login-blur').on('click', function (e) {
            $('body').attr('class', 'login-layout blur-login');
            $('#id-text2').attr('class', 'white');
            $('#id-company-text').attr('class', 'light-blue');

            e.preventDefault();
        });

    });
</script>
</body>
</html>
