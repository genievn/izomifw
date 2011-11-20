<?php
define( 'IMPORT_PATH_SEPARATOR', '.');
define( 'IMPORT_FILE_NOT_FOUND', '__file_not_found__');

class Import{
	static private $import = null;
	static private $history = array( IMPORT_FILE_NOT_FOUND => array() );
	private $root = null;

	static public function getInstance(){
		if (self::$import) return self::$import;
		self::$import = new Import();
		return self::$import;

//		if (!self::$config) self::$config = new Config();
//		return self::$config;
	}
	/**
	 * Set starting point to import
	 *
	 * @param String $root
	 */
	public function setImportRoot($root){
		$this->root = $root;
	}

	public function getImportRoot(){
		return $this->root;
	}
	public function importPath($path){
		//Determine a particular class or all(*) should be imported
		$class = $this->getPathClass($path);
		if ($class=='*' || !class_exists($class)){
			//if the class is not existed or we r going to import * then
			$this->loadPath( $this->getRealPath( $path ), ( $class == '*' ) );
		}
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $path
	 * @return unknown
	 */
	private function getPathClass($path){
		$route = explode(IMPORT_PATH_SEPARATOR,$path);
		return $route[count($route)-1];
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $path
	 * @return unknown
	 */
	private function getRealPath($path){
		//Concate with the root to get the real path
		return $this->getImportRoot().str_replace(IMPORT_PATH_SEPARATOR,DIRECTORY_SEPARATOR,$path);
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $path
	 */

	private function loadPath($path, $isFolder = false){
		if (!$isFolder && @include_once($path.'.php')){
			return $this->logImport($path.'.php');
		}
		elseif (!$isFolder){
			return $this->logImport($path.'.php',true);
		}
		$this->includeFolder(str_replace('*','',$path));
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $path
	 */
	public function includeFolder($path){
		//Check if the folder has been imported or not found
		if (isset(self::$history[$path]) || isset(self::$history[IMPORT_FILE_NOT_FOUND][$path])) return;

		//Check if the folder is ok to be opened
		if (!is_dir($path) || !$handler=opendir($path)) return;

		while ( false !== ( $file = readdir( $handler ) ) ){
			if (substr($file,-4)=='.php' || substr($file,-4)=='.inc'){
				if (@include_once($path.$file)){
					$this->logImport($path.$file);
				}else{
					$this->logImport($path.$file,true);
				}
			}
		}
		closedir($handler);
		self::$history[$path]=$path;
	}
	private function logImport($path, $fail = false){
		if ($fail){
			self::$history[IMPORT_FILE_NOT_FOUND][$path] = $path;
		}else {
			self::$history[$path] = $path;
		}
	}
	static public function getHistory(){
		return self::$history;
	}
}

/**
 * Hepler function to replace Import::getInstance()->importPath( $path )
 *
 * @param String $path
 * @return Object
 */
function import( $path )
{
	return Import::getInstance()->importPath( $path );
}
?>