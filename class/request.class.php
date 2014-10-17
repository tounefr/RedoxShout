<?php
if(!defined('ROOT'))
	exit("Coucou toi !");
	
abstract class Request {

	protected $curl;
	protected $curl_options;
	protected $curl_html;
	public $url;
	protected $headers; // array
	protected $cookies; // array
	
	public function __construct($url = null, $headers = array(), $cookies = array()) {
		$this->curl = curl_init();
		$this->url = $url;
		$this->headers = $headers;
		$this->cookies = $cookies;
	}
	
	public function getInfos() {
		return curl_getinfo($this->curl, CURLINFO_HEADER_OUT);
	}
	
	public function setCookie($key, $value) {
		$this->cookies[$key] = $value;
	}
	
	public function setHeader($key, $value) {
		$this->headers[$key] = $value;
	}
	
	public static function arrayToStringCookies(array $cookies) {
		$cookie_string = "";
		foreach($cookies as $key => $value) {
			$cookie_string.= $key."=".$value.";";
		}
		return $cookie_string;		
	}
	
	// from array
	public function formatHeaders($headers) {
		$cookie_array = array();
		$cookie_string = "";
		foreach($headers as $key => $value) {
			$cookie_string.= $key.": ".$value;
			array_push($cookie_array, $cookie_string);
		}
		return $cookie_array;
	}
	
	public function initRequest() {
		$this->curl_options[CURLOPT_URL] = $this->url;
		$this->curl_options[CURLOPT_RETURNTRANSFER] = true;
		$this->curl_options[CURLINFO_HEADER_OUT] = true;
		//$this->curl_options[CURLOPT_HEADER] = true;
		$this->curl_options[CURLOPT_USERAGENT] = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36";
		$this->curl_options[CURLOPT_FOLLOWLOCATION] = true;
		//$this->curl_options[CURLOPT_COOKIESESSION] = true;
		$this->curl_options[CURLOPT_COOKIEJAR] = ROOT . "/cron/cookies.txt";
		$this->curl_options[CURLOPT_COOKIEFILE] = ROOT . "/cron/cookies.txt";
		//$this->curl_options[CURLOPT_HTTPHEADER] = $this->formatHeaders($this->headers);
		curl_setopt_array($this->curl, $this->curl_options);
	}
	
	public function get() {
		$this->initRequest();
		$this->curl_html = curl_exec($this->curl);
		return $this->curl_html;
	}
	
	public function post($postfields) {
		$this->curl_options[CURLOPT_POST] = true;
		$this->curl_options[CURLOPT_POSTFIELDS] = $postfields;
		$this->initRequest();
		$this->curl_html = curl_exec($this->curl);
		return $this->curl_html;
	}
	
	public function setUrl($url) {
		$this->url = $url;
		$this->curl_options[CURLOPT_URL] = $this->url;
	}
	
	public function __destruct() {
		curl_close($this->curl);
	}
	
}