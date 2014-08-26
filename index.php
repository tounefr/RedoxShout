<?php
require_once('./config.php');
require_once('./class/redoxshout.class.php');


try {
	$redox = new RedoxShout(FORUM_USERNAME, FORUM_PASSWORD);
} catch(Exception $e) {
	exit($e->getMessage());
}

$pattern_integer = "#[0-9]{1,}#";
$pattern_date = "#([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})#";

if(!empty($_POST)) {
	if(
		!empty($_POST['date_start']) &&
		!empty($_POST['hour_start']) &&
		!empty($_POST['minute_start']) &&
		!empty($_POST['date_end']) &&
		!empty($_POST['hour_end']) &&
		!empty($_POST['minute_end'])
	) {
	
		if(
			preg_match($pattern_integer, $_POST['hour_start'], $hour_start) &&
			preg_match($pattern_date, $_POST['date_start'], $date_start) &&
			preg_match($pattern_integer, $_POST['minute_start'], $minute_start) &&
			preg_match($pattern_integer, $_POST['hour_end'], $hour_end) &&
			preg_match($pattern_integer, $_POST['minute_end'], $minute_end) &&
			preg_match($pattern_date, $_POST['date_end'], $date_end)
		) {
			$day_start = $date_start[1];
			$month_start = $date_start[2];
			$year_start = $date_start[3];
			$dateFormatedStart = $year_start."-".$month_start."-".$day_start." ".$_POST['hour_start'].":".$_POST['minute_start'].":00";

			$day_end = $date_end[1];
			$month_end = $date_end[2];
			$year_end = $date_end[3];
			$dateFormatedEnd = $year_end."-".$month_end."-".$day_end." ".$_POST['hour_end'].":".$_POST['minute_end'].":00";

			$messages = $redox->getMessages(0, $dateFormatedStart, $dateFormatedEnd);
		} else {
			$alert = "Le format de la date est incorrect !";
			$messages = $redox->getMessages(30);
		}
		
	} else {
		$alert = "Veuillez renseigner tous les champs !";
		$messages = $redox->getMessages(30);
	}
} else {
	$messages = $redox->getMessages(30);
}

$date_start = isset($_POST['date_start']) ? $_POST['date_start'] : "";
$hour_start = isset($_POST['hour_start']) ? $_POST['hour_start'] : 0;
$minute_start = isset($_POST['minute_start']) ? $_POST['minute_start'] : 0;

$date_end = isset($_POST['date_end']) ? $_POST['date_end'] : "";
$hour_end = isset($_POST['hour_end']) ? $_POST['hour_end'] : 0;
$minute_end = isset($_POST['minute_end']) ? $_POST['minute_end'] : 0;

?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>RedoxShout</title>

	<!-- Bootstrap -->
	<link href="static/css/bootstrap.min.css" rel="stylesheet">
	<style>
	body {
		padding-top: 50px;
	}
	.starter-template {
		padding: 40px 15px;
		text-align: center;
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
					<li class="active"><a href="#">Home</a></li>
				</ul>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="starter-template">
			<?php if(isset($alert)): ?>
				<div class="alert alert-danger" role="alert"><?php echo $alert; ?></div>
			<?php endif; ?>

			<form method="post" role="form" class="form-inline">
				<div class="form-group">
					<strong>Date début :</strong>
					<input type="text" class="form-control" name="date_start" placeholder="dd/mm/YY" value="<?php echo $date_start; ?>" />
					<select name="hour_start" class="form-control">
						<?php for($i = 0; $i <= 23; $i++): ?>
						<option<?php if($hour_start == $i) { echo " selected"; } ?>>
							<?php if($i < 10) { echo "0"; } echo $i; ?>
						</option>
						<?php endfor; ?>
					</select>
					<select name="minute_start" class="form-control">
						<?php for($i = 0; $i <= 59; $i++): ?>
						<option <?php if($minute_start == $i) { echo " selected"; } ?>><?php if($i < 10) { echo "0"; } echo $i; ?></option>
						<?php endfor; ?>
					</select>
				</div>
				<div class="form-group">
					<strong>Date fin :</strong>
					<input type="text" class="form-control" name="date_end" placeholder="dd/mm/YY" value="<?php echo $date_end; ?>" />
					<select name="hour_end" class="form-control">
						<?php for($i = 0; $i <= 23; $i++): ?>
						<option<?php if($hour_end == $i) { echo " selected"; } ?>>
							<?php if($i < 10) { echo "0"; } echo $i; ?>
						</option>
						<?php endfor; ?>
					</select>
					<select name="minute_end" class="form-control">
						<?php for($i = 0; $i <= 59; $i++): ?>
						<option <?php if($minute_end == $i) { echo " selected"; } ?>><?php if($i < 10) { echo "0"; } echo $i; ?></option>
						<?php endfor; ?>
					</select>
				</div>
				<input type="submit" value="Valider" class="btn btn-primary" />
			</form>
			
			<br />
			
			<?php if(isset($messages)): ?>
			<table class="table table-striped table-bordered table-hover">
				<tr>
					<th>Auteur</th>
					<th>Date</th>
					<th>Message</th>
				</tr>
				<?php 
				if(!empty($messages)):
				foreach($messages as $message): 
				?>
				<tr>
					<?php if($message['author'] == "Trash") {?>
					<td style="color:red;font-weight:bold;">Trash</td>
					<?php } else { ?>
					<td><?php echo htmlspecialchars($message['author']); ?></td>
					<?php }?>
					<td><?php echo htmlspecialchars($message['date']); ?></td>
					<td><?php echo htmlspecialchars($message['message']); ?></td>
				</tr>
				<?php 
				endforeach;
				endif;
				?>
			</table>
			<?php endif; ?>
		</div>
	</div>

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="js/bootstrap.min.js"></script>
</body>
