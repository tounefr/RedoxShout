<?php
require_once('/home/redox/redoxshout/class/redoxRequest.class.php');
require_once('/home/redox/redoxshout/class/database.class.php');

class RedoxShout {
	private $request;
	private $database;
	private $url;
	
	public function __construct($login, $pass)	{
		$this->database = new Database();
		$this->request = new RedoxRequest($login, $pass);
	}
	
	public function parseMessages($html) {
		$messagesParsed = array();
		$authors = array();
		$dates = array();
		$messages = array();
		
		$html = "
			<!DOCTYPE HTML>
			<html>
				<head>
					<meta charset='UTF-8' />
				</head>
			<body>
				$html;
			</body>
			</html>
		";
		
		$DOMDocument = new DOMDocument();
		$DOMDocument->loadHTML($html);

		// Parsing authors
		$spans = $DOMDocument->getElementsByTagName('span');
		for($i2 = 0; $i2 < $spans->length; $i2++) {
			$span = $spans->item($i2);
			$styleAttribute = $span->attributes->getNamedItem("style");
			if(!empty($styleAttribute)) {
				$firstChild = $span->firstChild;
				if($firstChild->nodeName == "strong" || $firstChild->nodeName == "b") {
					array_push($authors, $firstChild->nodeValue);
				}
			}
		}
		// Parsing dates
		for($i3 = 0; $i3 < $spans->length; $i3++) {
			$span = $spans->item($i3);
			$classAttribute = $span->attributes->getNamedItem('class');
			$titleAttribute = $span->attributes->getNamedItem('title');
			
			if(!empty($classAttribute) && !empty($titleAttribute)) {
				if($classAttribute->nodeValue == "right desc") {
					array_push($dates, $span->nodeValue);
				}
			}
		}
		
		// Parsing messages
		for($i4 = 0; $i4 < $spans->length; $i4++) {
			$span = $spans->item($i4);
			$classAttribute = $span->attributes->getNamedItem('class');
			if(!empty($classAttribute) && $classAttribute->nodeValue == "shoutbox_text") {
				array_push($messages, $span->nodeValue);
			}
		}

		for($i = 0; $i < 30; $i++) {
			$messagesParsed[$i]["author"] = $authors[$i];
			
			$date = self::getDate($dates[$i]);
			$messagesParsed[$i]["date"] = self::formatDate($date['h'], $date['m']);
			$messagesParsed[$i]["message"] = $messages[$i];
		}

		return $messagesParsed;
	}
	
	public static function getDate($date) {
		if(preg_match("#\(([0-9]{1,2}):([0-9]{1,2})\)#", $date, $matches)) {
			$date = array('h' => $matches[1], 'm' => $matches[2]);
			return $date;
		}
	}
	
	public static function formatDate($hour, $minute, $year = 0, $month = 0, $day = 0) {
		if(empty($day) && empty($month) && empty($year)) {
			$day = date('d', time());
			$month = date('m', time());
			$year = date('Y', time());
		}
		$seconds = date('s', time());
		return "$year/$month/$day $hour:$minute:$seconds";
	}
	
	public function refresh() {
		$this->request->connection();
		$this->request->getInfosShout();
		$this->saveDb($this->parseMessages($this->request->getShout()));
	}
	
	public static function getTimestamp($date) {
		if(preg_match("#\(([0-9]{2}):([0-9]{2})\)#", $date, $matches)) {
			return mktime($matches[1], $matches[2], 0, date('m', time()), date('Y', time()));
		}
	}
	
	public function getMessages($offset = 0, $dateFormatedStart = 0, $dateFormatedEnd = 0) {
		return $this->database->getMessages($offset, $dateFormatedStart, $dateFormatedEnd);
	}
	
	public function saveDb($messagesParsed) {
			
		$lastMessages = $this->database->getLastMessages(30);		
		for($i = 0; $i < count($messagesParsed); $i++) {
			$messageParsed = $messagesParsed[$i];

			if(!empty($lastMessages)) {
				$lastMessage = $lastMessages[$i];
				
				if(!$this->database->exist($messageParsed)) {
					$this->database->addMessage($messageParsed);
				}
			} else {
				$this->database->addMessage($messageParsed);
			}
		}
	}
}