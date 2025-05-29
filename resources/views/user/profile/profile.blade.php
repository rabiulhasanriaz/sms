@extends('user.master')
@section('main_content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>User Profile</h3>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">
                    @include('user.partials.session_messages')
                    @include('user.partials.all_error_messages')
        
                    <form action="{{ route('user.profile') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div id="user-profile-1" class="user-profile row">
        
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <h3 class="text-center text-primary">Account Information</h3>
                                    <hr>
                                    <div class="col-xs-12 col-sm-4  col-md-4  col-lg-4 padding">
                                        <h4 class="text-primary">Credit
                                            : {{ number_format(BalanceHelper::user_total_credit(Auth::id()),5) }} <i class="fa fa-euro"></i></h4>
                                    </div>
                                    <div class="col-xs-12 col-sm-4  col-md-4  col-lg-4 padding">
                                        <h4 class="text-danger">Debit
                                            : {{ number_format(BalanceHelper::user_total_debit(Auth::id()),5) }} <i class="fa fa-euro"></i></h4>
                                    </div>
                                    <div class="col-xs-12 col-sm-4  col-md-4  col-lg-4 padding">
                                        <h4 class="text-success">Balance
                                            : {{ number_format(BalanceHelper::user_available_balance(Auth::id()),5) }} <i class="fa fa-euro"></i></h4>
                                    </div>
                                </div>
                                <br>
                                
                                <div class="col-md-3 offset-md-3">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Name</th>
                                            <td><input type="text" name="name" class="input-sm"
                                                id="inputsm" placeholder="Name"
                                                value="{{ Auth::user()->userDetail->name }}"
                                                style="height: 25px;" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Company Name</th>
                                            <td>
                                                <input type="text" name="company_name" class="input-sm"
                                                       id="inputsm" placeholder="company_name"
                                                       value="{{ Auth::user()->company_name }}"
                                                       style="height: 25px;" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ Auth::user()->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Mobile</th>
                                            <td>{{ Auth::user()->cellphone }}</td>
                                        </tr>
                                        <tr>
                                            <th>Location</th>
                                            <td>{{ Auth::user()->userDetail->address }}</td>
                                        </tr>
                                        <tr>
                                            <th>Joined</th>
                                            <td>{{ Auth::user()->created_at->format('Y-m-j h:i:s a') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Designation</th>
                                            <td><input type="text" value="{{ Auth::user()->userDetail->designation }}"
                                                name="designation" class="input-sm" id="inputsm"
                                                placeholder="Designation" style="height: 25px;" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Photo</th>
                                            <td><input type="file" name="profile_image"></td>
                                        </tr>
                                    </table>
                                </div>
                                
        
                                
                                <div class="space-20"></div>
                                <div class="col-md-12 text-center">
                                    <button class="btn btn-primary btn-sm">Update
                                    </button>
                                </div>
                            </div><!-- /.col -->
        
                        </div>
                    </form>
                </div><!-- /.col -->
            </div>
        </div>
    </div>

@endsection



