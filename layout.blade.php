<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">
    <link rel="shortcut icon" href="images/favicon.png">
    <title>{{$page_title}}</title>
    <!--Core CSS -->
    <link href="bs3/css/bootstrap.min.css" rel="stylesheet">
    <link href="js/jquery-ui/jquery-ui-1.10.1.custom.min.css" rel="stylesheet">
    <link href="css/bootstrap-reset.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="js/jvector-map/jquery-jvectormap-1.2.2.css" rel="stylesheet">
    <link href="css/clndr.css" rel="stylesheet">

    <!--clock css-->
    <link href="js/css3clock/css/style.css" rel="stylesheet">
    <!--lightbox css-->
    <link href="css/ekko-lightbox.min.css" rel="stylesheet">
    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="js/morris-chart/morris.css">
    <!--dynamic table-->
    <link href="js/advanced-datatable/css/demo_page.css" rel="stylesheet" />
    <link href="js/advanced-datatable/css/demo_table.css" rel="stylesheet" />
    <link href="js/data-tables/DT_bootstrap.css" rel="stylesheet" />
    <link href="timepick/src/wickedpicker.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="js/bootstrap-datepicker/css/datepicker.css" />

    <link rel="stylesheet" type="text/css" href="js/bootstrap-daterangepicker/daterangepicker-bs3.css" />


    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet"/>
    <link href="css/print-styles.css" rel="stylesheet">
    <link href="css/pdf-batch.css" rel="stylesheet">
    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]>
    <script src="js/ie8-responsive-file-warning.js"></script><![endif]-->
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
<?php 
$uri='';
$arr_ex=array("admin-top-users.php","admin-user-date-accounting.php");
$uri=explode('/',$_SERVER['PHP_SELF']); 
if(in_array($uri[2],$arr_ex)){
?>
<body onload="custom_func();">
<?php }else { ?>
<body>
<?php } ?>
<section id="container">
<!--header start-->
<header class="header fixed-top clearfix" >
<!--logo start-->
<div class="brand" style="display:flex; align-items:center;">

    @if($type == 'admin')
    <a href="{{$site_url}}admin-home.php" class="logo" >
        <!-- <img src="{{ $site_url }}images/client-files/logo-file.jpg" alt=""  height="50" width="200"> -->
        <img src="{{ $site_url }}images/tunningsignin.png" alt=""  height="70" width="200" style="margin-left:2px">

       
    </a>
    @endif
    @if($type == 'operator')
    <a href="{{$site_url}}op-home.php" class="logo">
        <!-- <img src="{{ $site_url }}images/client-files/logo-file.jpg" alt=""  height="44" width="164"> -->
         <img src="{{ $site_url }}images/tunningsignin.png" alt=""  height="70" width="200" style="margin-left:2px">
    </a>
    @endif



   <div class="sidebar-toggle-box" style="display:flex; align-items:center; margin-bottom:20px">
        <div class="fa fa-bars"  ></div>
    </div>  
</div>
<!--logo end-->


<div class="top-nav clearfix">
    <!--search & user info start-->
    <ul class="nav pull-right top-menu">

        <!-- user login dropdown start-->
        <li class="dropdown">
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                <img alt="" src="images/avatar1_small.jpg">
                <span class="username">{{$name}}</span>
                <b class="caret"></b>
            </a>
            <ul class="dropdown-menu extended logout">
                <li><a href="{{$site_url}}logout.php"><i class="fa fa-sign-out"></i> Log Out</a></li>
            </ul>
        </li>

    </ul>
    <!--search & user info end-->
