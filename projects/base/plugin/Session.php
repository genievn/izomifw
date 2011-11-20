<?php
class SessionPlugin extends Object {
	
	public function startUp($action){		
		if (isset($_REQUEST["PHPSESSID"]))
        {
            session_id($_REQUEST["PHPSESSID"]);
            session_start();
        }
	}
}
?>