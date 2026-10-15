<?php
include_once "vendor/autoload.php";
use Carbon\Carbon;
use Philo\Blade\Blade;

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';
$blade = new Blade($views, $cache);
$user = new UserAccounts;
$images = new Images;
$capsule = $user->getCapsule();

if ($user->isAdmin()) {
    $data = [
        'type' => 'admin',
        'site_url' => Config::$site_url,
        'page_title' => "Signup Report",
        'logo_file' => $images->getScreenLogo(),
        'name' => 'Administrator',
        'myuser' => $user,
        'user_details' => []
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fromdate = $_POST['date-from'];
        $todate = $_POST['date-to'];
        
        $rawFrom = Carbon::createFromFormat('m/d/Y', $fromdate);
        $rawTo = Carbon::createFromFormat('m/d/Y', $todate);

        // Logic check: only query if To Date is NOT less than From Date
        if ($rawTo->gte($rawFrom)) {
            $from = $rawFrom->format('Y-m-d');
            $to = $rawTo->format('Y-m-d');

            $query = "SELECT 
                u.firstname, u.mobilephone, u.email, u.username as otp, u.creationdate as signup_date,
                a.framedipaddress, a.callingstationid as mac_address, a.acctstarttime, a.acctstoptime, 
                a.acctsessiontime, a.acctinputoctets, a.acctoutputoctets
              FROM userinfo u
              JOIN radacct a ON u.username = a.username  
              WHERE DATE(u.creationdate) >= '$from' 
              AND DATE(u.creationdate) <= '$to'
              AND u.creationby = 'Self-Signup'
              ORDER BY a.acctstarttime DESC"; 

            $data['user_details'] = $capsule::select($capsule::raw($query));
            $data['flag'] = 1; // Success flag
        } else {
            $data['user_details'] = [];
            $data['flag'] = 0; // Error flag
        }

        $data['dtfrom'] = $fromdate;
        $data['dtto'] = $todate;
    }

    echo $blade->view()->make('admin.signup-report', $data);
} else {
    header('Location: ' . Config::$site_url . 'logout.php');
}