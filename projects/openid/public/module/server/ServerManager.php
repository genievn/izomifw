<?php
/**
 * undocumented class
 *
 * @package default
 * @author user
 **/

class ServerManager extends Object
{
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function getServer($store, $op_endpoint)
	{
		static $server = null;
		if(!isset($server)){
			$server = & new Auth_OpenID_Server($store, $op_endpoint);
		}
		return $server;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function getLoggedInUser()
	{
		return isset($_SESSION['openid_url']) ? $_SESSION['openid_url'] : false;
	}
	
	public function setRequestInfo($info=null)
	{
		if (!isset($info)){
			unset($_SESSION['request']);
		}else{
			$_SESSION['request'] = serialize($info);
		}
	}
	
	public function getRequestInfo()
	{
		return isset($_SESSION['request'])
			? unserialize($_SESSION['request'])
			: false;
	}
	
	/**
	 * Set the openid_url in the session
	 *
	 * @param string $identity_url 
	 * @return void
	 * @author Thanh H. Nguyen
	 */
	public function setLoggedInUser($identity_url=null)
	{
		if (!isset($identity_url)) {
			unset($_SESSION['openid_url']);
		} else {
			$_SESSION['openid_url'] = $identity_url;
		}
	}
	
} // END class 
?>