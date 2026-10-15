<?php
/*
 *********************************************************************************************************
 * daloRADIUS - RADIUS Web Platform
 * Copyright (C) 2007 - Liran Tal <liran@enginx.com> All Rights Reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 *********************************************************************************************************
 *
 * Authors:     Liran Tal <liran@enginx.com>
 *
 * daloRADIUS edition - fixed up variable definition through-out the code
 * as well as parted the code for the sake of modularity and ability to
 * to support templates and languages easier.
 * Copyright (C) Enginx and Liran Tal 2007, 2008
 *
 *********************************************************************************************************
 */

?>

<style>
    /* Style for the Resend OTP link */
    .resendotp {
        color: #007bff;
        /* Bright blue color */
        font-size: 16px;
        font-weight: bold;
        text-decoration: none;
        display: inline-block;
        margin-top: 10px;
        padding: 5px 10px;
        border-radius: 5px;
        transition: background-color 0.3s, color 0.3s;
    }

    /* Container for the form */
    .container {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        font-family: Arial, sans-serif;
        position: relative;
        width: 100%;
        /* Ensure full width on mobile */
        overflow: hidden;
        /* Allow scrolling if content exceeds screen height */
    }

    /* Wrapper for the form */
    .wrapper {
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        width: 90%;
        max-width: 400px;
        box-sizing: border-box;
        text-align: center;
        margin-bottom: 132px;
        overflow: hidden;
    }

    /* Style for the login form */
    .form-signin {
        width: 100%;
        box-sizing: border-box;
    }

    .form-group img {
        max-width: 100%;
        height: auto;
    }

    .form-signin .form-control {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border-radius: 5px;
        border: 1px solid #ccc;
        box-sizing: border-box;
    }

    .toast {
        visibility: hidden;
        min-width: 250px;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 5px;
        padding: 16px;
        position: fixed;
        z-index: 1000;
        left: 50%;
        bottom: 30px;
        transform: translateX(-50%);
        font-size: 16px;
        opacity: 0;
        transition: opacity 0.5s, bottom 0.5s;
    }

    .toast.show {
        visibility: visible;
        opacity: 1;
        bottom: 50px;
    }

    /* Flex container for the buttons */
    .login-buttons {
        display: flex;
        flex-direction: row;
        /* Stack buttons vertically on small screens */
        gap: 10px;
        /* Space between buttons */
        margin-top: 10px;
    }

    /* Styling for the buttons */
    .btn-primary,
    .btn-secondary {
        cursor: pointer;
        font-size: 14px;
        font-weight: 700;
        border: none;
        padding: 10px;
        border-radius: 5px;
        transition: background-color 0.3s;
        margin-top: 8px;
        width: 48%;
        /* Ensure buttons take full width */
        flex: 1;
    }

    .btn-primary {
        background-color: #007bff;
        color: #fff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: #fff;
    }

    .btn-secondary:hover {
        background-color: #565e64;
    }

    /* Submit button style */
    .sbt-normal-login,
    .sbt-coupon-login {
        background-color: #343a40;
        color: #fff;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        padding: 10px;
        border: none;
        border-radius: 5px;
        transition: background-color 0.3s;
        width: 100%;
        box-sizing: border-box;
        margin-top: 10px;
    }

    .sbt-normal-login:hover,
    .sbt-coupon-login:hover {
        background-color: #23272b;
    }

    /* Toggle bar style */
    .toggle-bar {
        font-size: 14px;
        margin-top: 15px;
        color: #555;
    }

    .toggle-bar a {
        color: #007bff;
        text-decoration: none;
    }

    .toggle-bar a:hover {
        text-decoration: underline;
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .wrapper {
            width: 100%;
            padding: 0px;
        }

        /* Stack buttons vertically on smaller screens */
        .login-buttons {
            gap: 16px;
            /* Space between buttons */
        }

        .btn-primary,
        .btn-secondary {
            width: 90%;
            /* Full width on mobile */
            margin-left: 0px;
        }

        .btn-secondary {
            margin-left: -6px;
            width: 100%;
        }

        /* Adjust input field margins for better spacing */
        .form-signin .form-control {
            margin: 8px 0;
            /* Smaller margin for inputs */
        }

        /* Make logo responsive */
        .form-group img {
            height: auto;
            /* Maintain aspect ratio */
            max-height: 80px;
            /* Limit maximum height */
        }

    }
</style>

