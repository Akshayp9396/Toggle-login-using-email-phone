<?php
date_default_timezone_set('Asia/Kolkata');


#
# chilli - ChilliSpot.org. A Wireless LAN Access Point Controller
# Copyright (C) 2003, 2004 Mondru AB.
#
# The contents of this file may be used under the terms of the GNU
# General Public License Version 2, provided that the above copyright
# notice and this permission notice is included in all copies or
# substantial portions of the software.

# Redirects from ChilliSpot daemon:
#
# Redirection when not yet or already authenticated
#   notyet:  ChilliSpot daemon redirects to login page.
#  already: ChilliSpot daemon redirects to success status page.
#
# Response to login:
#   already: Attempt to login when already logged in.
#   failed:  Login failed
#   success: Login succeded
#
# logoff:  Response to a logout
#
#/*
# *********************************************************************************************************
# *
# * Authors:     Liran Tal <liran@enginx.com>
# *
# * daloRADIUS edition - fixed up variable definition through-out the code
# * as well as parted the code for the sake of modularity and ability to
# * to support templates and languages easier.
# * Copyright (C) Enginx and Liran Tal 2007, 2008
# *
# *********************************************************************************************************
# */

# Shared secret used to encrypt challenge with. Prevents dictionary attacks.
# You should change this to your own shared secret.
$uamsecret = "testing123";

# Uncomment the following line if you want to use ordinary user-password
# for radius authentication. Must be used together with $uamsecret.
# pap or mschapv2
#$userpassword="pap";
#$userpassword=" ";

# Define your template here
$template_name = "default";
$language = "en";


# Our own path
$loginpath = $_SERVER['PHP_SELF'];

if (empty($template_name))
    $template_name = "default";
if (file_exists("template/" . $template_name)) {
    $template = $template_name;
} else {
    $template = "default";
}

include('util/password.php');




if (isset($_POST['Password']))
    $password = trim($_POST['Password']);
elseif (isset($_GET['Password']))
    $password = trim($_GET['Password']);
else
    $password = "";

if (isset($_POST['challenge']))
    $challenge = $_POST['challenge'];
elseif (isset($_GET['challenge']))
    $challenge = $_GET['challenge'];
else
    $challenge = "";


if (isset($_POST['button']))
    $button = $_POST['button'];
elseif (isset($_GET['button']))
    $button = $_GET['button'];
else
    $button = "";


if (isset($_POST['logout']))
    $logout = $_POST['logout'];
elseif (isset($_GET['logout']))
    $logout = $_GET['logout'];
else
    $logout = "";


if (isset($_POST['prelogin']))
    $prelogin = $_POST['prelogin'];
elseif (isset($_GET['prelogin']))
    $prelogin = $_GET['prelogin'];
else
    $prelogin = "";


if (isset($_POST['res']))
    $res = $_POST['res'];
elseif (isset($_GET['res']))
    $res = $_GET['res'];
else
    $res = "";


if (isset($_POST['uamip']))
    $uamip = $_POST['uamip'];
elseif (isset($_GET['uamip']))
    $uamip = $_GET['uamip'];
else
    $uamip = "";


if (isset($_POST['uamport']))
    $uamport = $_POST['uamport'];
elseif (isset($_GET['uamport']))
    $uamport = $_GET['uamport'];
else
    $uamport = "";


if (isset($_POST['userurl']))
    $userurl = $_POST['userurl'];
elseif (isset($_GET['userurl']))
    $userurl = $_GET['userurl'];
else
    $userurl = "";


if (isset($_POST['timeleft']))
    $timeleft = $_POST['timeleft'];
elseif (isset($_GET['timeleft']))
    $timeleft = $_GET['timeleft'];
else
    $timeleft = "";


if (isset($_POST['redirurl']))
    $redirurl = $_POST['redirurl'];
elseif (isset($_GET['redirurl']))
    $redirurl = $_GET['redirurl'];
else
    $redirurl = "";

if (isset($_POST['login-type']))
    $logintype = $_POST['login-type'];
elseif (isset($_GET['login-type']))
    $logintype = $_GET['login-type'];


if (isset($_POST['login-type'])) {
    $logintype = $_POST['login-type'];
} elseif (isset($_GET['login-type'])) {
    $logintype = $_GET['login-type'];
}

if (isset($logintype) && $logintype == 'coupon') {
    $username = (isset($_POST['couponCode'])) ? trim($_POST['couponCode']) : (isset($_GET['couponCode']) ? trim($_GET['couponCode']) : "");

    $password = $username;
} else {
    $username = (isset($_POST['UserName'])) ? trim($_POST['UserName']) : (isset($_GET['UserName']) ? trim($_GET['UserName']) : "");
}


