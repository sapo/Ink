<?php

	$inkOptions = array(
		array(
			'label' => array('text'=>'Layout','for'=>'layout'),
			'attributes' => array('name'=>'layout','id'=>'layout','value'=>'1')
		),
		array(
			'label' => array('text'=>'Navigation','for'=>'navigation'),
			'attributes' => array('name'=>'navigation','id'=>'navigation','value'=>'1')
		),
		array(
			'label' => array('text'=>'Typography','for'=>'typography'),
			'attributes' => array('name'=>'typography','id'=>'typography','value'=>'1')
		),
		array(
			'label' => array('text'=>'Icons','for'=>'icons'),
			'attributes' => array('name'=>'icons','id'=>'icons','value'=>'1')
		),
		array(
			'label' => array('text'=>'Forms','for'=>'forms'),
			'attributes' => array('name'=>'forms','id'=>'forms','value'=>'1')
		)
	);

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to CodeIgniter</title>
	<meta charset="utf-8">
    <title>SAPO UI Toolkit</title>
    <meta name="description" content="">
    <meta name="author" content="SAPO WEB & MEDIA DESIGN">
	
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="320">
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;">

    <link rel="shortcut icon" href="styles/imgs/favicon.ico">
    <link rel="apple-touch-icon-precomposed" href="styles/imgs/touch-icon.57.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="styles/imgs/touch-icon.72.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="styles/imgs/touch-icon.114.png">
	<link rel="apple-touch-startup-image" href="styles/imgs/splash.320x460.png" media="screen and (min-device-width: 200px) and (max-device-width: 320px) and (orientation:portrait)">
	<link rel="apple-touch-startup-image" href="styles/imgs/splash.768x1004.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)">
	<link rel="apple-touch-startup-image" href="styles/imgs/splash.1024x748.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)">
	

	<link rel="stylesheet" href="../ink/css/normalize.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="../ink/css/common.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="../ink/css/typo.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="../ink/css/forms.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="../ink/css/buttons.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="../ink/css/navigation.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="../ink/css/tables.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="../ink/css/pagination.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="../ink/css/alerts.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="../ink/css/widgets.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="../ink/css/grid.css" type="text/css" media="screen" title="no title" charset="utf-8">

	<!--[if IE 6]>
	<link rel="stylesheet" href="../css/ie6.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<![endif]-->
	<!--[if IE 7]>
	<link rel="stylesheet" href="../css/ie7.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<![endif]-->
	
	<link rel="stylesheet" href="styles/css/demo.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<link rel="stylesheet" href="styles/css/prettify.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<script type="text/javascript" src="http://js.staging.sapo.pt/Bundles/ink.js"></script>
	<script type="text/javascript" src="http://js.sapo.pt/SAPO/Component/Tabs/0.1/"></script>
	<script type="text/javascript" src="http://js.sapo.pt/SAPO/Component/Tooltip/1.1/"></script>
	<script type="text/javascript" src="http://js.sapo.pt/SAPO/Effects/Core/0.1/"></script>
	<script type="text/javascript" src="http://js.sapo.pt/SAPO/Effects/Slide/0.1/"></script>
	<script type="text/javascript" src="http://js.sapo.pt/SAPO/Dom/Element/0.1/"></script>
	<script type="text/javascript" src="http://js.sapo.pt/SAPO/Component/DatePicker/2.1/"></script>
</head>
<body>
	<?php echo form_open('download/custom',array('class'=>'ink-l50 ink-labels-above')); ?>
	<?php foreach ($inkOptions as $option): ?>
		<p class="ink-form-row">
		<?php echo form_label($option['label']['text'],$option['label']['for']); ?>
		<?php echo form_checkbox($option['attributes']) ?>
		</p>
	<?php endforeach; ?>
	<?php echo form_close(); ?>
</body>
</html>