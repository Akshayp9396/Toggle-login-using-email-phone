<?php
include_once "App/admin_activity_bootstrap.php";
include_once "vendor/autoload.php";

$user = new UserAccounts;
$capsule = $user->getCapsule();

if ($user->isAdmin()) {
    $dtfrom = $_GET['date-from'];
    $dtto = $_GET['date-to'];

    $start = date('Y-m-d 00:00:00', strtotime($dtfrom));
    $end = date('Y-m-d 23:59:59', strtotime($dtto));

    // CHANGE: Use 'join' instead of 'leftJoin' to hide "Not Started" users
    // This ensures only users with usage records (radacct) appear in the CSV
    $reportData = $capsule::table('userinfo as u')
        ->join('radacct as a', 'u.username', '=', 'a.username') 
        ->select([
            'u.firstname', 'u.username as otp', 'u.mobilephone', 'u.email',
            'a.framedipaddress', 'a.callingstationid', 'a.acctstarttime', 
            'a.acctstoptime', 'a.acctsessiontime', 'a.acctinputoctets', 'a.acctoutputoctets'
        ])
        ->whereBetween('u.creationdate', [$start, $end])
        ->where('u.creationby', '=', 'Self-Signup') 
        ->orderBy('a.acctstarttime', 'desc') // Match the UI sorting
        ->get();

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Guest_Report_'.str_replace('/', '_', $dtfrom).'_to_'.str_replace('/', '_', $dtto).'.csv');

    $output = fopen('php://output', 'w');

    // Headers matching your UI exactly
    fputcsv($output, ['Name', 'OTP/Access Code', 'Mobile / Email', 'IP Address', 'MAC Address', 'Start Time', 'Stop Time', 'Total Time', 'Upload', 'Download']);

    foreach ($reportData as $row) {
        $row = (array)$row;
        $contact = !empty($row['email']) ? $row['email'] : (!empty($row['mobilephone']) ? $row['mobilephone'] : 'N/A');
        
        $formattedRow = [
            $row['firstname'],
            $row['otp'],
            $contact,
            $row['framedipaddress'],
            $row['callingstationid'],
            $row['acctstarttime'],
            ($row['acctstoptime']) ? $row['acctstoptime'] : 'Online',
            $user->time2str($row['acctsessiontime'] ?? 0),
            $user->formatSizeUnits($row['acctinputoctets'] ?? 0),
            $user->formatSizeUnits($row['acctoutputoctets'] ?? 0)
        ];
        
        fputcsv($output, $formattedRow);
    }
    
    fclose($output);
    exit;
}