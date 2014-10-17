<h3 id="top_currentMonth">Top 50 mois courant (<?php echo RedoxShout::$days[date('n', time())]; ?>)</h3>
<table class="table table-striped">
	<tr>
		<th>Position</th>
		<th>Pseudo</th>
		<th>Nombre de shouts</th>
	</tr>
	
	<?php 
	if(count($topFifty) > 0):
		$pos = 1;
		foreach($topFifty as $member):
	?>
		<tr>
			<td><?php echo $pos; ?></td>
			<td><?php echo "<a href='index.php?author=".secureXSS($member['author'])."'>".secureXSS($member['author'])."</a>"; ?></td>
			<td><?php echo $member['count']; ?></td>
		</tr>
	<?php 
		$pos++;
		endforeach;
	endif;
	?>
	
</table>

<h3 id="top_anytime">Top 50 général (depuis le 7 juillet 2014 15h07)</h3>
<table class="table table-striped">
	<tr>
		<th>Position</th>
		<th>Pseudo</th>
		<th>Nombre de shouts</th>
	</tr>
	
	<?php 
	if(count($topAnyTime) > 0):
		$pos = 1;
		foreach($topAnyTime as $member):
	?>
		<tr>
			<td><?php echo $pos; ?></td>
			<td><?php echo "<a href='index.php?author=".secureXSS($member['author'])."'>".secureXSS($member['author'])."</a>"; ?></td>
			<td><?php echo $member['count']; ?></td>
		</tr>
	<?php 
		$pos++;
		endforeach;
	endif;
	?>
	
</table>