</div>
</header>
<!--header end-->
<!--sidebar start-->
<aside>
    <div id="sidebar" class="nav-collapse">
        <!-- sidebar menu start-->
        <div class="leftside-navigation">
            <ul class="sidebar-menu" id="nav-accordion">
                <li>
                    @if($type == 'admin')
                        <a class="active" href="{{$site_url}}admin-home.php">
                            <i class="fa fa-hashtag"></i>
                            <span>Dashboard</span>
                        </a>
                    @elseif($type == 'operator')
                        <a class="active" href="{{$site_url}}op-home.php">
                            <i class="fa fa-hashtag"></i>
                            <span>Dashboard</span>
                        </a>
                    @endif
                </li>
                @if($type == 'admin')

                    <li class="sub-menu">
                        <a href="#">
                            <i class="fa fa-user"></i>
                            <span>Manage Operators</span>
                        </a>
                        <ul class="sub">
                            <li><a href="{{$site_url}}admin-list-users.php">List Operators</a></li>
                            <li><a href="{{$site_url}}admin-create-user.php">Create Operator</a></li>
                        </ul>
                    </li>
					
			<li class="sub-menu" style="display:none;">
                        <a href="#">
                            <i class="fa fa-cogs"></i>
                            <span>Manage Policy</span>
                        </a>
                        <ul class="sub">
                            <!--<li><a href="{{$site_url}}admin-manage-policy-time.php">Time</a></li>-->
							<li><a href="{{$site_url}}admin-timepolicy-list.php">Time Policy</a></li>
                            <!--<li><a href="{{$site_url}}admin-manage-policy-bandwidth.php">Bandwidth</a></li>-->
				<li><a href="{{$site_url}}admin-bandwidthpolicy-list.php">Bandwidth Policy</a></li>
							<!--<li><a href="{{$site_url}}admin-manage-policy-data.php">DataPolicy</a></li>-->
							<li><a href="{{$site_url}}admin-datapolicy-list.php">Data Policy</a></li>
							<li><a href="{{$site_url}}admin-manage-policy-user-search.php">Edit User Policy</a></li>
                        </ul>
                    </li> 

                    <li class="sub-menu">
                        <a href="#">
                            <i class="fa fa-compress"></i>
                            <span>Manage Plans</span>
                        </a>
                        <ul class="sub">
                            <li><a href="{{$site_url}}admin-customer-plans.php">List Plans</a></li>
                            <li><a href="{{$site_url}}admin-create-plan.php">Create Plan</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="{{$site_url}}admin-toggle-login.php">
                            <i class="fa fa-toggle-on"></i>
                            <span>Toggle Login Mode</span>
                        </a>
                    </li>
					
					

                    <li class="sub-menu">
                        <a href="#">
                            <i class="fa fa-briefcase"></i>
                            <span>Manage Packs</span>
                        </a>
                        <ul class="sub">
                            <li><a href="{{$site_url}}admin-pack-list.php">List Packs</a></li>
                            <li><a href="{{$site_url}}admin-pack-create.php">Create Pack</a></li>
							<li><a href="{{$site_url}}op-admin-available-packs.php">Issue Coupon</a></li>
                        </ul>
                    </li>
                     
					
			
		
					

                    <!--<li class="sub-menu">
                        <a href="#">
                            <i class="fa fa-upload"></i>
                            <span>Offline Users</span>
                        </a>
                        <ul class="sub">
                            <li><a href="{{$site_url}}admin-offline-users.php">Offline Users</a></li>
                        </ul>
                    </li>-->


 <li class="sub-menu">
                        <a href="#">
                            <i class="fa fa-user-plus"></i>
                            <span>Manage Users</span>
                        </a>
                        <ul class="sub">
                        <li><a href="{{$site_url}}admin-tot-users.php">List Users</a></li>
                        <li><a href="{{$site_url}}admin-create-user-backend.php">Add User</a></li>
                        </ul>
                    </li>

                    <li class="sub-menu">
                        <a href="#">
                            <i class="fa fa-flag"></i>
                            <span>Reports</span>
                        </a>
                        <ul class="sub">
                         <li><a href="{{$site_url}}admin-online.php">Online Users</a></li>
                         <li><a href="{{$site_url}}admin-usage-report.php">Usage Reports</a></li>
                         <li><a href="{{$site_url}}admin-user-accounting.php">Usage Accounting</a></li>
						  <li><a href="{{$site_url}}admin-user-date-accounting.php">Date Accounting</a></li>
                          <li><a href="{{$site_url}}admin-signup-report.php">Guest User Report</a></li>
						  <li><a href="{{$site_url}}admin-top-users.php">Top users</a></li>
                        </ul>
		    </li>
		
			<li class="sub-menu">
                        <a href="#">
                            <i class="fa fa-flag"></i>
                            <span>Coupon Report</span>
                        </a>
                        <ul class="sub">

                          <li><a href="{{$site_url}}admin-user-coupon-report.php">Coupon Report</a></li>

                        </ul>
                    </li>




	     <li class="sub-menu" style="display:none">
                        <a href="#">
                                                        <i class="fa fa-filter"></i>
                            <span>Content Filter</span>
                        </a>
                        <ul class="sub">

                                                        <!--<li><a href="{{$site_url}}admin-basic-content-filter.php">Basic HTTP Filter</a></li>-->
                                                        <li class="sub-menu">
                                                                <a href="#">Basic HTTP Filter</a>
                                                                <ul class="sub">
                                                                        <li><a href="{{$site_url}}admin-web-content-filter.php">Website Filter</a></li>
                                                                        <li><a href="{{$site_url}}admin-keyword-content-filter.php">Keyword Filter</a></li>
                                                                </ul>
                                                        </li>
                                                        <li><a href="{{$site_url}}admin-adv-content-filter.php">Advanced HTTPS Filter</a></li>
                                                        <li><a href="{{$site_url}}admin-content-filter.php">Category Web Filter</a></li>
                        			        <li><a href="{{$site_url}}admin-site-exclusions.php">Category Filter Exclusions</a></li>

                        </ul>
                    </li>


                    <li class="sub-menu" style="display:none">
                        <a href="#">
                            <i class="fa fa-globe"></i>
                            <span>Access logs</span>
                        </a>
			<ul class="sub">
			    <li><a href="{{$site_url}}https-log.php">DNS Logs</a></li> 
                   <!--         <li><a href="{{$site_url}}admin-weblog.php">Web Logs</a></li>
                            <li><a href="{{$site_url}}admin-rt-weblog.php">Proxy Logs</a></li>
			    <li><a href="{{$site_url}}admin-exclusion-status.php">Filter logs</a></li>     -->
                        </ul>
                    </li>




		   <li class="sub-menu">
                        <a href="#">
                            <i class="fa fa-desktop"></i>
                            <span>Monitoring</span>
                        </a>
                        <ul class="sub">
                            <li><a href="{{$site_url}}eZServerMonitor"  target="_blank">Server Monitor</a></li>
                            <li><a href="{{$site_url}}sysinfo"  target="_blank">System Info</a></li>
                            <li><a href="{{$site_url}}admin-login-view-attempts.php">Login Attempts</a></li>
                            <!-- activity log 8-12-2025 -->
                            <li><a href="{{$site_url}}admin-activity-logs.php"> Activity Log</a></li>
                            <li><a href="{{$site_url}}admin-load-balance.php">WAN Status Logs </a></li>
                        </ul>
                    </li>


                    <li class="sub-menu">
                        <a href="#">
                            <i class="fa fa-bug"></i>
                            <span>Troubleshooting</span>
                        </a>
                        <ul class="sub">
                            <li><a href="{{$site_url}}admin-check-internet.php">Internet Status</a></li>
                            <li><a href="{{$site_url}}admin-ping-ip.php">Ping IP</a></li>
			    <li><a href="{{$site_url}}service_status.php">Service Status</a></li>
                              <li><a href="{{$site_url}}admin-offline-users.php">Offline Clients</a></li>
                        </ul>
		    </li>



			<li class="sub-menu">
                        <a href="#">
                            <i class="fa fa-sitemap"></i>
                            <span>Manage Network</span>
                        </a>
                        <ul class="sub">			    
			    <!--         <li><a href="{{$site_url}}admin-dynamic-dns.php">Dynamic DNS</a></li>    -->
				      <li><a href="{{$site_url}}admin-manage-network.php">WAN</a></li>
                        </ul>
           </li>
		   
		   
		   <li class="sub-menu">
                        <a href="#">
                            <i class="fa fa fa-desktop"></i>
                            <span>Manage Device</span>
                        </a>
                        <ul class="sub">
                        <!--    <li><a href="{{$site_url}}admin-manage-network.php">WAN</a></li>	--> 							
			    <li><a href="{{$site_url}}admin-manage-device.php">Power</a></li>
                        </ul>
                    </li>

			<li class="sub-menu">
                        <a href="#">
                            <i class="fa fa-eye"></i>
                            <span>Monitor AP's</span>
                        </a>
                        <ul class="sub">
                            <li><a href="{{$site_url}}ap_check_mod.php">List  AP</a></li>
                            <li><a href="{{$site_url}}admin-create-ap.php">Add  AP</a></li>
							
                        </ul>
                    </li>

                    <!-- content filter -->
                     <li class="sub-menu"  style="display:none">
                        <a href="#">
                            <i class="fa fa-filter"></i>
                            <span>Content Filtering </span>
                        </a>                         
                        <ul class="sub">
                            <li><a href="{{$site_url}}admin-content-filtering-Catrgory.php">Categories</a></li>
                            <li><a href="{{$site_url}}admin-content-filteringDomains.php">Domains</a></li>
                            <li><a href="{{$site_url}}admin-content-filtering.php">Alerts</a></li>
                        </ul>                     
                    </li>

                    

                    <!-- Settings Menu -->
                    <li class="sub-menu">
                        <a href="#">
                            <i class="fa fa-cog"></i>
                            <span>Settings</span>
                        </a>
                        <ul class="sub">
                            <li><a href="{{$site_url}}admin-ip-reservation.php">IP Reservation</a></li>
                        </ul>
                    </li>

                    <!-- logo upload -->

                    <li class="sub-menu">
                        <a href="#">
                            <i class="fa fa-upload"></i>
                            <span>Upload Logo</span>
                        </a>
                        <ul class="sub">
                            <li><a href="{{$site_url}}admin-client-logo-upload.php">Upload Logo</a></li>
                        </ul>
                    </li>

                 



                   


                    

                    <li class="sub-menu">
                        <a href="{{$site_url}}admin-change-password.php">
                            <i class="fa fa-key"></i>
                            <span>Change Password</span>
                        </a>
                    </li>
                    <li class="sub-menu" style="display:none">
                        <a href="http://172.27.27.27/optimus" target="_blank">
                            <i class="fa fa-wrench"></i>
                            <span>Advanced Management</span>
                        </a>
                    </li>
    @else

                    <li class="sub-menu" style="display:none;">
                        <a href="{{$site_url}}op-available-packs.php">
                            <i class="fa fa-scissors"></i>
                            <span>Coupon Packs</span>
                        </a>
                    </li>

                    <li class="sub-menu" style="display:none;">
                        <a href="{{$site_url}}op-batch-issue-nocustomerid.php">
                            <i class="fa fa-tags"></i>
                            <span>Issue Pre Print Coupon</span>
                        </a>
                    </li>
                @endif
            </ul>
       </div>
        <!-- sidebar menu end-->
    </div>
