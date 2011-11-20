<?php
class FilterManager extends Object {
	private $filter = null;
	private $rawRequestData = array();
	
	public function __construct(){
		import('core.base.common.InputFilter');
		$this->filter = object( 'InputFilter' );
	}
	
	public function filterRequest(){
		//print_r($_REQUEST);
		foreach( $_REQUEST as $key => $value )
		{
			$this->setRawRequest( $key, $value );			
			$_REQUEST[$key] = $this->filter( $value );
		}
	    
		foreach( $_POST as $key => $value )
		{
			$this->setRawRequest( $key, $value );			
			$_POST[$key] = $this->filter( $value );
		}
	    
		foreach( $_GET as $key => $value )
		{
			$_GET[$key] = $this->filter( $value );
		}
	
		foreach( $_COOKIE as $key => $value )
		{
			$_COOKIE[$key] = $this->filter( $value );
		}
		//print_r($this->getRawRequest('full_text'));	
	}
	
	private function setRawRequest($key, $value){
		$this->rawRequestData[$key] = $value;
	}
	
	public function getRawRequest($key, $default=null, $type=null){
		if( isset( $this->rawRequestData[$key] ) )
			return $this->getValue( $this->rawRequestData[$key], $default, $type );
		return $default;		
	}
	public function getValue($value, $default=null, $type=null){
		$type = strtolower($type);
		$result = null;
		
		if ((is_null($value))) return $default;
		
		switch ( $type )
		{
			case 'INT' :
			case 'INTEGER' :
				// Only use the first integer value
				@ preg_match('/-?[0-9]+/', $value, $matches);
				$value = @ (int) $matches[0];
				break;

			case 'FLOAT' :
			case 'DOUBLE' :
				// Only use the first floating point value
				@ preg_match('/-?[0-9]+(\.[0-9]+)?/', $value, $matches);
				$value = @ (float) $matches[0];
				break;

			case 'BOOL' :
			case 'BOOLEAN' :
				$value = (bool) $value;
				break;

			case 'ARRAY' :
				if ( !is_array( $value) ) $value = array ($value);
				break;

			case 'STRING' :
				$value = (string) $value;
				break;

			case 'NONE' :
			default :
				// No casting necessary
				break;
		}
		
		return $value;
	}
	
	public function getFilter(){
		return $this->filter;
	}
	
	public function filter($value, $default=null, $type=null){
		if(get_magic_quotes_gpc() && !is_array($value)) $value = stripslashes($value);
		return $this->getValue($this->getFilter()->process($value),$default,$type);
	}
}
