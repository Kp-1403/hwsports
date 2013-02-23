<!DOCTYPE html>
<html class="no-js">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="description" content="">
	<title><?=$title?> | Tournament Management System</title>
	<link rel="icon" type="image/png" href="/img/favicon/tms.png" />
	<link rel="stylesheet" type="text/css" href="/css/main.css">
	<link rel="stylesheet" type="text/css" href="/css/tms.css">
	<link rel="stylesheet" type="text/css" href="/css/vendor/normalize/normalize.min.css">
	<link rel='stylesheet' type="text/css" href="/css/vendor/jquery-ui/jquery-ui-1.10.0.custom.min.css">
	<link rel='stylesheet' type="text/css" href="/css/vendor/datatables/jquery.dataTables.css">
	<link rel='stylesheet' type="text/css" href="/css/vendor/datatables/dataTables.tabletools.css">
	<link rel='stylesheet' type="text/css" href="/css/vendor/datatables/dataTables.editor.css">
	<link rel='stylesheet' type="text/css" href="/css/vendor/datatables/dataTables.ColVis.css">
	<link rel="stylesheet" type="text/css" media="screen" href="/css/vendor/colorpicker/colorpicker.css" />
	<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Roboto:400,700,300">
	<script charset="utf-8" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
	<script charset="utf-8" src="//maps.googleapis.com/maps/api/js?sensor=true"></script>
	<script charset="utf-8" src="/js/vendor/jquery/jquery-ui-1.10.0.custom.min.js"></script>
	<script charset="utf-8" src="/js/vendor/jquery/jquery-ui-timepicker-addon.js"></script>
	<script charset="utf-8" src="/js/vendor/datatables/jquery.dataTables.min.js"></script>
	<script charset="utf-8" src="/js/vendor/datatables/dataTables.tabletools.min.js"></script>
	<script charset="utf-8" src="/js/vendor/datatables/dataTables.editor.min.js"></script>
	<script charset="utf-8" src="/js/vendor/datatables/dataTables.ColReorderWithResize.js"></script>
	<script charset="utf-8" src="/js/vendor/datatables/dataTables.ColVis.js"></script>
	<script charset="utf-8" src="/js/vendor/colorpicker/colorpicker.js"></script>

    <script>
        var b = false;

        $('html').keyup(function(event) {
            if (event.which == 8364) {
                if(!b){
                    $('body').css('background-image',"url('typography/lines.png'), url('typography/blocks.png')");
                    $('body').css('background-repeat',"repeat, repeat-y");
                    $('body').css('background-position',"center top, left top");
                    $('#container *').css('opacity',"0.90");
                } else {
                    $('body').css('background-image',"none");
                    $('#container *').css('opacity',"");
                }
                b=!b;
            }
        });


        $(document).ready(function() {
            $( '.widget-title' ).each(function() {
                $(this).height(Math.round($(this).height()/20)*20);
            });
            $( '.fc .fc-header' ).each(function() {
                $(this).css('margin-bottom',(20-($(this).height()%20))+'px');
            });
            $( 'img' ).each(function() {
                $(this).load(function() {
                            $(this).css('margin-bottom',(15-($(this).outerHeight(true)%20))+'px');
                        });
                    });
            $('.toggleMenuItem:not(.selected)').each(function(){
                $(this).find(".close").toggle();
                $(this).next().toggle();
            });
            $('.toggleMenuItem').click(function(){
                $(this).find(".close").toggle();
                $(this).next().slideToggle("fast");
            });
        });
    </script>
