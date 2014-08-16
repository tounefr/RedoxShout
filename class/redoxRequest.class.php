<?php
require_once('/home/redox/redoxshout/class/request.class.php');

class RedoxRequest extends Request {
	
	private $request;
	private $tokenConnection;
	private $login;
	private $pass;
	private $shout_hash;
	private $shout_sessid;
	
	public function __construct($login, $pass) {
		$this->login = $login;
		$this->pass = $pass;
		parent::__construct();
	}
	
	public function getTokenConnection() {
		if(!empty($tokenConnection)) {
			throw new Exception("Vous êtes déjà connecté !");
		}
		
		$this->url = "http://forum.redoxbot.net/index.php?app=core&module=global&section=login";
		$html = parent::get();

		$DOMDocument = new DOMDocument();
		@$DOMDocument->loadHTML($html);
		$elements = $DOMDocument->getElementsByTagName('input');
		for($i = 0; $i < $elements->length; $i++) {
			$element = $elements->item($i);
			$attributes = $element->attributes;
			if($element->nodeName == "input") {
				$name = @$attributes->getNamedItem('name')->nodeValue == "auth_key";
				$value = @$attributes->getNamedItem('value')->nodeValue;
				if($name && !empty($value)) {
					$this->tokenConnection = $value;
					return $this->tokenConnection;
				}
			}
		}
	}
	
	public function getShoutHash() {
		return $this->shout_hash;
	}
	
	public function getShoutSessid() {
		return $this->shout_sessid;
	}
	
	public function connection() {
		$this->getTokenConnection();
		$this->url = "http://forum.redoxbot.net/index.php?app=core&module=global&section=login&do=process";
		$postfields = array(
			"auth_key" => $this->tokenConnection,
			"referer" => "http://forum.redoxbot.net/index.php?app=core&module=global&section=login",
			"ips_username" => $this->login,
			"ips_password" => $this->pass,
			"rememberMe" => 1
		);		
		$html = $this->post($postfields);
		return $html;
	}
	
	public function getShout() {
		$this->url = "http://forum.redoxbot.net/index.php?s=".$this->getShoutSessid()."&&app=shoutbox&module=ajax&section=coreAjax&secure_key=".$this->getShoutHash()."&type=getShouts&lastid=-1&global=1";
		return $this->get();
	}
	
	public function getInfosShout() {
		$this->url = "http://forum.redoxbot.net/index.php";
		$html = $this->get();

		preg_match_all("#ipb\.vars\['secure_hash'\] 		= '(.*)';#", $html, $matchesHash);
		preg_match_all("#ipb\.vars\['session_id'\]			= '(.*)';#", $html, $matchesSessId);
		
		$this->shout_hash = $matchesHash[1][0];
		$this->shout_sessid = $matchesSessId[1][0];
	}
	
}