</aside>
<!--sidebar end-->
<!--main content start-->
<section id="main-content">
    <section class="wrapper">

    @yield('content')

    </section>
</section>
<!--main content end-->
<!--right sidebar start-->

<!--right sidebar end-->
</section>
<!-- Placed js at the end of the document so the pages load faster -->
<!--Core js-->
<script src="js/jquery.js"></script>
<script src="js/jquery-ui/jquery-ui-1.10.1.custom.min.js"></script>
<script src="bs3/js/bootstrap.min.js"></script>
<script src="js/common.js"></script>
<script src="js/jquery.dcjqaccordion.2.7.js"></script>
<script src="js/jquery.scrollTo.min.js"></script>
<script src="js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="js/jquery.nicescroll.js"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/flot-chart/excanvas.min.js"></script><![endif]-->
<script src="js/skycons/skycons.js"></script>
<script src="js/jquery.scrollTo/jquery.scrollTo.js"></script>
<script src="js/jquery.easing.min.js"></script>
<script src="js/calendar/clndr.js"></script>
<script src="js/underscore-min.js"></script>
<script src="js/calendar/moment-2.2.1.js"></script>
<script src="js/evnt.calendar.init.js"></script>
<script src="js/jvector-map/jquery-jvectormap-1.2.2.min.js"></script>
<script src="js/jvector-map/jquery-jvectormap-us-lcc-en.js"></script>
{{-- <script src="js/gauge/gauge.js"></script> --}}
<!--clock init-->
<script src="js/css3clock/js/css3clock.js"></script>
<!--Easy Pie Chart-->
<script src="js/easypiechart/jquery.easypiechart.js"></script>
<!--Sparkline Chart-->
<script src="js/sparkline/jquery.sparkline.js"></script>
<!--Morris Chart-->
<script src="js/morris-chart/morris.js"></script>
<script src="js/morris-chart/raphael-min.js"></script>
<!--jQuery Flot Chart-->
<script src="js/flot-chart/jquery.flot.js"></script>
<script src="js/flot-chart/jquery.flot.tooltip.min.js"></script>
<script src="js/flot-chart/jquery.flot.resize.js"></script>
<script src="js/flot-chart/jquery.flot.pie.resize.js"></script>
<script src="js/flot-chart/jquery.flot.animator.min.js"></script>
<script src="js/flot-chart/jquery.flot.growraf.js"></script>
<script src="js/dashboard.js"></script>
<script src="js/ekko-lightbox.min.js"></script>
<script src="js/jQuery.print.js"></script>
<script src="js/jquery.customSelect.min.js" ></script>

<script type="text/javascript" src="timepick/src/wickedpicker.js"></script>

<!--script for this page-->
<!--dynamic table-->
<script type="text/javascript" language="javascript" src="js/advanced-datatable/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="js/data-tables/DT_bootstrap.js"></script>

<script type="text/javascript" src="js/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/jquery.form.min.js"></script>
<!--common script init for all pages-->
<script src="js/scripts.js"></script>


<!-- used to bring graph -->
<!-- <script type="text/javascript" src="http://100.99.17.30:19999/dashboard.js?theme=slate"></script> -->

<script type="text/javascript">
    // Tell Netdata not to load its own jQuery/Bootstrap
    var netdataNoBootstrap = true;
    var netdataNoFontAwesome = true;
    var netdataNojQuery = true;
</script>
<script type="text/javascript" src="http://100.99.17.30:19999/dashboard.js?theme=slate"></script>


<!--dynamic table initialization -->
<script src="js/dynamic_table_init.js"></script>
</body>
</html>