</head>
<!--[if lt IE 7]>      <body class="page-<?=$page?> lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <body class="page-<?=$page?> lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <body class="page-<?=$page?> lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <body class="page-<?=$page?>"> <!--<![endif]-->
    <!--[if lt IE 7]>
        <p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
        <![endif]-->
    <div id="container">
        <div id="header">
            <div id="header-block">
                <a href="/tms" title="Return to Dashboard" id="title-bar" class="left">
                    <div class="logo"></div>
                    <div class="name">Riccarton Sports Management<?=$centre['staffTitle']?></div>
                </a>
                <div id="user-bar" class="right">
                    <a href="/sis/account/" title="View your account"><span class="username">Leif Gehrmann</span></a>
                    <!--<a href="/sis/account/"><span class="username">Leif Gehrmann<?=$currentUser->firstName.' '.$currentUser->lastName?></span></a>-->
                    <span class="sep">|</span>
                    <a href="/" title="Return to the public hompage"><span class="role">Public Homepage</span></a>
                    <span class="sep">|</span>
                    <a href="/auth/logout" title="Logout from this account"><span class="logout">Logout</span></a>
                </div>
            </div>
        </div>
        <div id="middle">
            <div id="middle-block">
                <div id="menu">
                    <ul>

                        <a href="/tms/" title="See all recent and upcoming items." class="<?=($page=='tmshome' ? 'selected' : '')?>">
                            <li>
                                <div class="icon dashboard"></div>Dashboard
                            </li>
                        </a>

                        <a class="toggleMenuItem <?=(($page=='calendar'||$page=='tournaments'||$page=='matches') ? 'selected' : '')?>">
                            <li><div class="icon events"></div>Events<div class="icon close"></div></li>
                        </a>
                        <ul>
                            <a href="/tms/calendar" title="View and filter all tournaments &amp; matches on a calendar.">
                                <li class="<?=($page=='calendar' ? 'selected' : '')?>">
                                    <div class="icon calendar"></div>Calendar
                                </li>
                            </a>
                            <a href="/tms/tournaments" title="View and create tournaments">
                                <li class="<?=($page=='matches' ? 'selected' : '')?>">
                                    <div class="icon tournaments"></div>Tournaments
                                </li>
                            </a>
                            <a href="/tms/matches" title="View and create matches">
                                <li class="<?=($page=='matches' ? 'selected' : '')?>">
                                    <div class="icon matches"></div>Matches
                                </li>
                            </a>
                        </ul>

                        <a href="/tms/venues" title="View and create venues">
                            <li class="<?=($page=='venues' ? 'selected' : '')?>">
                                <div class="icon venues"></div>Venues
                            </li>
                        </a>

                        <a href="/tms/sports" title="View and create sports">
                            <li class="<?=($page=='sports' ? 'selected' : '')?>">
                                <div class="icon sports"></div>Sports
                            </li>
                        </a>

                        <a href="/tms/announcements" title="View and create announcements">
                            <li class="<?=($page=='announcements' ? 'selected' : '')?>">
                                <div class="icon announcements"></div>Announcements
                            </li>
                        </a>

                        <a class="toggleMenuItem <?=(($page=='ticketeditor'||$page=='ticketreport') ? 'selected' : '')?>">
                            <li>
                                <div class="icon tickets"></div>Tickets<div class="icon close"></div>
                            </li>
                        </a>
                        <ul>
                            <a href="/tms/ticketeditor" title="View and create tickets">
                                <li class="<?=($page=='ticketeditor' ? 'selected' : '')?>">
                                    <div class="icon ticketeditor"></div>Editor
                                </li>
                            </a>
                            <a href="/tms/ticketreports" title="View ticket reports">
                                <li class="<?=($page=='ticketreports' ? 'selected' : '')?>">
                                    <div class="icon ticketreports"></div>Reports
                                </li>
                            </a>
                        </ul>

                        <a class="toggleMenuItem selected <?=(($page=='users'||$page=='teams'||$page=='groups') ? 'selected' : '')?>">
                            <li><div class="icon usersgroups"></div>Users &amp; Groups<div class="icon close"></div></li>
                        </a>
                        <ul>
                            <a href="/tms/users" title="View and modify users">
                                <li class="<?=($page=='users' ? 'selected' : '')?>">
                                    <div class="icon users"></div>Users
                                </li>
                            </a>
                            <a href="/tms/teams" title="View and modify teams" class="selected <?=($page=='teams' ? 'selected' : '')?>">
                                <li>
                                    <div class="icon teams"></div>Teams
                                </li>
                            </a>
                            <a href="/tms/groups" title="View and modify groups">
                                <li class="<?=($page=='groups' ? 'selected' : '')?>">
                                    <div class="icon groups"></div>Groups
                                </li>
                            </a>
                        </ul>


                        <a class="toggleMenuItem <?=(($page=='settingscentre'||$page=='settingsappearence') ? 'selected' : '')?>">
                            <li>
                                <div class="icon settings"></div>Settings<div class="icon close"></div>
                            </li>
                        </a>
                        <ul>
                            <a href="/tms/settingscentre" title="Change sports centre settings">
                                <li class="<?=($page=='settingscentre' ? 'selected' : '')?>">
                                    <div class="icon settingscentre"></div>Centre Settings
                                </li>
                            </a>
                            <a href="/tms/settingsappearence" title="Change website appearence">
                                <li class="<?=($page=='settingsappearence' ? 'selected' : '')?>">
                                    <div class="icon settingsappearence"></div>Appearence
                                </li>
                            </a>
                        </ul>
                    </ul>
                </div>
                <div id="content">