if (isset($_POST['sbt-signup'])) {

    $conn = new mysqli("localhost", "root", "sqlpass", "raddb");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch Plan Name, Reset Hours, and Signup Mode (phone or email)
    $planQuery = "SELECT planname, reset_hours, signup_identifier_type FROM couponplans WHERE is_guest_active = 1 LIMIT 1";
    $planResult = $conn->query($planQuery);

    if ($planResult && $planResult->num_rows > 0) {
        $row = $planResult->fetch_assoc();
        $commonPlan = $row['planname'];
        $resetGap = $row['reset_hours'];
        $signupMode = !empty($row['signup_identifier_type']) ? $row['signup_identifier_type'] : 'phone';
    } else {
        $conn->close();
        header("Location: " . $loginpath . "?res=failed&reason=no_active_plan&uamip=$uamip&uamport=$uamport");
        exit;
    }

    $name = trim($_POST['signup_name'] ?? '');

    // validation for name
    if (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
        $conn->close();
        header("Location: " . $loginpath . "?res=failed&reason=invalid_name&uamip=$uamip&uamport=$uamport");
        exit;
    }

    if ($signupMode === 'email') {
        $email = trim($_POST['signup_email'] ?? '');

        // validation for email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $conn->close();
            header("Location: " . $loginpath . "?res=failed&reason=invalid_email&uamip=$uamip&uamport=$uamport");
            exit;
        }

        // limit checks for email
        $checkQuery = "SELECT a.acctstarttime
                       FROM userinfo u
                       JOIN radacct a ON u.username = a.username
                       WHERE u.email = '" . mysqli_real_escape_string($conn, $email) . "' 
                       ORDER BY a.acctstarttime DESC LIMIT 1";

        $checkResult = $conn->query($checkQuery);

        if ($checkResult && $checkResult->num_rows > 0) {
            $row = $checkResult->fetch_assoc();

            // Time Calculations
            $lastLoginTS = strtotime($row['acctstarttime']);
            $currentTimeTS = time();
            $elapsedHours = ($currentTimeTS - $lastLoginTS) / 3600;
            $isSameDay = (date('Y-m-d', $lastLoginTS) == date('Y-m-d', $currentTimeTS));

            // Check if the last login was TODAY
            if ($isSameDay) {
                // Same day reset gap check
                if ($elapsedHours < $resetGap) {
                    $conn->close();
                    header("Location: " . $loginpath . "?res=failed&reason=limit_reached&type=reset_gap&uamip=$uamip&uamport=$uamport");
                    exit;
                }
            }
        }

        // --- OTP CREATION & REUSE LOGIC (EMAIL) ---
        $otp = "";
        $isNewOtp = true;

        // Check OTP created within the last 10 minutes
        $stmtReuse = $conn->prepare("
            SELECT c.username, UNIX_TIMESTAMP(c.creation_time) as created_at 
            FROM userinfo u 
            JOIN radcheck c ON u.username = c.username 
            WHERE u.email = ? AND u.creationby = 'Self-Signup' 
            ORDER BY c.creation_time DESC LIMIT 1
        ");
        $stmtReuse->bind_param("s", $email);
        $stmtReuse->execute();
        $reuseResult = $stmtReuse->get_result();

        if ($reuseRow = $reuseResult->fetch_assoc()) {
            // We reuse the OTP if it hasn't expired yet 
            if ((time() - $reuseRow['created_at']) <= 600) {
                $otp = $reuseRow['username'];
                $isNewOtp = false;
            }
        }

        if ($isNewOtp) {
            $otp = rand(100000, 999999);

            // Clear ONLY the login tables
            $conn->query("DELETE FROM radcheck WHERE username = '$otp'");
            $conn->query("DELETE FROM radusergroup WHERE username = '$otp'");

            $stmtAuth = $conn->prepare("INSERT INTO radcheck (username, attribute, op, value, creation_time) VALUES (?, 'Auth-Type', ':=', 'Accept', NOW())");
            $stmtAuth->bind_param("s", $otp);
            $stmtAuth->execute();

            $stmt2 = $conn->prepare("INSERT INTO radusergroup (username, groupname, priority) VALUES (?, ?, 0)");
            $stmt2->bind_param("ss", $otp, $commonPlan);
            if (!$stmt2->execute()) {
                error_log("PLAN ATTACH FAIL (radusergroup): " . $stmt2->error);
            }

            $creationSource = "Self-Signup";
            $stmt3 = $conn->prepare("INSERT INTO userinfo (username, firstname, email, creationdate, creationby) VALUES (?, ?, ?, NOW(), ?)");
            $stmt3->bind_param("ssss", $otp, $name, $email, $creationSource);
            $stmt3->execute();
        }

        // Email API / delivery placeholder (can be configured in future)
        $mailSubject = "Your WiFi Access Code";
        $mailBody = "Welcome to WiFi Hotspot!\n\nYour Access Code is: " . $otp . "\n\nPlease enter this code on the login page to access the internet.";
        $headers = "From: no-reply@" . ($_SERVER['SERVER_NAME'] ?? 'wifi-hotspot.local') . "\r\n";
        @mail($email, $mailSubject, $mailBody, $headers);

    } else {
        // Mode is 'phone'
        $mobile = trim($_POST['signup_mobile'] ?? '');

        // validation for mobile number
        if (!preg_match('/^[0-9]{10}$/', $mobile)) {
            $conn->close();
            header("Location: " . $loginpath . "?res=failed&reason=invalid_mobile&uamip=$uamip&uamport=$uamport");
            exit;
        }

        // limit checks for mobile
        $checkQuery = "SELECT a.acctstarttime
                       FROM userinfo u
                       JOIN radacct a ON u.username = a.username
                       WHERE u.mobilephone = '" . mysqli_real_escape_string($conn, $mobile) . "' 
                       ORDER BY a.acctstarttime DESC LIMIT 1";

        $checkResult = $conn->query($checkQuery);

        if ($checkResult && $checkResult->num_rows > 0) {
            $row = $checkResult->fetch_assoc();

            // Time Calculations
            $lastLoginTS = strtotime($row['acctstarttime']);
            $currentTimeTS = time();
            $elapsedHours = ($currentTimeTS - $lastLoginTS) / 3600;
            $isSameDay = (date('Y-m-d', $lastLoginTS) == date('Y-m-d', $currentTimeTS));

            // Check if the last login was TODAY
            if ($isSameDay) {
                // Same day reset gap check
                if ($elapsedHours < $resetGap) {
                    $conn->close();
                    header("Location: " . $loginpath . "?res=failed&reason=limit_reached&type=reset_gap&uamip=$uamip&uamport=$uamport");
                    exit;
                }
            }
        }

        // --- OTP CREATION & REUSE LOGIC (MOBILE) ---
        $otp = "";
        $isNewOtp = true;

        // Check OTP created within the last 10 minutes
        $stmtReuse = $conn->prepare("
            SELECT c.username, UNIX_TIMESTAMP(c.creation_time) as created_at 
            FROM userinfo u 
            JOIN radcheck c ON u.username = c.username 
            WHERE u.mobilephone = ? AND u.creationby = 'Self-Signup' 
            ORDER BY c.creation_time DESC LIMIT 1
        ");
        $stmtReuse->bind_param("s", $mobile);
        $stmtReuse->execute();
        $reuseResult = $stmtReuse->get_result();

        if ($reuseRow = $reuseResult->fetch_assoc()) {
            // We reuse the OTP if it hasn't expired yet 
            if ((time() - $reuseRow['created_at']) <= 600) {
                $otp = $reuseRow['username'];
                $isNewOtp = false;
            }
        }

        if ($isNewOtp) {
            $otp = rand(100000, 999999);

            // Clear ONLY the login tables
            $conn->query("DELETE FROM radcheck WHERE username = '$otp'");
            $conn->query("DELETE FROM radusergroup WHERE username = '$otp'");

            $stmtAuth = $conn->prepare("INSERT INTO radcheck (username, attribute, op, value, creation_time) VALUES (?, 'Auth-Type', ':=', 'Accept', NOW())");
            $stmtAuth->bind_param("s", $otp);
            $stmtAuth->execute();

            $stmt2 = $conn->prepare("INSERT INTO radusergroup (username, groupname, priority) VALUES (?, ?, 0)");
            $stmt2->bind_param("ss", $otp, $commonPlan);
            if (!$stmt2->execute()) {
                error_log("PLAN ATTACH FAIL (radusergroup): " . $stmt2->error);
            }

            $creationSource = "Self-Signup";
            $stmt3 = $conn->prepare("INSERT INTO userinfo (username, firstname, mobilephone, creationdate, creationby) VALUES (?, ?, ?, NOW(), ?)");
            $stmt3->bind_param("ssss", $otp, $name, $mobile, $creationSource);
            $stmt3->execute();
        }

        // SMS API placeholder
        $smsMsg = urlencode("Your WiFi Access Code is: " . $otp);
        $smsUrl = "https://your-sms-provider.com/api/send?number=$mobile&msg=$smsMsg";
        @file_get_contents($smsUrl);
    }

    $conn->close();

    header("Location: " . $loginpath . "?res=notyet&login-type=coupon&signup=new" .
        "&uamip=$uamip&uamport=$uamport&challenge=$challenge&userurl=" . urlencode($userurl));
    exit;
}




if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $ajaxConn = new mysqli("localhost", "root", "sqlpass", "raddb");
    if ($ajaxConn->connect_error) {
        die(json_encode(['success' => false]));
    }

    // Resend Password (IP Login / UHID)
    if ($_GET['action'] == 'resend_password') {
        $uhid = mysqli_real_escape_string($ajaxConn, $_GET['uhid']);

        // Find the most recent record for this UHID
        $query = "SELECT username, mobilephone FROM userinfo WHERE username = '$uhid' OR uhid = '$uhid' ORDER BY creationdate DESC LIMIT 1";
        $result = $ajaxConn->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $otp = $row['username'];
            $mobile = $row['mobilephone'];


            $smsMsg = urlencode("Your Access Code is: " . $otp);
            $externalApi = "https://ovalinfosec.com/pmsapi/public/index.php/api/resendPassword";

            // You can use cURL or file_get_contents here to notify your external system
            // @file_get_contents($externalApi . "?mobile=$mobile&msg=$smsMsg"); 

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
    }


    $ajaxConn->close();
    exit;
}

