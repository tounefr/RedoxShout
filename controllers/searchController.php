<?php 

$offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 1;
$date_start = isset($_GET['date_start']) ? $_GET['date_start'] : "";
$date_end = isset($_GET['date_end']) ? $_GET['date_end'] : "";
$author = isset($_GET['author']) ? $_GET['author'] : "";
$id =  isset($_GET['id']) ? (int) $_GET['id'] : 0;

try {
	if($id > 0) {
		$messages = $redox->getMessageById($id);
	} else {
		$messages = $redox->getMessages($offset, $date_start, $date_end, $author);
	}
	
} catch(Exception $e) {
	$alert = $e->getMessage();
}

if(!empty($author)) {
	$aboutAuthor = $redox->aboutAuthor($author);
}
else {
	$nbrPosts = $redox->nbrPosts();
}