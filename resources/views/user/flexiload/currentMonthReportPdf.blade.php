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
<h3 style="text-align: center;">Flexiload Bill Payment Details</h3>
<table>
    <thead>
    <tr>
        <th style="text-align: center;">Sl</th>
        <th style="text-align: center;">Date & Time</th>
        <th style="text-align: center;">User Name</th>
        <th style="text-align: center;">Number</th>
        <th style="text-align: center;">Transaction Id</th>
        <th style="text-align: center;">Remarks</th>
        <th style="text-align: center;">Amount</th>
    </tr>
    </thead>

    <tbody>
    @foreach( $allData as $data )
        <tr>
            <td style="text-align: center;">{{ $loop->iteration }}</td>
            <td style="text-align: center;">{{ $data->created_at }}</td>
            <td style="text-align: center;">{{ $data->owner_name }}</td>
            <td style="text-align: center;">{{ $data->targeted_number }}</td>
            <td style="text-align: center;">{{ $data->transaction_id }}</td>
            <td style="text-align: center;">{{ $data->remarks }}</td>
            <td style="text-align: right;">{{ $data->campaign_price }}</td>
        </tr>
    @endforeach
    </tbody>

    <tfoot>

    </tfoot>
</table>
</body>
</html>
