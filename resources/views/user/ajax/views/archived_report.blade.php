<h5>Campaign Title: {{ $campaign->sci_campaign_title }}</h5>
<table id="archived-sms-report-details" class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th>Sl</th>
        <th>Mobile</th>
        <th>Targeted time</th>
        <th>Sender ID</th>
        <th>Operator</th>
        <th class="hidden-480">Charge/SMS</th>
        <th class="hidden-480">SMS Text</th>
        <th class="hidden-480">Status Report</th>
    </tr>
    </thead>

    <tbody>
    @php($serial=1)
    @foreach($reports as $report)
        <tr>
            <td>{{ @$serial++ }}</td>
            <td>{{ @$report->sc_cell_no }}</td>
            <td>{{ @$report->sc_targeted_time->format('Y-M-d H:sa') }}</td>
            <td>{{ @$report->sender->sir_sender_id }}</td>
            <td>{{ @$report->operator->ope_operator_name }}</td>
            <td class="hidden-480">{{ @$report->sc_sms_cost }}</td>
            <td class="hidden-480"><pre style="width: 340px; line-height: 10px;">{!! nl2br(e(@$report->sc_message)) !!}</pre></td>
            @if(@$report->sc_delivery_report=="DELIVERED")
                <td class="hidden-480 text-success">{{ @$report->sc_delivery_report }}</td>
            @else
                <td class="text-danger hidden-480">{{ @$report->sc_delivery_report }}</td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>



<script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
<script type="text/javascript">
    $('#archived-sms-report-details').DataTable();
</script>



