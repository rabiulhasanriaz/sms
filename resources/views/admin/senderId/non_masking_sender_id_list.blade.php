@extends('admin.master')

@section('non_masking_menu_class', 'active')
@section('page_location')
<ul class="breadcrumb">
	<li>
		<i class="ace-icon fa fa-home home-icon"></i>
		<a href="{{ route('admin.index') }}">Dashboard</a>
	</li>
	<li>
		<a href="{{ route('admin.senderID.index') }}">Sender ID</a>
	</li>
	<li class="active">Non Masking</li>
</ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
<h1>
	Non Masking Sender ID
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		 Add
	</small>
</h1>
@endsection

@section('main_content')

<div class="space-6"></div>


<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">

            @if(session()->has('message'))
                <span class="text-{{ session()->get('type') }}">{{ session()->get('message') }}</span>
            @endif

            @if($errors->any())
                <ul>
                    @foreach($errors->all() as $error)
                        <li class="text-danger">{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

			<form action="{{ route('admin.senderID.nonMaskingSenderID.store') }}" method="post" class="form-horizontal" role="form">
                @csrf
				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 col-lg-offset-2 col-md-offset-2">
				<!-- PAGE CONTENT BEGINS -->
					<div class="form-group">
						<label for="form-field-select-3"> Non-masking </label>
						<input type="text" name="nonmasking" class="form-control" value="">
					</div>
					
					<div class="clearfix form-group">
						<input type="submit" class="btn btn-info" value="Submit">
						&nbsp; &nbsp; &nbsp;
						<button class="btn btn-danger" type="reset">
							<i class="ace-icon fa fa-undo bigger-110"></i>
							Reset
						</button>							
					</div>
				</div>
			 </form>
		</div>
	</div><!-- /.col -->




	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background: #f8f8f8;"><hr>
			<h3> Non-Masking View </h3>
			<table id="dynamic-table" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th>SL</th>
						<th>Sender ID</th>
						<th>Date </th>
						<th>System</th>
					</tr>
				</thead>

				<tbody>
				@php($serial=1)
				@foreach($nonMaskingSenderIds as $nonMaskingSenderId)
					<tr>
				    	<td>{{ $serial++ }}</td>
				    	<td>{{ $nonMaskingSenderId->number }}</td>
				    	<td>{{ $nonMaskingSenderId->created_at->format('j-M-Y') }}</td>
				    	<td>
				    		<a href="{{ route('admin.senderID.nonMaskingSenderID.edit', $nonMaskingSenderId->id) }}" class="btn-none-edit">Edit</a> |
							<a href="{{ route('admin.senderID.nonMaskingSenderID.delete', [$nonMaskingSenderId->id]) }}" class="btn-none-delete" onclick="return confirm('Are you sure you want to delete ?');">Delete</a>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
		<!-- PAGE CONTENT ENDS -->
	</div><!-- /.col -->
</div><!-- /.row -->


@endsection