include('lang/main.php');

/* if SSL was not used show an error */
if (!($_SERVER['HTTPS'] == 'on')) {
    include('hotspotlogin-nonssl.php');
    exit(0);
}



// --- START of 10-MINUTE EXPIRY CHECK ---
if ($button == 'Login' && !empty($username)) {
    $conn = new mysqli("localhost", "root", "sqlpass", "raddb");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    // 1. Check if this OTP has ALREADY logged in today
    $stmtAcc = $conn->prepare("SELECT COUNT(*) as logins FROM radacct WHERE username = ? AND acctstarttime >= CURDATE()");
    $stmtAcc->bind_param("s", $username);
    $stmtAcc->execute();
    $accRow = $stmtAcc->get_result()->fetch_assoc();
    $alreadyLoggedIn = ($accRow['logins'] > 0);

    // 2. Check creation time for Self-Signup users
    $stmt = $conn->prepare("
        SELECT TIMESTAMPDIFF(SECOND, c.creation_time, NOW()) as elapsed_sec 
        FROM radcheck c
        JOIN userinfo u ON c.username = u.username
        WHERE c.username = ? AND u.creationby = 'Self-Signup'
        LIMIT 1
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $elapsedSec = $row['elapsed_sec'];

        // Only enforce 10-min limit if they HAVE NOT logged in yet
        if (!$alreadyLoggedIn && $elapsedSec > 600) {
            $conn->close();
            header("Location: " . $loginpath . "?res=failed&reason=otp_expired&uamip=$uamip&uamport=$uamport");
            exit;
        }
    }
    $conn->close();
}
// --- END of 10-MINUTE EXPIRY CHECK ---




