<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>RedoxShout</title>

	<!-- Bootstrap -->
	<link href="./views/static/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" media="screen" href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/master/build/css/bootstrap-datetimepicker.min.css" />
	<style>
	body {
		padding-top: 50px;
	}
	.starter-template {
		padding: 40px 15px;
		text-align: center;
	}
	.form-group {
		margin-left: 5px;
		margin-right: 5px;
	}
	.td_message {
		color: black;
	}
	.td_message:hover {
		text-decoration: none;
		
	}
	</style>

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>
	<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<a class="navbar-brand" href="./">RedoxShout</a>
			</div>
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li><a href="./" <?php if($page=="search"){ echo "class=\"active\""; }?>>Home</a></li>
					<li><a href="?page=top#top_currentMonth" <?php if($page=="top"){ echo "class=\"active\""; }?>>Classement mois courant</a></li>
					<li><a href="?page=top#top_anytime" <?php if($page=="top"){ echo "class=\"active\""; }?>>Classement général</a></li>
					<li><a target="_blank" href="http://forum.redoxbot.net/index.php?/topic/20253-redoxshout-lhistorique-de-la-shoutbox/">Topic forum</a></li>
				</ul>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="starter-template">