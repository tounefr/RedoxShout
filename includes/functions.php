<?php
function generateUrl(array $queryArray = array()) {
	foreach($queryArray as $key => $value) {
		$_GET[$key] = $value;
	}
	
	return $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET);
}

function debug($object) {
	echo "<pre>";
	print_r($object);
	echo "</pre>";
}

function secureXSS($var) {
	return htmlspecialchars($var);
}