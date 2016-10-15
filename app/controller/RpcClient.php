<?php

class RpcClient {
	var $server;
	var $clientKey;

	function __construct(string $s, string $ck){
		$this->server = "http://" . $s . "/rpc/";
		$this->clientKey = $ck;
	}

	function __call($method, $args){
		set_time_limit(60);
		$url = $this->server . $method;
		$result = unserialize($this->curlGet($url, array("key" => $this->clientKey, "args" => serialize($args))));
		if(gettype($result) == "object"){
			if(get_class($result) == "Exception"){
				throw $result;
			}
		}else{
			return $result;
		}
	}

	private function curlGet($url, $opts = array(), $curlOpts = array()){
		  // data to be returned
		$result = array();
		
		$id = 0;
		$curly = curl_init();

		if(!empty($opts)){
			$url .= "?";
			foreach($opts as $name => $val){
				$url .= "&" . $name . "=" . urlencode($val);
			}
		}
		
		curl_setopt($curly, CURLOPT_URL,            $url);
		curl_setopt($curly, CURLOPT_HEADER,         0);
		curl_setopt($curly, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curly, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($curly, CURLOPT_MAXREDIRS , 5);
		curl_setopt($curly, CURLOPT_CAINFO, STORAGE_PATH . "/cacert.pem");

		if(!empty($curlOpts)){
			curl_setopt_array($curly, $curlOpts);
		}
		
		// execute the handles
		$result=curl_exec($curly);

		return $result;
	}
}