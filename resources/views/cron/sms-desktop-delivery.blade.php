<!DOCTYPE html>
<html>
<head>
    <title>Delivery Report</title>
    <meta http-equiv="refresh" content="15">
</head>
<body>

<h2 style="text-align: center;">Fetch delivery report .... </h2>
<br>
    <ul>
        @foreach($returnData as $key=>$val)
            {{ $key }} => {{ $val }}<br><br>
        @endforeach
    </ul>
</body>
</html>
