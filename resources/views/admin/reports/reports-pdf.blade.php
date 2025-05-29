<style>
    #invoice{
    padding: 30px;
}

.invoice {
    position: relative;
    background-color: #FFF;
    min-height: 680px;
    padding: 15px
}

.invoice header {
    padding: 10px 0;
    margin-bottom: 20px;
    border-bottom: 1px solid #3989c6
}

.invoice .company-details {
    text-align: right
}

.invoice .company-details .name {
    margin-top: 0;
    margin-bottom: 0
}

.invoice .contacts {
    margin-bottom: 20px
}

.invoice .invoice-to {
    text-align: left
}

.invoice .invoice-to .to {
    margin-top: 0;
    margin-bottom: 0
}

.invoice .invoice-details {
    text-align: right
}

.invoice .invoice-details .invoice-id {
    margin-top: 0;
    color: #3989c6
}

.invoice main {
    padding-bottom: 50px
}

.invoice main .thanks {
    margin-top: -20px;
    font-size: 2em;
    margin-bottom: 50px
}

.invoice main .notices {
    padding-left: 6px;
    border-left: 6px solid #3989c6
}

.invoice main .notices .notice {
    font-size: 1.2em
}

.invoice table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
    margin-bottom: 20px
}

.invoice table td,.invoice table th {
    padding: 15px;
    background: #eee;
    border-bottom: 1px solid #fff
}

.invoice table th {
    white-space: nowrap;
    font-weight: 400;
    font-size: 16px
}

.invoice table td h3 {
    margin: 0;
    font-weight: 400;
    color: #3989c6;
    font-size: 1.2em
}

.invoice table .qty,.invoice table .total,.invoice table .unit {
    text-align: right;
    font-size: 1.2em
}

.invoice table .no {
    color: #fff;
    font-size: 1.6em;
    background: #3989c6
}

.invoice table .unit {
    background: #ddd
}

.invoice table .total {
    background: #3989c6;
    color: #fff
}

.invoice table tbody tr:last-child td {
    border: none
}

.invoice table tfoot td {
    background: 0 0;
    border-bottom: none;
    white-space: nowrap;
    text-align: right;
    padding: 10px 20px;
    font-size: 1.2em;
    border-top: 1px solid #aaa
}

.invoice table tfoot tr:first-child td {
    border-top: none
}

.invoice table tfoot tr:last-child td {
    color: #3989c6;
    
    border-top: 1px solid #3989c6
}

.invoice table tfoot tr td:first-child {
    border: none
}

.invoice footer {
    width: 100%;
    text-align: center;
    color: #777;
    border-top: 1px solid #aaa;
    padding: 8px 0
}

@media print {
    .invoice {
        font-size: 11px!important;
        overflow: hidden!important
    }

    .invoice footer {
        position: absolute;
        bottom: 10px;
        page-break-after: always
    }

    .invoice>div:last-child {
        page-break-before: always
    }
}
</style>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<!--Author      : @arboshiki-->
<div id="invoice">

    
    <div class="invoice overflow-auto">
        <div style="min-width: 600px">
            <header>
                <div class="row">
                    <div class="col">
                        FC SMS
                        {{-- <a target="_blank" href="https://lobianijs.com">
                            <img src="http://lobianijs.com/lobiadmin/version/1.0/ajax/img/logo/lobiadmin-logo-text-64.png" data-holder-rendered="true" />
                            </a> --}}
                    </div>
                    <div class="col company-details">
                        {{-- <h2 class="name">
                            <a target="_blank" href="https://lobianijs.com">
                            Arboshiki
                            </a>
                        </h2>
                        <div>455 Foggy Heights, AZ 85004, US</div>
                        <div>(123) 456-789</div> --}}
                        <div>Date: {{ Carbon\Carbon::now()->format('Y-m-d') }}</div>
                    </div>
                </div>
            </header>
            <main>
                <div class="row contacts">
                    <div class="col invoice-to">
                        <div class="text-gray-light">Hello Dear</div>
                        <div class="text-gray-light">Subject: Bill for SMS Credit.</div>
                        <div class="address">Sir, </div>
                        <div>Follow Itemize bill is submitted in below in details. </div>
                    </div>
                    <div class="col invoice-details">
                        <div class=""></div>
                    </div>
                </div>
                <table border="0" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-left">DESCRIPTION</th>
                            <th class="text-right">Quantity</th>
                            <th class="text-right">Euro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $total = 0;    
                        @endphp
                        @foreach ($userReports as $userReport)
                        <tr>
                            <td class="no">{{ $loop->iteration }}</td>
                            <td class="text-left">SMS Balance for the quantity of {{ $userReport->total }}<br>
                                Bill period {{ request()->start_date }} to {{ request()->end_date }}
                            </td>
                            <td class="unit">{{ $userReport->total }}</td>
                            <td class="qty">{{ $userReport->total_cost }}</td>
                        </tr> 
                        @php
                        $total = $total +  $userReport->total_cost;   
                        @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>

                            <td colspan="3">Total Project Cost</td>
                            <td class="text-right">{{ number_format($total,5) }}</td>
                        </tr>
                        <tr>
                            
                            <td colspan="3">SUB TOTAL</td>
                            <td class="text-right">{{ number_format($total,5) }}</td>
                        </tr>
                    </tfoot>
                </table>
                <div class="thanks"></div>
                <div class="" style="margin-top: 30px;">
                    <div>------------------------------</div>
                    <div class="">
                        <b>FC SMS</b><br>
                        <b>Md Moin Uddin</b><br>
                        CTO
                        E-mail: info@fcsms.in
                    </div>
                </div>
            </main>
            <footer style="margin-top: 330px;">
                Developed By FCSMS
            </footer>
        </div>
        <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
        <div></div>
    </div>
</div>