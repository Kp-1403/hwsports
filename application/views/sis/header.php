<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="description" content="">
        <title><?=$title?> | ***********************************************************</title>
        <link href="../../css/normalize.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/<?=$slug?>/main.css" rel="stylesheet" type="text/css">
        <link href="../../css/<?=$slug?>/sis.css" rel="stylesheet" type="text/css">
        <style type="text/css">
        <!--
            body {
                background:#E7EEF9;
            }
            #header {
                background:#00468C;
                /************************************************************</*/
                /************************************************************</*/
                /************************************************************</*/
                /************************************************************</*/
                /************************************************************</*/
                background-image: url("../../img/banners/hwsports.png");
            }
            #content h1, #content h2, #content h3, #content a{
                color:#00468C;
            }
        -->
        </style>
    </head>
    <!--[if lt IE 7]>      <body class="page-<?=$_ci_view?> lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
    <!--[if IE 7]>         <body class="page-<?=$_ci_view?> lt-ie9 lt-ie8"> <![endif]-->
    <!--[if IE 8]>         <body class="page-<?=$_ci_view?> lt-ie9"> <![endif]-->
    <!--[if gt IE 8]><!--> <body class="page-<?=$_ci_view?>"> <!--<![endif]-->
    <!--[if lt IE 7]>
        <p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
    <![endif]-->
    <body>
        <div id="container">
            <a href="/"><div id="header"></div></a>
            <div id="menu">
                <ul>
                    <a href="/"><li <? if($title=="Homepage"){ ?>class="selected"<? } ?>><img src="../../img/icons/home.14.png"/>Homepage</li></a>
                    <a href="/sis/whatson"><li <? if($title=="What's On"){ ?>class="selected"<? } ?>><img src="../../img/icons/match.14.png"/>What's On</li></a>
                    <a href="/sis/calendar"><li <? if($title=="Calendar"){ ?>class="selected"<? } ?>><img src="../../img/icons/calendar.14.png"/>Calendar</li></a>
                    <a href="/sis/tournaments"><li <? if($title=="Tournaments"){ ?>class="selected"<? } ?>><img src="../../img/icons/tournament.14.png"/>Tournaments</li></a>
                    <a href="/auth/register"><li class="special"><img src="../../img/icons/star.14.png"/>Registration</li></a>
                    <? if($this->ion_auth->logged_in()){ ?>
                        <a href="/auth/logout"><li><img src="../../img/icons/key.14.png"/>Logout</li></a>
                    <? } else { ?>
                        <a href="/auth/login"><li><img src="../../img/icons/key.14.png"/>Login</li></a>
                    <? } ?>
					<? if($this->ion_auth->in_group('admin') || $this->ion_auth->in_group('centreadmin')){ ?>
                        <a href="/tms"><li><img src="../../img/icons/key.14.png"/>Management</li></a>
                    <? } ?>
                    <a href="sis/help"><li <? if($title=="Help"){ ?>class="selected"<? } ?>><img src="../../img/icons/help.14.png"/>Help</li></a>
                </ul>
            </div>
            <div id="content">