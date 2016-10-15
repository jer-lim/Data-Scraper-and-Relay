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
		$curler = new Curler();
		$result = unserialize($curler->get($url, array("key" => $this->clientKey, "args" => serialize($args))));
		if(gettype($result) == "object"){
			if(get_class($result) == "Exception"){
				throw $result;
			}
		}else{
			return $result;
		}
	}
}