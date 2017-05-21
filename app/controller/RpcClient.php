<?php

class RpcClient {
	var $server;
	var $clientKey;

	var $curly = array();
	var $id = 0;
	var $mh;

	function __construct(string $s, string $ck){
		$this->server = "https://" . $s . "/rpc/";
		$this->clientKey = $ck;
		$this->mh = curl_multi_init();
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

	public function add($server, $method, $urls, $options){
		$url = "https://" . $server . "/rpc/" . $method;
		$this->addCurlGet($url, array("key" => $this->clientKey, "args" => serialize(array($urls, $options))));
	}

	public function execute(){
		// data to be returned
		$result = array();
		
		// execute the handles
		$running = null;
		do {
			curl_multi_exec($this->mh, $running);
		} while($running > 0);
		
		
		// get content and remove handles
		foreach($this->curly as $id => $c) {
			$result[$id] = unserialize(curl_multi_getcontent($c));
			curl_multi_remove_handle($this->mh, $c);
		}

		foreach($result as $r){
			if(gettype($r) == "object"){
				if(get_class($r) == "Exception"){
					throw $r;
				}
			}
		}
		
		// all done
		curl_multi_close($this->mh);
		$this->id = 0;

		return $result;
	}

	private function addCurlGet($url, $opts = array(), $curlOpts = array()){
		$this->curly[$this->id] = curl_init();

		if(!empty($opts)){
			$url .= "?";
			foreach($opts as $name => $val){
				$url .= "&" . $name . "=" . urlencode($val);
			}
		}

		curl_setopt($this->curly[$this->id], CURLOPT_URL,            $url);
		curl_setopt($this->curly[$this->id], CURLOPT_HEADER,         0);
		curl_setopt($this->curly[$this->id], CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->curly[$this->id], CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($this->curly[$this->id], CURLOPT_MAXREDIRS , 5);
		curl_setopt($this->curly[$this->id], CURLOPT_CAINFO, STORAGE_PATH . "/cacert.pem");

		if(!empty($curlOpts)){
			curl_setopt_array($this->curly[$this->id], $curlOpts);
		}
		
		curl_multi_add_handle($this->mh, $this->curly[$this->id]);
		$this->id++;
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