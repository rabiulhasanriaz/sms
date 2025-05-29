@extends('admin.master')

@section('reseller_menu_class','open')
@section('reseller_tree_menu_class', 'current-page')
@section('content')
<div class="right_col" role="main">
    <div class="">
        @include('admin.partials.session_messages')
        <div class="page-title">
            <div class="title_left">
                <h3>Reseller Registration</h3>
            </div>
    
            <div class="title_right">
                <div class="col-md-5 col-sm-5  form-group pull-right top_search">
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
        <div class="row">
            <div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Reseller Registration Form</small></h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a class="dropdown-item" href="#">Settings 1</a>
                                    </li>
                                    <li><a class="dropdown-item" href="#">Settings 2</a>
                                    </li>
                                </ul>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div id="test" class="tree">
                            <h3 class="text-center text-primary">Tree Of Customer</h3>
                            <hr>
                            <ul>
                                @foreach($roots as $root)
                                    <li class="parent_li">
                                        <span title="Root" style="background: green; color: #fff;">{{ @$root->userDetail->company_name }}</span>
                                        <ul>
            
                                            <!-- main -reseller -->
                                            @if($root->myUsers->count()>'0')
                                                @foreach($root->myUsers as $user1)
                                                    <li class="parent_li">
                                                        <span title="IGL Web Lmt"
                                                              style="color: green;font-weight: bold;">{{ @$user1->company_name }}</span>
                                                        <ul>
                                                            <!-- 2nd -reseller -->
                                                            @if($user1->myUsers->count()>'0')
                                                                @foreach($user1->myUsers as $user2)
                                                                    <li class="parent_li">
                                                                        <span title="IGL Web Lmt"
                                                                              style="color: #77017e;font-size: 12px; ">{{ @$user2->company_name }}</span>
                                                                        <ul>
                                                                            <!-- 3rd reseller-->
                                                                            @if($user2->myUsers->count()>'0')
                                                                                @foreach($user2->myUsers as $user3)
                                                                                    <li class="parent_li">
                                                                                <span title="IGL Web Lmt"
                                                                                      class="text-primary">{{ @$user3->company_name }}</span>
                                                                                <ul>
                                                                                    <!-- 4th reseller -->
                                                                                    @if($user3->myUsers->count()>'0')
                                                                                        @foreach($user3->myUsers as $user4)
                                                                                            <li class="parent_li">
                                                                                                <span title="IGL Web Lmt" class="text-primary">{{ @$user4->company_name }}</span>
                                                                                                <ul>
                                                                                                    <!-- 5th reseller -->
                                                                                                    @if($user4->myUsers->count()>'0')
                                                                                                        @foreach($user4->myUsers as $user5)
                                                                                                            <li class="parent_li">
                                                                                                                <span title="IGL Web Lmt" class="text-danger">{{ @$user5->company_name }}</span>
                                                                                                                <ul>
                                                                                                                    <!--last users-->
                                                                                                                    @if($user5->myUsers->count()>'0')
                                                                                                                        @foreach($user5->myUsers as $user6)
                                                                                                                    <li class="parent_li">
                                                                                                                        <span title="IGL Web Lmt" class="text-danger">{{ @$user6->company_name }}</span>
                                                                                                                    </li>
                                                                                                                        @endforeach
                                                                                                                    @endif
                                                                                                                </ul>
                                                                                                            </li>
                                                                                                        @endforeach
                                                                                                    @endif
                                                                                                </ul>
                                                                                            </li>
                                                                                        @endforeach
                                                                                    @endif
                                                                                </ul>
                                                                            </li>
                                                                                @endforeach
                                                                            @endif
                                                                        </ul>
                                                                    </li>
                                                                @endforeach
                                                            @endif
            
                                                        </ul>
                                                    </li>
                                            @endforeach
                                        @endif
                                        <!-- main -reseller end -->
            
                                        </ul>
                                    </li>
                                @endforeach
            
                            </ul>
            
            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
    </div>
    </div>
@endsection
