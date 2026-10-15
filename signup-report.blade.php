@extends('layout')

@section('content')

<script type="text/javascript">
function checkdt() {
    var from_dat = document.getElementById('fromdate2').value;
    var to_dat = document.getElementById('todate2').value;
    
    if (from_dat && to_dat) {
        var date1 = new Date(from_dat);
        var date2 = new Date(to_dat);

        if (date2 < date1) {
            alert("To date Should be greater than From date");
            document.getElementById('fromdate2').value = '';
            document.getElementById('fromdate2').focus();
            document.getElementById('todate2').value = '';
            return false;
        }
    }
    return true;
}
</script>


<style>
    /* Compact Table Styles */
    #dynamic-table thead th {
        vertical-align: middle;
        text-align: center;
        background-color: #f1f4f7;
        font-size: 12px;
        font-weight: 700;
        color: #2c3e50;
        padding: 10px 5px !important; 
    }
    #dynamic-table tbody td {
        vertical-align: middle;
        text-align: center;
        font-size: 12px;
        padding: 8px 5px !important;
    }
    .name-col { min-width: 90px; text-align: left !important; }
    .otp-col { min-width: 80px; font-weight: bold; }
    .ip-col { min-width: 100px; }
    .time-col { min-width: 80px; }
    .use-col { min-width: 80px; }
    .mob-col { min-width: 80px; }

</style>

<div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">Guest User Report</header>
            <div class="panel-body">
                <!-- <form method="POST" action=""> -->
                    <form method="POST" action="" onsubmit="return checkdt();">
                    <div class="row" style="display: flex; align-items: flex-end; padding-bottom: 20px; flex-wrap: wrap;">
                        <div class="col-md-7">
                            <span class="help-block">Select date range</span>
                            <div class="input-group input-large">
                                <!-- <input type="text" name="date-from" class="form-control dpd1" required placeholder="mm/dd/yyyy" value="{{$dtfrom ?? ''}}"> -->
                                 <input type="text" name="date-from" id="fromdate2" class="form-control dpd1" required placeholder="mm/dd/yyyy" value="{{$dtfrom ?? ''}}">
                                <span class="input-group-addon">To</span>
                                <!-- <input type="text" name="date-to" class="form-control dpd2" required placeholder="mm/dd/yyyy" value="{{$dtto ?? ''}}"> -->
                                 <input type="text" name="date-to" id="todate2" class="form-control dpd2" required placeholder="mm/dd/yyyy" value="{{$dtto ?? ''}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary" style="width: 120px; height: 34px; font-weight: bold;">Get Report</button>
                        </div>
                    </div>
                    @if(isset($flag) && $flag == 1)
    
    {{-- Export Button --}}
    @if(isset($dtfrom))
    <a href="{{ $site_url }}admin-export-signup-csv.php?date-from={{ urlencode($dtfrom) }}&date-to={{ urlencode($dtto) }}" 
       class="btn btn-warning" 
       style="background-color: #ffc107; border-color: #ffc107; color: #fff; height: 34px; line-height: 20px; font-weight: bold; margin-bottom: 20px;">
       <i class="fa fa-file-excel-o"></i> Export Csv
    </a>
    @endif

    {{-- Data Table --}}
    <div class="adv-table">
        <table class="display table table-bordered table-striped" id="dynamic-table" style="width:100% !important;">
            <thead>
                <tr>
                    <th class="name-col">Name</th>
                    <th class="otp-col">OTP/Access Code</th>
                    <th class="mob-col">Mobile / Email</th>
                    <th class="ip-col">IP Address</th>
                    <th>MAC Address</th>
                    <th class="time-col">Start Time</th>
                    <th class="time-col">Stop Time</th>
                    <th>Total Time</th>
                    <th class="use-col">Upload</th>
                    <th class="use-col">Download</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($user_details))
                    @foreach ($user_details as $details)
                    <tr>
                        <td class="name-col">{{ !empty($details['firstname']) ? $details['firstname'] : 'Guest' }}</td>
                        <td class="otp-col">{{$details['otp']}}</td>
                        <td>
                            @if(!empty($details['email']))
                                <i class="fa fa-envelope-o text-success"></i> {{$details['email']}}
                            @elseif(!empty($details['mobilephone']))
                                <i class="fa fa-mobile text-primary"></i> {{$details['mobilephone']}}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="ip-col">{{$details['framedipaddress'] ?? 'N/A'}}</td>
                        <td>{{$details['mac_address'] ?? 'N/A'}}</td>
                        <td class="time-col">{{$details['acctstarttime'] ?? 'Not Started'}}</td>
                        <td class="time-col">
                            @if($details['acctstoptime'])
                                {{$details['acctstoptime']}}
                            @elseif($details['acctstarttime'])
                                <span class="text-success" style="font-weight:bold;">Online</span>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{$myuser->time2str($details['acctsessiontime'] ?? 0)}}</td>
                        <td>{{$myuser->formatSizeUnits($details['acctinputoctets'] ?? 0)}}</td>
                        <td>{{$myuser->formatSizeUnits($details['acctoutputoctets'] ?? 0)}}</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
@endif
            </div>
        </section>
    </div>
</div>
@stop