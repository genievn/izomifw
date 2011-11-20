<?php
/**
 * undocumented class
 *
 * @package default
 * @author user
 **/
class OpenidManager extends Object
{
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function import()
	{
		import('core.lib.openid.Auth.*');
		import('core.lib.openid.Auth.OpenID.*');
		import('core.lib.openid.Yadis.*');
		return true;
	}
	
	
} // END class 
?>