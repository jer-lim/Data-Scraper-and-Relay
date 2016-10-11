<?php

class RpcServer {
	var $allowedKeys = array();

	function __construct(array $keys = null){
		if($keys != null){
			$this->allowedKeys = $keys;
		}
	}

	function addKey($key){
		array_push($this->allowedKeys, $key);
	}

	function handleRequest($key, $method, $args){
		if(in_array($key, $this->allowedKeys)){
			if(empty($args)){
				$result = call_user_func(array($this, $method));
			}else{
				$result = call_user_func_array(array($this, $method), $args);
			}

			echo serialize($result);
		}else{
			echo serialize(new Exception("Invalid key"));
		}
	}

	private function multiGet($input, $curlOptions){
		$curler = new Curler();
		foreach($input as $in){
			$curler->addGet($in['url'], $in['options'], $curlOptions);
		}
		return $curler->execute();
	}
}