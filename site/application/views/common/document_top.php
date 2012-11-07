<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo $title ?></title>
    <meta name="description" content="">
    <meta name="author" content="SAPO WEB & MEDIA DESIGN">
	
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="320">
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;">

    <link rel="shortcut icon" href="<?php echo assets_url('imgs') ?>ink-favicon.ico">
    <link rel="apple-touch-icon-precomposed" href="<?php echo assets_url('imgs') ?>touch-icon.57.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo assets_url('imgs') ?>touch-icon.72.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo assets_url('imgs') ?>touch-icon.114.png">
	<link rel="apple-touch-startup-image" href="<?php echo assets_url('imgs') ?>splash.320x460.png" media="screen and (min-device-width: 200px) and (max-device-width: 320px) and (orientation:portrait)">
	<link rel="apple-touch-startup-image" href="<?php echo assets_url('imgs') ?>splash.768x1004.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)">
	<link rel="apple-touch-startup-image" href="<?php echo assets_url('imgs') ?>splash.1024x748.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)">

	<link rel="stylesheet" type="text/css" href="<?php echo assets_url('css') ?>ink.css" />

	<!--[if IE ]>
	<link rel="stylesheet" href="<?php echo assets_url('css') ?>ink-ie.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<![endif]-->
	
	<link rel="stylesheet" href="<?php echo specific_url('css') ?>demo.css" type="text/css" media="screen" title="no title" charset="utf-8">
	
	<link rel="stylesheet" type="text/css" href="http://js.sapo.pt/Assets/Images/ComponentDialog2/themes/default/theme.css"/>

	<!-- DEFINES TRANSITIONS: appear3d, slide, hover -->
	<link rel="stylesheet" type="text/css" href="http://js.sapo.pt/Assets/Images/ComponentDialog2/effects/appear3d.css"/>

    <script type="text/javascript" src="http://js.staging.sapo.pt/Bundles/Ink.js"></script>
	<script type="text/javascript" src="http://js.staging.sapo.pt/SAPO/Dom/Selector/0.1/"></script>
	<script type="text/javascript" src="http://js.staging.sapo.pt/SAPO/Component/Tabs/0.1/"></script>
	<script type="text/javascript" src="http://js.staging.sapo.pt/SAPO/Component/Tooltip/1.1/"></script>
	<script type="text/javascript" src="http://js.staging.sapo.pt/SAPO/Effects/Core/0.1/"></script>
	<script type="text/javascript" src="http://js.staging.sapo.pt/SAPO/Effects/Slide/0.1/"></script>
	<script type="text/javascript" src="http://js.staging.sapo.pt/SAPO/Dom/Event/0.1/"></script>
	<script type="text/javascript" src="http://js.staging.sapo.pt/SAPO/Dom/Css/0.1/"></script>
    <script type="text/javascript" src="http://js.staging.sapo.pt/SAPO/Dom/Element/0.1/"></script>
	<script type="text/javascript" src="http://js.staging.sapo.pt/SAPO/Dom/Element/0.1/"></script>
	<script type="text/javascript" src="http://js.staging.sapo.pt/SAPO/Utility/Dimensions/0.1/"></script>
	<script type="text/javascript" src="http://js.staging.sapo.pt/SAPO/Component/DatePicker/2.1/"></script> 
    <script type="text/javascript" src="http://js.staging.sapo.pt/SAPO/Utility/Color/0.1/"></script>
    <script type="text/javascript" src="http://js.staging.sapo.pt/SAPO/Component/ColorWheel/0.1/"></script>
    <script type="text/javascript" src="http://js.staging.sapo.pt/SAPO/Ink/Modal/0.1/"></script>

	<script type="text/javascript" src="<?php echo specific_url('js') ?>html5shiv.js"></script>
	<script type="text/javascript" src="<?php echo specific_url('js') ?>html5shiv-printshiv.js"></script>
	<script type="text/javascript" src="<?php echo specific_url('js') ?>respond.src.js"></script>
	<script type="text/javascript" src="<?php echo specific_url('js') ?>prettify.js"></script>
</head>
<body onload="prettyPrint()">
