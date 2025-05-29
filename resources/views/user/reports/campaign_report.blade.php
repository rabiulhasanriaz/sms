<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		table{
			border-collapse: collapse;
			width: 100%;
		}
		table, th, td{
			border: 1px solid lightgray;
		}
		th, td{
			padding: 3px;
		}
	</style>
</head>
<body>
	<div style="text-align: center;">
			<img src="{{ OtherHelpers::website_logo() }}" alt="">
	</div>
	<h3 style="text-align: center;">Sms Campaign Details</h3>
	<table>
		<thead>
			<tr>
				<th style="text-align: center;">Sl</th>
				<th style="text-align: center;">Cell No</th>
				<th style="text-align: center;">Message</th>
				<th style="text-align: center;">Cost</th>
				<th style="text-align: center;">Created At</th>
				<th style="text-align: center;">Delivery Report</th>
			</tr>
		</thead>

		<tbody>
			@php($total = 0)
			
			@foreach( $reports as $data )
				<tr>
					<td style="text-align: center;">{{ $loop->iteration }}</td>
					<td style="text-align: center;">{{ $data->sd_cell_no }}</td>
					<td style="text-align: center;">{{ $data->sd_message }}</td>
					<td style="text-align: center;">{{ $data->sd_sms_cost }}</td>
					<td style="text-align: center;">{{ $data->created_at }}</td>
					<td style="text-align: right;">{{ $data->sd_delivery_report }}</td>
				</tr>
				{{ $total = $total + $data->sd_sms_cost }}
			@endforeach
		</tbody>

		<tfoot>
			<tr>
				<td colspan="5" style="text-align: right;">Total:</td>
				<td style="text-align: right;">{{ number_format($total,2) }}</td>
			</tr>
			<tr>
				<td style="text-align: right;">In Word:</td>
				<td colspan="5" style="text-align: left;">{{ \OtherHelpers::number_to_text($total) }}</td>
			</tr>
		</tfoot>
	</table>
</body>
</html>