(isset($_GET['reply'])) ? $reply = $_GET['reply'] : $reply = "";

$userurldecode = $userurl;
$redirurldecode = $redirurl;

# If attempt to login
if ($button == 'Login') {
    $hexchal = pack("H32", $challenge);
    if ($uamsecret) {
        $newchal = pack("H*", md5($hexchal . $uamsecret));
    } else {
        $newchal = $hexchal;
    }
    $newpwd = pack("a32", $password);

    $response = md5("\0" . $password . $newchal);
    $pappassword = implode("", unpack("H32", ($newpwd ^ $newchal)));


    //$pappassword = getEncryptedPasswordPAP($password, $uamsecret, $challenge);
    //$response = getEncryptedPasswordOther($password, $uamsecret, $challenge);

    echo "<!doctype html>
    <html lang=\"en\">
    <head>
    <!-- Required meta tags -->
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">
    
  <title>$title</title>
  <meta http-equiv=\"Cache-control\" content=\"no-cache\">
  <meta http-equiv=\"Pragma\" content=\"no-cache\">
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
  <link rel=\"stylesheet\" href=\"css/bootstrap.min.css\">"
    ;

    if (file_exists("template/" . $template . "/css/style.css")) {
        echo "<link href=\"template/" . $template . "/css/style.css\" rel=\"stylesheet\" type=\"text/css\" />";
    }

    if (isset($uamsecret) && isset($userpassword) && $userpassword == "pap") {
        $login_url = "http://$uamip:$uamport/login?username=$username&password=$pappassword";
    } else {
        $login_url = "http://$uamip:$uamport/login?username=$username&response=$response&userurl=$userurl";
    }



    echo "  <meta http-equiv=\"refresh\" content=\"0;url=$login_url\">";

    echo "</head>";
    echo "<body>";

    include('template/' . $template . '/loggingin.php');

    echo "</body>";
    echo "</html>";

    echo "
<!--
<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<WISPAccessGatewayParam 
  xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
  xsi:noNamespaceSchemaLocation=\"http://www.acmewisp.com/WISPAccessGatewayParam.xsd\">
<AuthenticationReply>
<MessageType>120</MessageType>
<ResponseCode>201</ResponseCode>
";
    if (isset($uamsecret) && isset($userpassword) && $userpassword == "pap") {
        echo "<LoginResultsURL>http://$uamip:$uamport/login?username=$username&password=$pappassword</LoginResultsURL>";
    } else {
        echo "<LoginResultsURL>http://$uamip:$uamport/login?username=$username&response=$response&userurl=$userurl</LoginResultsURL>";
    }
    echo "</AuthenticationReply> 
</WISPAccessGatewayParam>
-->
";

    exit(0);
}

