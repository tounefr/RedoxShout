<?php
if(!defined('ROOT'))
	exit("Coucou toi !");

require_once('./includes/config.php');
require_once('./class/redoxshout.class.php');

class Database {
	
	private $pdo = null;
	
	public function __construct() {
		$this->pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_LOGIN, DB_PASS);
		$this->pdo->exec('SET CHARACTER SET utf8');
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	public function addMessage(array $message) {
		
		$req = $this->pdo->prepare('INSERT INTO shoutbox(author, date, message, checksum) VALUES(:author, :date, :message, MD5(CONCAT(author,date,message)))');
		$req->execute(array(
			'author' => $message['author'],
			'date' => $message['date'],
			'message' => $message['message']
		));
	}
	
	public function getMessages($offset = 0, $dateFormatedStart = 0, $dateFormatedEnd = 0, $author = "") {
	
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
		$sql.= " LIMIT $offset,30";
		
		$req = $this->pdo->prepare($sql);
		
		if(!empty($author))
			$req->bindValue(':author', $author);
		if(!empty($dateFormatedStart))
			$req->bindValue(':date_start', $dateFormatedStart);
		if(!empty($dateFormatedEnd))
			$req->bindValue(':date_end', $dateFormatedEnd);
		
		$req->execute();
		return $req->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function getMessageById($id) {
		$id = (int) $id;
		$req = $this->pdo->prepare('SELECT * FROM shoutbox WHERE id = :id');
		$req->bindValue(':id', $id, PDO::PARAM_INT);
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
		$req = $this->pdo->prepare('SELECT * FROM shoutbox WHERE checksum = MD5(CONCAT(:author,:date,:message))');
		$req->bindValue(':author', $message['author']);
		$req->bindValue(':date', $message['date']);
		$req->bindValue(':message', $message['message']);
		$req->execute();
		$data = $req->fetch(PDO::FETCH_ASSOC);
		echo $data['checksum'];
		if($req->rowCount() > 0)
			return true;
		else
			return false;		
	}
	
	public function getTop($dateStart, $dateEnd) {
		$req = $this->pdo->prepare('SELECT *, COUNT(*) AS count FROM shoutbox WHERE date BETWEEN :date_start AND :date_end GROUP BY author ORDER BY COUNT DESC LIMIT 0,50');
		$req->bindValue(':date_start', $dateStart);
		$req->bindValue(':date_end', $dateEnd);
		$req->execute();
		return $req->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function nbrPosts() {
		$req = $this->pdo->query('SELECT COUNT(*) AS count FROM shoutbox');
		return $req->fetch(PDO::FETCH_ASSOC)["count"];
	}
	
	public function aboutAuthor($name) {
		$req = $this->pdo->prepare('SELECT *, COUNT(*) as nbr_posts FROM shoutbox WHERE author = :author');
		$req->bindValue(':author', $name);
		$req->execute();
		return $req->fetch(PDO::FETCH_ASSOC);
	}
}