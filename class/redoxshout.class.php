<?php
if(!defined('ROOT'))
	exit("Coucou toi !");
	
require_once('./class/redoxRequest.class.php');
require_once('./class/database.class.php');

class RedoxShout {
	private $request;
	private $database;
	private $url;
	
	public static $pattern_date = "#([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})#";
	public static $days = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Novembre", "Octobre", "Novembre", "Décembre");
	
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
		
		
		echo "author = " . $authors[0] . " message = " . $messages[0] . " date = " . $dates[0] . " hash = ". md5($authors[0].$messages[0].$dates[0]) ." <br />";

		/*
		echo "<pre>";
		print_r($authors);
		print_r($dates);
		print_r($messages);
		print_r($messagesParsed);
		//print_r($DOMDocument->saveHTML());
		echo "</pre>";
		
		*/

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
		$seconds = 12;
		return "$year-$month-$day $hour:$minute:$seconds";
	}
	
	public static function formatDateForDatabase($dateUnformated)
	{
		if(is_array($dateUnformated)) {
			$day = isset($dateUnformated['day']) ? (int) $dateUnformated['day'] : 00;
			$month = isset($dateUnformated['month']) ? (int) $dateUnformated['month'] : 00;
			$year = isset($dateUnformated['year']) ? (int) $dateUnformated['year'] : 0000;
			$hours = isset($dateUnformated['hours']) ? (int) $dateUnformated['hours'] : 00;
			$minutes = isset($dateUnformated['minutes']) ? (int) $dateUnformated['minutes'] : 00;
			$seconds = date('s', time());
			
		} else {
			if(!preg_match("#([0-9]{2})\-([0-9]{2})\-([0-9]{4})\-([0-9]{2})\-([0-9]{2})#", $dateUnformated, $matches))
				return null;
			
				
			$day = $matches[1];
			$month = $matches[2];
			$year = $matches[3];
			$hours = $matches[4];
			$minutes = $matches[5];
			$seconds = date('s', time());
		}
		
		return "$year/$month/$day $hours:$minutes:$seconds";
	}
	
	public function aboutAuthor($name) {
		if(!is_string($name)) {
			return;
		}
		
		return $this->database->aboutAuthor($name);
	}
	
	public function nbrPosts() {
		return $this->database->nbrPosts();
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
	
	public function getMessages($page = 1, $date_start = 0, $date_end = 0, $author = "") {
		if(!empty($date_start)) {
			$date_start = self::formatDateForDatabase($date_start);
			if(empty($date_start))
				throw new Exception("Le format de la date de début est incorrect !"); 
		}
		
		if(!empty($date_end)) {
			$date_end = self::formatDateForDatabase($date_end);
			if(empty($date_end))
				throw new Exception("Le format de la date de fin est incorrect !");
		}

		if((int) $page <= 0) {
			$page = 1;
		} else {
			$page--;
		}
		
		$offset = $page * 30;

		if(!is_string($author))
			$author = "";
		
		$messages = $this->database->getMessages($offset, $date_start, $date_end, $author);
		if(empty($messages))
			throw new Exception("Aucun messages !");
		else
			return $messages;
	}
	
	public function getMessageById($id) {
		$id = (int) $id;
		return $this->database->getMessageById($id);
	}
	
	public function getTopFiftyCurrentMonth() {
		$dateStart = self::formatDateForDatabase(array(
			'year' => date('Y', time()),
			'month' => date('m', time()) - 1,
			'day' => 01 
		));
		$dateEnd = self::formatDateForDatabase(array(
			'year' => date('Y', time()),
			'month' => date('m', time()),
			'day' => 01
		));
		
		return $this->database->getTop($dateStart, $dateEnd);
	}
	
	public function getTopAnytime() {
		$dateStart = self::formatDateForDatabase(array());
		$dateEnd = self::formatDateForDatabase(array(
			'year' => date('Y', time()),
			'month' => date('m', time()),
			'day' => date('d', time()),
			'hours' => date('h', time()),
			'minutes' => date('i', time())
		));
		
		return $this->database->getTop($dateStart, $dateEnd);
	}

	public function saveDb($messagesParsed) {
			
		$lastMessages = $this->database->getLastMessages(30);		
		for($i = 0; $i < count($messagesParsed); $i++) {
			$messageParsed = $messagesParsed[$i];

			if(!empty($lastMessages)) {
				$lastMessage = $lastMessages[$i];
				
				if(!$this->database->exist($messageParsed)) {
					$this->database->addMessage($messageParsed);
					echo "<p>Ajouté :</p>";
					echo "<pre>";
					print_r($messageParsed);
					echo "</pre>";
				} else {
					echo "<p>Déjà ajouté !</p>";
					echo "<pre>";
					print_r($messageParsed);
					echo "</pre>";
				}
			} else {
				$this->database->addMessage($messageParsed);
				echo "<p>Vide et Ajouté !</p>";
			}
		}
	}
}