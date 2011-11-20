<?php
/**
 * undocumented class
 *
 * @package default
 * @author user
 **/
class ConsumerManager extends Object
{
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function &getStore()
	{
		/**
		* This is where the example will store its OpenID information.
		* You should change this path if you want the example store to be
		* created elsewhere.  After you're done playing with the example
		* script, you'll have to remove this directory manually.
		*/
		$store_path = config('openid.consumer.store_path');
		if (!file_exists($store_path) && !mkdir($store_path)) {
        	print "Could not create the FileStore directory '$store_path'. "." Please check the effective permissions.";
			exit(0);
		}
		
		return new Auth_OpenID_FileStore($store_path);
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function &getConsumer()
	{
		/**
		* Create a consumer object using the store object created
		* earlier.
		*/
		$store = $this->getStore();
		$consumer =& new Auth_OpenID_Consumer($store);
		return $consumer;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function getReturnTo()
	{
		return sprintf("%s://%s:%s%s/finish_auth.php",
					getScheme(), $_SERVER['SERVER_NAME'],
					$_SERVER['SERVER_PORT'],
					dirname($_SERVER['PHP_SELF']));
	}
} // END class 
?>