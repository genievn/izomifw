<?php
class OnlineuserManager extends izManager {	
    public function newOnlineUser($online){    	
    	$onlineuser = object('OnlineUser');
		$onlineuser->setTimestamp($online->getTimestamp());
		$onlineuser->setIp($online->getIp());
		$this->getWriter()->insertOnlineuser($onlineuser);		
    }
    public function deleteOnlineUser($online){
    	$query = "DELETE FROM onlineusers WHERE timestamp < ".($online->getTimestamp() - $online->getTimeout());    	
    	$this->getWriter()->execute($query);
    }
    public function countOnlineUser($online){
    	$query = "SELECT DISTINCT ip FROM onlineusers";
    	return $this->getReader()->query($query, true);    	
    }    
    
}
?>