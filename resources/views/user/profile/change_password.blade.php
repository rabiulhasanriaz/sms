@extends('user.master')
@section('main_content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Change Password</h3>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 bg-container offset-md-3">
                    @include('user.partials.all_error_messages')
                    @include('user.partials.session_messages')
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 col-sm-offset-4">
                        <h3 class="text-center text-primary"> Change password </h3>
        
                        <form action="{{ route('user.change-password') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="old">Old password :</label>
                                <input type="password" name="old_password" class="form-control" id="old"
                                       placeholder="Old Password" required="">
                            </div>
        
                            <div class="form-group">
                                <label for="new">New password :</label>
                                <input type="password" name="new_password" class="form-control" id="new"
                                       placeholder="New Password" required="">
                            </div>
        
                            <div class="form-group">
                                <label for="re">Re-password :</label>
                                <input type="password" name="re_password" class="form-control" id="re"
                                       placeholder="Re-Password">
                            </div>
        
                            <div class="form-group">
                                <input type="submit" class="btn btn-sm btn-primary" value="Change password">
                            </div>
                        </form>
                    </div>
                </div><!-- /.col -->
            </div>
        </div>
    </div>

@endsection



