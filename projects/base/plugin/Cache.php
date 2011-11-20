<?php
/**
 * @version $Id: CachePlugin.php	
 * Cache Plugin
 * @date 23-7-2008
 * @package izomi.admin.cache
 * @author Thanh H. Nguyen
 * @copyright Copyright (C) 2008 Thanh H. Nguyen. All rights reserved.
 * @email thanhnh@izomi.com	
 **/
class CachePlugin extends Object
{
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function preSite($action)
	{
		//for performance purpose, the page caching has been put into the processuri plugin
	}
	/**
	 * Fire to check cache for a module (iz:insert:module)
	 *
	 * @return void
	 * @author user
	 **/
	public function preDispatch($action)
	{
		//TODO: because the checkCache has been called in Processuri plugin, so if the page (also means $action) is enabled for cache
		//		but not yet done (only when shutdown event is fired), then the caching function will be called more than one
		//		(ONE here, and ONE in shutdown event)
		
		
		// Check for cache & modify $action (in checkCache() method) to use cache if available
		$action->setCache_mode('module');
        $blnCache = $this->getManager( 'cache' )->caching($action, $mode="module");
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function postDispatch($action)
	{			
		if ( config ( "cache.module.{$action->getModule()}.{$action->getMethod()}.enabled" ) == true ){			
			$action->setCache_mode('module');
			return $this->getManager('cache')->save($action);
		}
	}
	/**
	 * 
	 *
	 * @return void
	 * @author user
	 **/
	public function shutDown($action)
	{
		//all the final HTML output is available now
		//check to see if the cache is enabled for the current $action
		if ( config( 'cache.page.enabled' ) == true ){
			$action->setCache_mode('page');
			return $this->getManager('cache')->save($action);
		}		
	}
	
} // END class 
?>