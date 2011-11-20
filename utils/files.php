<?php
	function find_files($path, $pattern, $callback) {
		//$path = rtrim(str_replace("\\", "/", $path), '/') . '/';
		if (!endsWith($path, DIRECTORY_SEPARATOR)){
			$path = $path.DIRECTORY_SEPARATOR;
		}
		$matches = Array();
		$entries = Array();
		$dir = dir($path);
		while (false !== ($entry = $dir->read())) {
			$entries[] = $entry;
		}
		$dir->close();
		foreach ($entries as $entry) {
			$fullname = $path . $entry;
			if ($entry != '.' && $entry != '..' && is_dir($fullname)) {
				find_files($fullname, $pattern, $callback);
			} else if (is_file($fullname) && preg_match($pattern, $entry)) {
				call_user_func($callback, $fullname);
			}
		}
	}
	
	function directoryToArray($directory, $pattern = false, $recursive = false, $includeDir = false) {
		if (!endsWith($directory, DIRECTORY_SEPARATOR)){
			$directory = $directory.DIRECTORY_SEPARATOR;
		}
		$array_items = array();
		if (is_dir($directory) && $handle = opendir($directory)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if (is_dir($directory. DIRECTORY_SEPARATOR . $file)) {
						if($recursive) {
							$array_items = array_merge($array_items, directoryToArray($directory.$file, $pattern, $recursive, $includeDir));
						}
						$file = $directory . $file;
						if ($includeDir) $array_items[] = preg_replace("/\/\//si", DIRECTORY_SEPARATOR, $file);
					} else {
						$file = $directory . $file;
						if ($pattern == false){
							# if pattern is not provided then add the file to the array
							
							$array_items[] = preg_replace("/\/\//si", DIRECTORY_SEPARATOR, $file);
						}else{
							# check if pattern is correct							
							if (preg_match($pattern, $file)){
								#$file = $directory . $file;
								$array_items[] = preg_replace("/\/\//si", DIRECTORY_SEPARATOR, $file);
							}								
						}
					}
				}
			}
			closedir($handle);
		}
		return $array_items;
	}
	

	function endsWith( $str, $sub ) {
		return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
	}
	function beginsWith( $str, $sub ) {
		return ( substr( $str, 0, strlen( $sub ) ) == $sub );
	}
	// trims off x chars from the front of a string
	// or the matching string in $off is trimmed off
	function trimOffFront( $off, $str ) {
		if( is_numeric( $off ) )
			return substr( $str, $off );
		else
			return substr( $str, strlen( $off ) );
	}

	// trims off x chars from the end of a string
	// or the matching string in $off is trimmed off
	function trimOffEnd( $off, $str ) {
		if( is_numeric( $off ) )
			return substr( $str, 0, strlen( $str ) - $off );
		else
			return substr( $str, 0, strlen( $str ) - strlen( $off ) );
	}
	
	
?>