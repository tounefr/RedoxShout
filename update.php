<?php

require_once('/home/redox/redoxshout/class/redoxshout.class.php');
require_once('/home/redox/redoxshout/config.php');


try {
	$redox = new RedoxShout(FORUM_USERNAME, FORUM_PASSWORD);
	$redox->refresh();
	sleep(25);
	$redox->refresh();
	
} catch(Exception $e) {
	exit("Erreur fatale : " . $e->getMessage());
}