switch ($res) {
    case 'success':
        $result = 1;
        break; // If login successful
    case 'failed':
        $result = 2;
        break; // If login failed
    case 'logoff':
        $result = 3;
        break; // If logout successful
    case 'already':
        $result = 4;
        break; // If tried to login while already logged in
    case 'notyet':
        $result = 5;
        break; // If not logged in yet
    case 'smartclient':
        $result = 6;
        break; // If login from smart client
    case 'popup1':
        $result = 11;
        break; // If requested a logging in pop up window
    case 'popup2':
        $result = 12;
        break; // If requested a success pop up window
    case 'popup3':
        $result = 13;
        break; // If requested a logout pop up window
    default:
        $result = 0; // Default: It was not a form request
}

# HTML5 doctype
echo "<!doctype html>
<html lang=\"en\">
<head>
    <!-- Required meta tags -->
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">


  <title>$title</title>
  <meta http-equiv=\"Cache-control\" content=\"no-cache\">
  <meta http-equiv=\"Pragma\" content=\"no-cache\">
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
  <link rel=\"stylesheet\" href=\"css/bootstrap.min.css\" integrity=\"sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm\" crossorigin=\"anonymous\">
  
  ";

if (file_exists("template/" . $template . "/css/style.css")) {
    echo "<link href=\"template/" . $template . "/css/style.css\" rel=\"stylesheet\" type=\"text/css\" />";
}

if (file_exists("template/" . $template . "/js/style.js")) {
    echo "<script src=\"template/" . $template . "/js/style.js\" ></script>";
}

echo "<script language=\"javascript\">";
include('js/hotspotlogin.js');
echo "</script>";

echo "</head>";
echo "<body onBlur = 'javascript:doOnBlur($result)' style=\"background: url('backdrop.jpg');background-size: 100% 125%;background-repeat: no-repeat;background-attachment:fixed;background-size:cover;background-position:center;\" >";
if ($result == 2) {
    include('template/' . $template . '/login-failed-notification.php');
}

// Fetch active guest signup mode (phone or email) for loginform template
$signup_mode = 'phone';
$modeConn = new mysqli("localhost", "root", "sqlpass", "raddb");
if (!$modeConn->connect_error) {
    $planModeRes = $modeConn->query("SELECT signup_identifier_type FROM couponplans WHERE is_guest_active = 1 LIMIT 1");
    if ($planModeRes && $planRow = $planModeRes->fetch_assoc()) {
        if (!empty($planRow['signup_identifier_type'])) {
            $signup_mode = $planRow['signup_identifier_type'];
        }
    }
    $modeConn->close();
}

if ($result == 2 || $result == 5) {
    include('template/' . $template . '/loginform-header.php');
    include('template/' . $template . '/loginform-login.php');
    include('template/' . $template . '/loginform-footer.php');
}

if ($result == 1) {
    include('template/' . $template . '/login-successful.php');
}

if (($result == 4) || ($result == 12)) {
    include('template/' . $template . '/logoff.php');
}


if ($result == 11) {
    include('template/' . $template . '/loggingin-popup.php');
}


if (($result == 3) || ($result == 13)) {
    include('template/' . $template . '/prelogin.php');
}

echo "<script src=\"js/jquery-3.2.1.slim.min.js\"></script>";
echo "<script src=\"js/popper.min.js\"></script>";
echo "<script src=\"js/bootstrap.min.js\"></script>";
?>