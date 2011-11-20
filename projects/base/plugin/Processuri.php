<?php
class ProcessuriPlugin extends Object
{
	public function preSite( $action )
	{
        $this->getManager( 'processuri' )->readUrl();
        $this->getManager( 'processuri' )->readUrlHost();
        $this->getManager( 'processuri' )->readRequestString();
        $this->getManager( 'processuri' )->createUri();
        
        // After this we have got the virtual domain (if yes)
        // Now we have all Config values reset the Folder Locale
        // setSiteLocale is a method defined in izSite
        izlog('Setting site locale in processuri plugin');
        $this->setSiteLocale();
        
        // If the Action is not populated add the Config Values
        if( !$action->getModule() )
        {        	
            $action->setModule( config( 'root.action.module' ) );
            $action->setMethod( config( 'root.action.method' ) );
            $action->setParams( config( 'root.action.params' ) );
        }        
        
        // Check for cache & modify $action (in checkCache() method) to use cache if available
        $action->setCache_mode('page');
        $blnCache = $this->getManager( 'cache' )->caching($action, $mode="page");
        
        // IF cache is disable
        // 		$blnCache = false || config('cache.page.enabled') == false )
        // OR cache is enable but the cache file is not found
        //		$blnCache = true && config('cache.page.enabled') == true && config('cache.page.available') == false
        // THEN page should be rendered from the beginning & we will load all the plugins
        
        if (!$blnCache || ( !config ( 'cache.page.available', false ) && config('cache.page.enabled', false))){
        	// Now we have all Config values load the root Plugins
        	// Speed bottleneck here !!!!        	
        	$this->loadPlugin( config( 'root.plugins' ));
        }
        return $action;
	}


}
?>