<?php
// Include composer autoload and activity bootstrap
include_once "vendor/autoload.php";
include_once "App/admin_activity_bootstrap.php";

use Philo\Blade\Blade;

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';

$blade = new Blade($views, $cache);
$user = new UserAccounts;
$images = new Images;
$flash = new Flash_Messages();
$capsule = $user->getCapsule();

if (!$user->isAdmin()) {
    header('Location: ' . Config::$site_url . 'logout.php');
    exit;
}

$flash_msg = '';
if ($flash->hasFlashMessage()) {
    $flash_msg = $flash->show();
}

$msg = '';
$err = '';

// Handle POST request to update mode
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_mode = trim($_POST['signup_identifier_type'] ?? 'phone');

    if (in_array($selected_mode, ['phone', 'email'])) {
        $capsule::table('couponplans')
            ->where('is_guest_active', 1)
            ->update([
                'signup_identifier_type' => $selected_mode
            ]);

        $mode_label = ($selected_mode === 'email') ? 'Email Address' : 'Mobile Number';
        $flash->add("Guest Self-Signup mode has been successfully switched to {$mode_label}.");
        header('Location: ' . Config::$site_url . 'admin-toggle-login.php');
        exit;
    } else {
        $err = "Invalid signup mode selected. Please select either Mobile Number or Email Address.";
    }
}

// Fetch active guest plan configuration
$guest_plan = $capsule::table('couponplans')->where('is_guest_active', 1)->first();

$current_mode = 'phone';
if (!empty($guest_plan)) {
    if (is_array($guest_plan) && !empty($guest_plan['signup_identifier_type'])) {
        $current_mode = $guest_plan['signup_identifier_type'];
    } elseif (is_object($guest_plan) && !empty($guest_plan->signup_identifier_type)) {
        $current_mode = $guest_plan->signup_identifier_type;
    }
}

$data = [
    'type' => 'admin',
    'site_url' => Config::$site_url,
    'page_title' => "Toggle Login Mode",
    'logo_file' => $images->getScreenLogo(),
    'name' => 'Administrator',
    'myuser' => $user,
    'current_mode' => $current_mode,
    'guest_plan' => $guest_plan,
    'flash' => $flash_msg,
    'err' => $err,
    'msg' => $msg
];

echo $blade->view()->make('admin.toggle-login', $data);
