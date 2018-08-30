<?php
	/* File : class.Rest.php
	 * Author : Arun Kumar Sekar
	 * Edited by: Srishu Indrakanti
	*/
	class RESTBase {
		public $httpReferer;
		public $httpStatusArray=array();
		public $httpStatusCode = 200;
		public $contentType = "application/json";
			
		public function __construct() {
			$this->httpStatusArray = array(
				100 => 'Continue',  
				101 => 'Switching Protocols',  
				200 => 'OK',
				201 => 'Created',  
				202 => 'Accepted',  
				203 => 'Non-Authoritative Information',  
				204 => 'No Content',  
				205 => 'Reset Content',  
				206 => 'Partial Content',  
				300 => 'Multiple Choices',  
				301 => 'Moved Permanently',  
				302 => 'Found',  
				303 => 'See Other',  
				304 => 'Not Modified',  
				305 => 'Use Proxy',  
				306 => '(Unused)',  
				307 => 'Temporary Redirect',  
				400 => 'Bad Request',  
				401 => 'Unauthorized',  
				402 => 'Payment Required',  
				403 => 'Forbidden',  
				404 => 'Not Found',  
				405 => 'Method Not Allowed',  
				406 => 'Not Acceptable',
				407 => 'Proxy Authentication Required',  
				408 => 'Request Timeout',  
				409 => 'Conflict',  
				410 => 'Gone',  
				411 => 'Length Required',  
				412 => 'Precondition Failed',  
				413 => 'Request Entity Too Large',  
				414 => 'Request-URI Too Long',  
				415 => 'Unsupported Media Type',  
				416 => 'Requested Range Not Satisfiable',  
				417 => 'Expectation Failed',  
				500 => 'Internal Server Error',  
				501 => 'Not Implemented',  
				502 => 'Bad Gateway',  
				503 => 'Service Unavailable',  
				504 => 'Gateway Timeout',  
				505 => 'HTTP Version Not Supported');
		}
		
		private function getStatusMessage() {
			return ($this->httpStatusArray[$this->httpStatusCode])?$httpStatusArray[$this->httpStatusCode]:$httpStatusArray[500];
		}
			
		private function cleanInputs($data){
			$clean_input = array();
			if(is_array($data)){
				foreach($data as $k => $v){
					$clean_input[$k] = $this->cleanInputs($v);
				}
			}else{
				if(get_magic_quotes_gpc()){
					$data = trim(stripslashes($data));
				}
				$data = strip_tags($data);
				$clean_input = trim($data);
			}
			return $clean_input;
		}
			
		private function setHeaders($httpStatusCode) {
			$this->httpStatusCode = ($httpStatusCode)?$httpStatusCode:200;
			header("HTTP/1.1 ".$this->httpStatusCode." ".$this->getStatusMessage());
			header("Content-Type:".$this->contentType);
		}
	
		public function respondToCallerAndReturn($statusValue,$messageValue,$httpStatusCode) {
			$this->setHeaders($httpStatusCode);
			$returnArray=array();
			$returnArray["Status"]=$statusValue;
			$returnArray["Message"]=$messageValue;
			echo json_encode($returnArray);
			exit();
		}
	}