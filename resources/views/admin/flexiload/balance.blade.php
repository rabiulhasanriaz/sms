@extends('admin.master')

@section('flexiload_menu_class','open')
@section('flexiload_balance_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li class="active">Flexiload Balance Enquiry</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Flexiload
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Available Balance
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>


    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            @include('admin.partials.session_messages')
            @include('admin.partials.all_error_messages')
        <div class="col-sm-6">
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;">Airtel</span>
                  <span class="badge badge-primary badge-pill">
                      @if ($latestbal->airtel == NULL)
                      <span style="font-size: 20px;">0.00</span>
                      @else
                      <span style="font-size: 20px;">{{ $latestbal->airtel }}</span>
                      @endif
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;">Banglalink</span>
                  <span class="badge badge-primary badge-pill">
                      @if ($latestbal->blink == NULL)
                      <span style="font-size: 20px;">0.00</span>
                      @else
                      <span style="font-size: 20px;">{{ $latestbal->blink }}</span>
                      @endif
                  </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span style="font-size: 20px;">GrameenPhone</span>
                  <span class="badge badge-primary badge-pill">
                      @if ($latestbal->gp == NULL)
                      <span style="font-size: 20px;">0.00</span>
                      @else
                      <span style="font-size: 20px;">{{ $latestbal->gp }}</span>
                      @endif
                  </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;">Robi</span>
                    <span class="badge badge-primary badge-pill">
                        @if ($latestbal->robi == NULL)
                        <span style="font-size: 20px;">0.00</span>
                        @else
                        <span style="font-size: 20px;">{{ $latestbal->robi }}</span>
                        @endif
                    </span>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;">Teletalk</span>
                    <span class="badge badge-primary badge-pill">
                        @if ($latestbal->teletalk == NULL)
                        <span style="font-size: 20px;">0.00</span>
                        @else
                        <span style="font-size: 20px;">{{ $latestbal->teletalk }}</span>
                        @endif
                    </span>
                  </li>
                  @php
                      $total = $latestbal->airtel + $latestbal->gp + $latestbal->blink + $latestbal->robi + $latestbal->teletalk;
                  @endphp
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;" class="text-danger">Total</span>
                    <span class="badge badge-danger badge-pill">
                        <span style="font-size: 20px;">{{ $total }}</span>
                    </span>
                  </li>
              </ul>
        </div>

        <div class="col-sm-6">
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px; margin-left: 100px;" class="text-warning">Pending Flexiload Balance</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;">Prepaid</span>
                  <span class="badge badge-primary badge-pill">
                      @if ($pending_bal_pre == 0)
                      <span style="font-size: 20px;">0.00</span>
                      @else
                      <span style="font-size: 20px;">{{ $pending_bal_pre }}</span>
                      @endif
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;">Postpaid</span>
                  <span class="badge badge-primary badge-pill">
                      @if ($pending_bal_post == 0)
                      <span style="font-size: 20px;">0.00</span>
                      @else
                      <span style="font-size: 20px;">{{ $pending_bal_post }}</span>
                      @endif
                  </span>
                </li>
                  @php
                      $total = $pending_bal_pre + $pending_bal_post;
                  @endphp
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span style="font-size: 20px;" class="text-danger">Total</span>
                    <span class="badge badge-danger badge-pill">
                        <span style="font-size: 20px;">{{ number_format($total,2) }}</span>
                    </span>
                  </li>
              </ul>
        </div>

        </div><!-- /.col -->
    </div><!-- /.row -->


@endsection