<script type='text/javascript'>


    function switchView(mode) {
        const setReq = (ids, req) => ids.forEach(id => {
            const el = document.getElementById(id) || document.getElementsByName(id)[0];
            if (el) req ? el.setAttribute('required', '') : el.removeAttribute('required');
        });
        const show = (ids, d) => ids.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = d;
        });

        // Default Reset
        show(['user-login-fields', 'coupon-login-fields', 'signup-fields', 'resend-otp-submit', 'login-radio-buttons', 'txtpassword', 'resendotplink', 'backbutton', 'signup-link-text'], 'none');
        setReq(['signup_name', 'signup_mobile', 'signup_email', 'txtpassword', 'txtUserName', 'txtCouponCode'], false);
        document.querySelector('.sbt-normal-login').style.display = 'none';

        if (mode === 'user') {
            document.getElementById('login-type-id').value = 'user';
            show(['user-login-fields', 'login-radio-buttons', 'txtpassword', 'resendotplink', 'signup-link-text'], 'block');
            document.querySelector('.sbt-normal-login').style.display = 'block';
            setReq(['txtpassword', 'txtUserName'], true);
            if (document.getElementById('user-login')) document.getElementById('user-login').checked = true;
        }

        else if (mode === 'coupon') {
            document.getElementById('login-type-id').value = 'coupon';
            show(['coupon-login-fields', 'login-radio-buttons', 'signup-link-text'], 'block');
            setReq(['txtCouponCode'], true);
            if (document.getElementById('coupon-login')) document.getElementById('coupon-login').checked = true;
        }

        else if (mode === 'resend') {
            show(['user-login-fields', 'resend-otp-submit', 'backbutton'], 'block');
            setReq(['txtUserName'], true);
        }

        else if (mode === 'signup') {
            document.getElementById('login-type-id').value = 'coupon';
            show(['signup-fields'], 'block');
            <?php if (isset($signup_mode) && $signup_mode === 'email'): ?>
            setReq(['signup_name', 'signup_email'], true);
            <?php else: ?>
            setReq(['signup_name', 'signup_mobile'], true);
            <?php endif; ?>
            checkSignupLimit();
        }

        if (document.getElementById('toast')) document.getElementById('toast').innerText = '';
    }

    window.onload = function () {
        switchView('user');


        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('reason') === 'otp_expired') {
            showToast("Your OTP has expired (10-minute limit). Please Sign Up again for a new code.");
        }
        if (urlParams.get('signup') === 'new') {
            <?php if (isset($signup_mode) && $signup_mode === 'email'): ?>
            showToast("Signup successful! Please enter the Access Code sent to your email.");
            <?php else: ?>
            showToast("Signup successful! Please enter the OTP sent to your mobile.");
            <?php endif; ?>
            switchView('coupon');
        }
        if (urlParams.get('reason') === 'limit_reached') {
            const type = urlParams.get('type');
            if (type === 'reset_gap') {
                showToast("Reset time not finished. Try signing up after some time.");
            } else {
                showToast("Signup limit reached. Please check the Admin report.");
            }
            switchView('signup');
        }
        if (urlParams.get('reason') === 'invalid_mobile') {
            showToast("Invalid mobile number. Please enter a 10-digit numeric value.");
            switchView('signup');
        }
        if (urlParams.get('reason') === 'invalid_email') {
            showToast("Invalid email address. Please enter a valid email address (e.g. name@gmail.com).");
            switchView('signup');
        }
        if (urlParams.get('reason') === 'no_active_plan') {
            showToast("No active guest plan found. Please contact administrator.");
            switchView('signup');
        }
        if (urlParams.get('reason') === 'invalid_name') {
            showToast("Invalid name. Please enter only alphabets and spaces.");
            switchView('signup');
        }
    }

    function showToast(message) {
        var toast = document.getElementById('toast');
        if (!toast) {
            alert('Toast element not found!');
            return;
        }
        toast.innerText = message;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }


    // Resend triggered by the UI
    function resendOTP() {
        var username = document.getElementById('txtUserName').value.trim();
        if (!username) { showToast('Username is required'); return; }

        // We call our own backend
        fetch('hotspotlogin.php?action=resend_password&uhid=' + username)
            .then(response => response.json())
            .then(data => {
                if (data.success) { showToast('Password resent successfully'); }
                else { showToast('Failed to resend'); }
            });
    }

    // Limit Check
    function checkSignupLimit() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('reason') === 'limit_reached') {

            showToast("Signup limit reached. Please check the Admin report for your specific reset time.");
        }
    }


