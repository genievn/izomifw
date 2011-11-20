<?php
/**
 * @version $Id: CacheManager.php	
 * Cache Manager
 * @date 22-7-2008
 * @package izomi.admin.cache
 * @author Thanh H. Nguyen
 * @copyright Copyright (C) 2008 Thanh H. Nguyen. All rights reserved.
 * @email thanhnh@izomi.com	
 **/
define ('_PAGE_CACHE_', 'page');
define ('_MODULE_CACHE_', 'module');

class CacheManager extends Object
{
	private $cache = null;
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function save($action)
	{
		$cache = $this->getCache();		
		$cache->setCache_file($this->getCacheFilePathFromAction($action));
		$cache->setContent($action->getContent());
		$cache->save();
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function getCacheFolderPath()
	{
		$cachePath = config( 'root.cache_folder' ).DIRECTORY_SEPARATOR.str_replace( '.', DIRECTORY_SEPARATOR, config( 'root.host' ) ).DIRECTORY_SEPARATOR.config('root.current_lang').DIRECTORY_SEPARATOR.config('root.current_locale').DIRECTORY_SEPARATOR;
		if( !is_dir( $cachePath ) ) mkdir( $cachePath, 0777, true );
		return $cachePath;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function getCacheFilePathFromAction($action)
	{
		return config( 'root.abs' ).$this->getCacheFolderPath().$action->getModule().'.'.$action->getMethod().'.'.implode('_', $action->getParams()).'.'.$action->getCache_mode().'.html';
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function loadFromCacheFile($cacheFile)
	{
		return $this->getCache()->fetch($cacheFile);
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function getCache()
	{
		if (!$this->cache){
			$cacheObj = object('Cache');
			$cacheObj->setCache_path($this->getCacheFolderPath());
			$cacheObj->setRefresh_seconds(config('cache.page.refresh_seconds', 60));
			$this->cache = $cacheObj;
		}
		return $this->cache;
	}
	/**
	 * Check if cache is enable and available to get
	 * $action will be modified if a cache is found
	 * There is two mode for the cache: Page Cache || Module Cache
	 *
	 * @return void
	 * @author user
	 **/
	public function caching($action, $mode = _MODULE_CACHE_)
	{	
		//TODO: $action->getParams() may contain izAction object, so can not use implode('_',$action->getParams())
		//check if the $action is configured to be cache
		//if (!$cache_enabled = config("cache.{$mode}.{$action->getModule()}.{$action->getMethod()}.".implode('_',$action->getParams()), false)){			
			if (!($cache_enabled = config("cache.{$mode}.{$action->getModule()}.{$action->getMethod()}", false))){
				
				if (!($cache_enabled = config("cache.{$mode}.{$action->getModule()}",false))){
					
					if ($mode == _MODULE_CACHE_){
						
						$cache_enabled = config("cache.{$mode}.all", false); //temporary turn on module cache
					}else{
						$cache_enabled = config("cache.{$mode}.all", false); //temporary turn on page cache
					}					
				}
			}
		//}		
		//if cache is not enabled for the current action then return;
		//if ($cache_enabled) print_r(config("cache"));//echo 'hello'.$action->getModule().$action->getMethod();
		if (!($cache_enabled === true)){			
			if ($mode == _PAGE_CACHE_)	config(".cache.{$mode}.enabled", false);
			else config(".cache.{$mode}.{$action->getModule()}.{$action->getMethod()}.enabled", false);
			return false;
		}
		
		//find out if a cached version existed
		$cachePath = $this->getCacheFolderPath();
        //get absolute cache file name;
        $cacheFile = $this->getCacheFilePathFromAction($action);
        if (file_exists($cacheFile) and ((time()-filemtime($cacheFile)) < config( "cache.{$mode}.{$action->getModule()}.{$action->getMethod()}.refresh_seconds", 60 )))
        {
        	$action->setContent($this->getManager('cache')->loadFromCacheFile($cacheFile));
        	if ($mode == _PAGE_CACHE_){
	        	config(".cache.{$mode}.available", true);		//raise flag to alert izSite to render from cache 
	        	config(".cache.{$mode}.enabled", false);		//disable schedule to create cache        		
        	}else{
	        	config(".cache.{$mode}.{$action->getModule()}.{$action->getMethod()}.available", true);		//raise flag to alert izSite to render from cache 
	        	config(".cache.{$mode}.{$action->getModule()}.{$action->getMethod()}.enabled", false);		//disable schedule to create cache       		
        	}        	
        }else{
        	//cache is not availble, schedule IT!!!
        	if ($mode == _PAGE_CACHE_){
	        	config(".cache.{$mode}.available", false);		//turn off flag to indicate no cache available 
	        	config(".cache.{$mode}.enabled", true);			//enable schedule to create cache        		
        	}else{
	        	config(".cache.{$mode}.{$action->getModule()}.{$action->getMethod()}.available", false);		//turn off flag to indicate no cache available
	        	config(".cache.{$mode}.{$action->getModule()}.{$action->getMethod()}.enabled", true);		//enable schedule to create cache      
        	}
        }        
        return true;
	}		
}