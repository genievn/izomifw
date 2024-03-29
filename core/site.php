<?php
class izSite extends Object {
	private $modules = array();
	private $managers = array();
	private $log = array();
	
	public function __construct(){
		if (config('root.debug',false)) $this->loadPlugin('Debug');
		//Load root plugin		
		$this->loadPlugin(config('root.plugins'));
		$this->setSiteLocale();
		$this->setSiteController(object('izController',$this));
	}
	
	public function loadPlugin($plugins, $folders = null){		
		if (!is_array($plugins)) $plugins = array($plugins);
		//if the plugin folder is not specified, use default one
		if (!is_array($folders)) $folders = config('root.app_folders');
		$event = object('Event');
		
		foreach ($folders as $folder){
			foreach ($plugins as $pluginId=>$plugin){
				Event::fire('loadModule',$plugin);
				//Get the plugin class definition
				import($folder.DIRECTORY_SEPARATOR.'plugin'.DIRECTORY_SEPARATOR.$plugin);
				//create plugin object and put into listeners array
				$event->addListener(object($plugin.'Plugin',$this),$pluginId);
			}
		}		
	}
	
	public function getModule($module){
		
		$module = strtolower($module);
		if (!isset($this->modules[$module])){
            // fire loadModule event for the ImportPlugin to load the required module
			Event::fire('loadModule',$module);
			$object = object (ucfirst(strtolower($module))."Controller",$this->getSiteController());
			$object->setModuleName($module);
			// add to the loaded modules array
			$this->modules[$module] = $object;
		}
		

		return $this->modules[$module];
	}
	
	public function getManager($module){
		$module = strtolower($module);
		if (!isset($this->managers[$module])){
			Event::fire('loadModule',$module);
			//Get the manager for that module
			$object = object(ucfirst($module)."Manager",object('izManager',$this));
			$object->setModuleName($module);
			$this->managers[$module] = $object;
		}
		return $this->managers[$module];
	}

	public function run($action){
		$this->setAction($action);
		try{			
			Event::fire('startDebug',$action);			
			Event::fire('startUp',$action);			//Setting up the site: Language, locale
			Event::fire('preSite',$action);			//Process the uri, check for cache version			
			if ( !config('cache.page.available', false) ){
				$this->dispatch($action);				
				Event::fire('postSite',$action);			
				Event::fire('shutDown',$action);
			}else{
				//echo 'Page caching available';
			}
			Event::fire('closeDebug',$action);
		}catch (Exception $e){
			echo $e->__toString();
			#Event::fire('httpPage',404);
		}
		return $action;
	}
	
	public function dispatch($action){		
		Event::fire('preDispatch',$action);
        if (!config("cache.module.{$action->getModule()}.{$action->getMethod()}.available")){
        	//If cache is not available then process as normal        
			$module = $this->getModule($action->getModule());
			//Start an Output buffer to collect data generated by the module
			ob_start();
			//The method will be searched or call the default to get the content
			//An izRender will be created to render the partial layout
			//(which lately be call in izAction->getContent()
			$action->setObject($module->call($action->getMethod(),$action->getParams()));
			// Stop the output buffer and set the izAction content with the collected data
			$action->setContent(ob_get_clean());
		}else{
			//echo 'Module caching available';
		}
		// Fire the postDispatch event, listened by Insert plugin
		// the izRender object will be rendered and set to content of the current action
		
		Event::fire('postDispatch',$action);
		return $action;
	}
	
	public function toString(){		
		echo $this->run(object('izAction'))->getContent();
	}
	
	public function setSiteLocale(){
		$locale = izLocale::getInstance();
		// Re-order the apps folders so the last added is become the first
		$apps = config('root.app_folders');
		krsort($apps);
		$locale->setLocaleFolder(config('root.locale_folder'));	
		$locale->setLocalePath(config('root.abs'));
		$locale->setLocaleUrl(config('root.url'));
		$locale->setLocaleCurrent(config('root.current_locale'));
		$locale->setLocaleDefault(config('root.default_locale'));
		$locale->setLocaleAppFolders($apps);		
	}
}
?>