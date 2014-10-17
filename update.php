<?php
define('ROOT', '');
require_once('./class/redoxshout.class.php');
require_once('./includes/config.php');


try {
	$redox = new RedoxShout(FORUM_USERNAME, FORUM_PASSWORD);
	$redox->refresh();
	sleep(5);
	$redox->refresh();
	sleep(5);
	$redox->refresh();
	
} catch(Exception $e) {
	exit("Erreur fatale : " . $e->getMessage());
}