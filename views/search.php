<div class="jumbotron">
	<form method="get" role="form">
		<div class="form-inline">
			<div class="form-group">
				<label for="author">Auteur : </label>
					<input type="text" style="width:240px;margin:auto;" name="author" class="form-control" placeholder="Ex: Trash" value="<?php echo secureXSS($author); ?>" />
			</div>
			
			<br /><br />
			<div class="form-group">
				<label for="">Date début : </label>
					<div class="form-group">
						<div class="input-group date" id="datetimepickerstart">
							<input type="text" class="form-control" name="date_start" data-date-format="DD-MM-YYYY-hh-mm" value="<?php echo $date_start; ?>" />
							<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
			</div>
			<div class="form-group">
				<label for="">Date fin : </label>
					<div class="form-group">
						<div class="input-group date" id="datetimepickerend">
							<input type="text" class="form-control" name="date_end" data-date-format="DD-MM-YYYY-hh-mm" value="<?php echo $date_end; ?>" />
							<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
			</div>
			<input type="submit" value="Valider" class="btn btn-primary" />
		</div>
	</form>
</div>

<?php if(isset($alert)) { ?>
	<div class="alert alert-danger" role="alert"><?php echo $alert; ?></div>
<?php 
	} else { 
		if(count($messages) > 1) {
			if(isset($nbrPosts)) {
				echo "<strong style='margin-bottom:20px;display:block;'>$nbrPosts shouts ont été postés.</strong>";
			}
		}
	}
?>

<?php if(isset($messages)): ?>
<ul class="pagination">
	<?php if($offset > 1): ?>
	<li class="previous"><a href="<?php echo generateUrl(array("offset" => $offset - 1)); ?>">&larr; Précédent</a></li>
	<?php endif; 
	if(count($messages) > 1):
	?>
	<li class="next"><a href="<?php echo generateUrl(array("offset" => $offset + 1)); ?>">Suivant &rarr;</a></li>
	<?php endif; ?>
</ul>

<?php 
if(isset($aboutAuthor)) {
	echo "<strong style='margin-bottom:20px;display:block;'>Ce membre a posté au total ".$aboutAuthor['nbr_posts']." shouts.</strong>";
}
?>

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
		<td><a href="?author=Trash" style="color:red;font-weight:bold;">Trash</a></td>
		<?php } else { ?>
		<td><a href="?author=<?php echo secureXSS($message['author']); ?>"><?php echo secureXSS($message['author']); ?></a></td>
		<?php }?>
		<td><?php echo secureXSS($message['date']); ?></td>
		<td><a href="index.php?page=search&id=<?php echo $message['id']; ?>" class="td_message"><?php echo secureXSS($message['message']); ?></a></td>
	</tr>
	<?php 
	endforeach;
	endif;
	?>
</table>

<?php endif; ?>