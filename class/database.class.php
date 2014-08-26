<?php
require_once('/home/redox/redoxshout/config.php');
require_once('/home/redox/redoxshout/class/redoxshout.class.php');

class Database {
	
	private $pdo = null;
	
	public function __construct() {
		$this->pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_LOGIN, DB_PASS);
		$this->pdo->exec('SET CHARACTER SET utf8');
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	public function addMessage(array $message) {
		
		$req = $this->pdo->prepare('INSERT INTO shoutbox(author, date, message, author_sha1, message_sha1) VALUES(:author, :date, :message, :author_sha1, :message_sha1)');
		$req->execute(array(
			'author' => $message['author'],
			'date' => $message['date'],
			'message' => $message['message'],
			'author_sha1' => sha1($message['author']),
			'message_sha1' => sha1($message['message'])
		));
	}
	
	public function getMessages($limit = 0, $dateFormatedStart = 0, $dateFormatedEnd = 0, $author = "") {
	
		$sql = "SELECT * FROM shoutbox";
		
		if(!empty($author) && !empty($dateFormatedStart) && !empty($dateFormatedEnd)) {
			$sql.= " WHERE author = :author AND date BETWEEN :date_start AND :date_end";
			
		} else {
			if(!empty($author) && empty($dateFormatedStart) && empty($dateFormatedEnd)) {
				$sql.= " WHERE author = :author";
			}
			else if(empty($author) && !empty($dateFormatedStart) && !empty($dateFormatedEnd)) {
				$sql.= " WHERE date BETWEEN :date_start AND :date_end";
			}
		}
		
		$sql.= " ORDER BY id DESC";
		
		if(!empty($limit))
			$sql.= " LIMIT 0,:limit";
		
		$req = $this->pdo->prepare($sql);
		
		if(!empty($author))
			$req->bindValue(':author', $author);
		if(!empty($dateFormatedStart))
			$req->bindValue(':date_start', $dateFormatedStart);
		if(!empty($dateFormatedEnd))
			$req->bindValue(':date_end', $dateFormatedEnd);
		if(!empty($limit))
			$req->bindValue(':limit', $limit, PDO::PARAM_INT);
		
		
		$req->execute();
		return $req->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function getLastMessages($offset) {
		if(!preg_match("#[0-9]{1,}#", $offset)) {
			return;
		}
		
		$req = $this->pdo->prepare('SELECT * FROM shoutbox LIMIT 0,:offset');
		$req->bindValue(':offset', $offset, PDO::PARAM_INT);
		$req->execute();
		$data = $req->fetchAll(PDO::FETCH_ASSOC);
		
		return $data;
	}
	
	public function exist(array $message) {
		$req = $this->pdo->prepare('SELECT * FROM shoutbox WHERE author_sha1 = :author_sha1 AND message_sha1 = :message_sha1');
		$req->bindValue(':author_sha1', sha1($message['author']));
		$req->bindValue(':message_sha1', sha1($message['message']));
		$req->execute();
		$data = $req->fetch(PDO::FETCH_ASSOC);
		
		if($req->rowCount() > 0)
			return true;
		else
			return false;		
	}
}
