<?php
class DesktopController extends Object
{
	static private $jsSources = array();
	static private $modules = array(
			"demo-bogus" => array(
								"dir"	=>"bogus",
								"ignores"	=>array("bogus-win.js"),
								"includes"	=>array()
							),
			"demo-iframe" => array(
								"dir"	=>"iframe",
								"ignores"	=>array("iframe-win.js"),
								"includes"	=>array()
							)
							
		);
	
	public function defaultCall(){
		$render=$this->getTemplate('default');
		return $render;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function loadJsModule()
	{	
			
		$render = $this->getTemplate('load_js_module');		
		$error = object('errors');
		$moduleId = @$_REQUEST["moduleId"];
		
		if ($moduleId){
			$module_source = '';
			$render->setSuccess(true);			
			self::findJsFiles(config('idesk.path.doc_root').config('idesk.path.module_dir').DIRECTORY_SEPARATOR.self::$modules[$moduleId]["dir"].DIRECTORY_SEPARATOR,'/js$/', $ignores=self::$modules[$moduleId]["ignores"]);
			foreach (self::$jsSources as $js => $source) {
				# code...
				$module_source = $module_source."\n".$source;
			}
			$render->setJssource($module_source);
			//echo $module_source;
		}else{
			$render->setSuccess(false);
			$error->setReason("No module ID information");
			$render->setError($error);
		}
		
		return $render;
	}
	
	/**
	 * Generate a domain specific config file (paths, urls etc..)
	 *
	 * @return void
	 * @author user
	 **/
	public function jsConfig()
	{
		$render = $this->getTemplate('js_config');
		return $render;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	private function loadJsFileContent($jsFile)
	{
		echo 'hi';
		$jsSources[$jsFile] = file_get_contents($jsFile);
	}
	private static function findJsFiles($path, $pattern, $ignores) {
		//$path = rtrim(str_replace("\\", "/", $path), '/') . '/';
		if (!endsWith($path, DIRECTORY_SEPARATOR)){
			$path = $path.DIRECTORY_SEPARATOR;
		}
		$matches = Array();
		$entries = Array();
		if (is_dir($path))
			$dir = dir($path);
		else
			return;
		while (false !== ($entry = $dir->read())) {
			$entries[] = $entry;
		}
		$dir->close();
		foreach ($entries as $entry) {
			$fullname = $path . $entry;
			if ($entry != '.' && $entry != '..' && is_dir($fullname)) {
				self::findJsFiles($fullname, $pattern, $ignores);
			} else if (is_file($fullname) && !in_array($entry, $ignores) && preg_match($pattern, $entry)) {
				
				self::$jsSources[$fullname] = file_get_contents($fullname);
				//call_user_func($callback, $fullname);
			}
		}
	}

}
?>