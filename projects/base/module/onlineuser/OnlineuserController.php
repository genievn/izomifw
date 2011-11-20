<?php
class OnlineuserController extends Object {		
	
	public function defaultCall(){		
		return $this->processOnlineUser();	
	}
	
	private function processOnlineUser(){		
		$online = object('OnlineAttrs');
		$online->setTimeout(config('onlineuser.time_out', 10));
		$online->setTimestamp(time());
		$online->setIp($this->ipCheck());
				
		$this->newOnlineUser($online);
		$this->deleteOnlineUser($online);

		$count = $this->countOnlineUser($online);		
		$render = $this->getTemplate('online_users');
		$render->setCount($count);
		return $render;
	}
	
	
	public function newOnlineUser($online){		
		$this->getManager('onlineuser')->newOnlineUser($online);
	}
	
	public function deleteOnlineUser($online){
		$this->getManager('onlineuser')->deleteOnlineUser($online);		
	}
	
	public function countOnlineUser($online){
		return $this->getManager('onlineuser')->countOnlineUser($online);
	}

	public function ipCheck(){
	/*
	This function will try to find out if user is coming behind proxy server. Why is this important?
	If you have high traffic web site, it might happen that you receive lot of traffic
	from the same proxy server (like AOL). In that case, the script would count them all as 1 user.
	This function tryes to get real IP address.
	Note that getenv() function doesn't work when PHP is running as ISAPI module
	*/
		if (getenv('HTTP_CLIENT_IP')) {
			$ip = getenv('HTTP_CLIENT_IP');
		}
		elseif (getenv('HTTP_X_FORWARDED_FOR')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		}
		elseif (getenv('HTTP_X_FORWARDED')) {
			$ip = getenv('HTTP_X_FORWARDED');
		}
		elseif (getenv('HTTP_FORWARDED_FOR')) {
			$ip = getenv('HTTP_FORWARDED_FOR');
		}
		elseif (getenv('HTTP_FORWARDED')) {
			$ip = getenv('HTTP_FORWARDED');
		}
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;		
	}	
}