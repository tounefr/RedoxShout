<?php
error_reporting(0);
ini_set("display_errors", 0);

define('ROOT', $_SERVER['REQUEST_URI']);
require_once('./includes/config.php');
require_once('./includes/functions.php');
require_once('./class/redoxshout.class.php');

try {
	$redox = new RedoxShout(FORUM_USERNAME, FORUM_PASSWORD);
	
} catch(Exception $e) {
	exit($e->getMessage());
}

$page = isset($_GET['page']) ? $_GET['page'] : "search";
$page = preg_match("#[a-zA-Z]{0,}#", $page) ? $page : "search";

if(file_exists("views/$page.php")) {
	$page_isset = true;
} else {
	die(header("Location: ?page=error404"));
}


if(isset($page_isset)) {
	if(!strstr($page, "error"))
		include_once("./controllers/".$page."Controller.php");
	include_once("./views/includes/header.php");
	include_once("./views/$page.php");
	include_once("./views/includes/footer.php");
}