</script>



<div class='container'>
    <div class='wrapper'>
        <form action='<?php echo $loginpath; ?>' method='post' name='Login_Form' class='form-signin'>

            <input type='hidden' id='login-type-id' name='login-type' value='user'>

            <input type='hidden' name='challenge' value='<?php echo $challenge; ?>'>
            <input type='hidden' name='uamip' value='<?php echo $uamip; ?>'>
            <input type='hidden' name='uamport' value='<?php echo $uamport; ?>'>
            <input type='hidden' name='userurl' value='<?php echo $userurl; ?>'>

            <div class="form-group" style="text-align:center;">

                <img src="clientlogo.png" alt="Logo" height="80" width="229" />
            </div>

            <div id='toast' class="toast"></div>




            <div class='login-buttons' id='login-radio-buttons'>
                <label>
                    <input type='radio' name='login-radio-group' id='user-login' value='user' checked
                        onclick="switchView('user')"> IP Login
                </label>
                <label>
                    <input type='radio' name='login-radio-group' id='coupon-login' value='coupon'
                        onclick="switchView('coupon')"> Coupon Login
                </label>
            </div>




            <div id='user-login-fields'>
                <input type='text' class='form-control' id='txtUserName' name='UserName'
                    placeholder='<?php echo $centerUsername; ?>' required='' autofocus='' />
                <input type='password' class='form-control' id='txtpassword' name='Password'
                    placeholder='<?php echo $centerPassword; ?>' required='' autofocus='' />
                <a href='javascript:void(0)' id='resendotplink' onclick="switchView('resend')">Resend Password</a>

                <input type='submit' name='button' value='Login' class='sbt-normal-login'
                    style='width:100%;text-align:center;' />

                <div style='display: flex; justify-content: space-between; width: 100%;'>
                    <input type='button' id='resend-otp-submit' value='Submit' class='sbt-normal-login'
                        style='width:45%; text-align:center; display:none;' onclick='resendOTP()' />
                    <input type='button' id='backbutton' value='Back' class='sbt-normal-login'
                        style='width:45%; text-align:center; display:none;' onclick="switchView('user')" />
                </div>
            </div>

            <div id='coupon-login-fields' class='hidden'>
                <div id='coupon-auth-input' style='display:block;'>
                    <input type='text' class='form-control' name='couponCode' id='txtCouponCode'
                        placeholder='<?php echo $centerCouponCode; ?>' />
                    <!-- <a href='javascript:void(0)' id='resend-link-trigger' class='resendotplink' onclick='openResendUI()'>I didn't get my code</a> -->
                </div>


                <input type='submit' name='button' id='coupon-login-btn' class='sbt-coupon-login' value='Login'
                    style='width:100%;text-align:center;' />
            </div>




            <div id='signup-fields' style='display:none;'>
                <input type='text' class='form-control' name='signup_name' id='signup_name' placeholder='Full Name'
                    pattern='[A-Za-z\s]+' title='Only alphabets and spaces are allowed' />
                <?php if (isset($signup_mode) && $signup_mode === 'email'): ?>
                    <input type='email' class='form-control' name='signup_email' id='signup_email' placeholder='Email Address'
                        pattern='[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}' title='Please enter a valid email address (e.g. name@gmail.com)' />
                <?php else: ?>
                    <input type='tel' class='form-control' name='signup_mobile' id='signup_mobile' placeholder='Mobile Number'
                        pattern='[0-9]{10}' maxlength='10' title='Please enter a valid 10-digit mobile number' />
                <?php endif; ?>
                <input type='submit' name='sbt-signup' value='Sign Up' class='sbt-normal-login' style='width:100%;' />
                <div style='margin-top: 10px;'>
                    <a href='javascript:void(0)' onclick="switchView('user')" style='font-size: 13px; color: #007bff; text-decoration: none;'>Already have access? Back to Login</a>
                </div>
            </div>

            <div class='toggle-bar' style='font-size:14px;'>
                <span id='signup-link-text'>
                    Don't have access? <a href='javascript:void(0)' onclick="switchView('signup')">Sign Up for WiFi</a>
                </span>

                <br>
                By signing in, I accept the <a href='termsagreement.html' target='_blank'><u
                        style='font-weight:500;font-size:13px;'>Terms & Conditions</u></a>
            </div>
            <br>
            <input type='hidden' name='button' value='Login'>
        </form>
    </div>
</div>

<?php
/* Footer Logic */
?>