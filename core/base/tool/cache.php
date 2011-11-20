<?php
class Cache extends Object { 
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function fetch($cacheFile)
	{		
		if (file_exists($cacheFile) and ((time()-filemtime($cacheFile)) < $this->getRefresh_seconds())){
			$cacheContent = file_get_contents($cacheFile);
		}
		return $cacheContent;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function save()
	{
		return $this->saveToFile();
	}
	
 
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	private function saveToFile()
	{
		$file = fopen($this->getCache_file(),'w');
		fwrite($file, $this->getContent());
		fclose($file);
		return true;
	}
}

class acmeCache extends Object
{
	public function fetch($name, $refreshSeconds = 0){
		if(!$GLOBALS['cache_active']) return false; 
		if(!$refreshSeconds) $refreshSeconds = 60; 	
		$cacheFile = acmeCache::cachePath($name); 
		if(file_exists($cacheFile) and ((time()-filemtime($cacheFile))< $refreshSeconds)) $cacheContent = file_get_contents($cacheFile);
		return $cacheContent;
	} 
 
	public function save($name, $cacheContent){
		if(!$GLOBALS['cache_active']) return; 
		$cacheFile = acmeCache::cachePath($name);
		acmeCache::savetofile($cacheFile, $cacheContent);
	} 
 	function cachePath($name){
		$cacheFolder = $GLOBALS['cache_folder'];
		if(!$cacheFolder) $cacheFolder = trim($_SERVER['DOCUMENT_ROOT'],'/').'/cache/';
		return $cacheFolder . md5(strtolower(trim($name))) . '.cache';			
	}
 
	function savetofile($filename, $data){
		$dir = trim(dirname($filename),'/').'/'; 
		acmeCache::forceDirectory($dir);  
		$file = fopen($filename, 'w');
		fwrite($file, $data); fclose($file);
	} 
  
	function forceDirectory($dir){ // force directory structure 
		return is_dir($dir) or (acmeCache::forceDirectory(dirname($dir)) and mkdir($dir, 0777));
	}
 
}
?>