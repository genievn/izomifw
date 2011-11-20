<?php
define('CONFIG_PATH_SEPARATOR','.');
define('CONFIG_FILE_NOT_FOUND','__file_not_found__');
class Config {
	static private $accessCount = 0;	
	static private $config = null;			// a reference to itself
	static private $cfgBasket = array();
	static private $history = array( CONFIG_FILE_NOT_FOUND => array() );
    
	private $codeType = null;
	private $textType = null;
	
	private function getCodeType(){
		return $this->codeType;
	}
	private function setCodeType($type){
		$this->codeType = $type;
	}
	
	private function getTextType(){
		return $this->textType;
	}
	
	private function setTextType($type){
		$this->textType = $type;
	}
	/**
	 * Functon to get the existed instance of Config or create a new instance if needed
	 *
	 * @return Config
	 */
	static public function getInstance(){
		if (self::$config) return self::$config;
		self::$config = new Config();
		return self::$config;
		
//		if (!self::$config) self::$config = new Config();
//		return self::$config;
	}
	
	/**
	 * Return the config basket
	 *
	 * @return Array
	 */
	
	static public function getConfig(){
		return self::$cfgBasket;
	}
	
	static public function getHistory(){
		return self::$history;
	}
	
	static public function getAccessCount(){
		return self::$accessCount;
	}
	
	public function getParam($key, $default = null){
		$path = explode(CONFIG_PATH_SEPARATOR, $key);
		$tmpArray = & self::$cfgBasket;
		
		foreach ($path as $key){
			if (!isset($tmpArray[$key])) return $default;
			$tmpArray = & $tmpArray[$key];
			self::$accessCount++;
		}
		return $tmpArray;
	}
	
	public function setParam($key, $value){
		$path = explode(CONFIG_PATH_SEPARATOR,$key);
		$tmpArray = & self::$cfgBasket;
		foreach ($path as $key){
			if (!isset($tmpArray[$key])) $tmpArray[$key] = '';
			$tmpArray = & $tmpArray[$key];
			self::$accessCount++;
		}
		$tmpArray = $value;
		return $this;
	}
	
	
	public function includeText($path){		
		$handler = @fopen($path,"r");
		if (!$handler){
			self::$history[CONFIG_FILE_NOT_FOUND][$path] = $path;
			return false;
		}
		while (!feof($handler)) {
			$buffer = fgets($handler,4096);
			$buffer = rtrim($buffer);		
			
			if (substr($buffer,0,2)!='//' && strpos($buffer,'=')>0){
				list($key, $value) = explode('=',$buffer,2);
				$this->setParam(trim($key),trim($value));
			}
		}
		
	}
	/**
	 * Enter description here...
	 *
	 * @param String $path
	 * @return Boolean
	 */
	public function includePath($path){		
		$fileType = substr($path,-3);
		if (isset(self::$history[$path])) return true;
		
		if ($fileType == $this->getCodeType()){
			if (!@include($path)){
				self::$history[CONFIG_FILE_NOT_FOUND][$path] = $path;
				return false;
			}
		}
		
		if ($fileType == $this->getTextType()){
			if ($this->includeText($path)) return false;
		}
		
		self::$history[$path] = $path;
		return true;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $path
	 * @return unknown
	 */
	private function includeDir($path){		
		if (isset(self::$history[$path])) return true;		
		if (!is_dir($path)) return $this->includePath($path);
		
		if ($handler = opendir($path)){
			while (false !== ($file = readdir($handler))){
				if (substr($file,-3) == $this->getCodeType() || substr($file,-3)== $this->getTextType()){
					$this->includePath($path.DIRECTORY_SEPARATOR.$file);
				}
			}
			closedir($handler);
			self::$history[$path] = $path;
			return true;
		}
		return true;		
	}
	
	private function includeTree($tree, $path){
		foreach ($tree as $dir){
			$path.=DIRECTORY_SEPARATOR.$dir;
			if (!$this->includeDir($path)) return;
		}
	}
	
	public function loadConfig($path, $root='', $code='php', $text='ini'){
		$this->setCodeType($code);
		$this->setTextType($text);
		
		if (is_array($path)){
			$this->includeTree($path,$root);			
		}elseif (is_string($path)){
			$this->includeDir($root.$path);
		}
	}
}
/**
 * Hepler function to replace 
 * Config::getInstance()->setParam( $key, $value )
 * and
 * Config::getInstance()->getParam( $key, $value )
 * 
 * e.g.
 * To set a value append a '.' at the start of the key String
 * config( '.root.key', 'my_value' );
 * 
 * To get a value enter the key String
 * $myValue = config( 'root.key' );
 * 
 * @param String $key
 * @param String $value
 * @return Object
 */
function config( $key, $value=null )
{
    if( substr( $key, 0, 1 ) == CONFIG_PATH_SEPARATOR )
        Config::getInstance()->setParam( substr( $key, 1 ), $value );
    else
        return Config::getInstance()->getParam( $key, $value );
}
?>