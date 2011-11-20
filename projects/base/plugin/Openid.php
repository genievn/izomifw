<?php
/**
 * undocumented class
 *
 * @package default
 * @author user
 **/
class OpenidPlugin extends Object
{
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function preSite($action)
	{
		import('core.lib.openid.auth.*');
		import('core.lib.openid.auth.openid.*');
		import('core.lib.openid.auth.yadis.*');
		return $action;
	}
} // END class 
?>