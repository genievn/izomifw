<?php
define( 'TAG_START_MARKER', '<' );
define( 'TAG_START_END_MARKER', '>' );

define( 'TAG_END_START_MARKER', '</' );
define( 'TAG_END_MARKER', '/>' );

class Replace
{
    static public $tagReplaceCount = 0;
    static public $elementReplaceCount = 0;
    
	private $tag = null;
	private $handler = null;
	private $handlerMethods = null;
	
	public function setHandler( $handler )
	{
	    if( !is_object( $handler ) ) return false;
	    
	    $this->handler = $handler;
	    
	    $this->handlerMethods = get_class_methods( $this->handler );
	    
	    return true;
	}
	
	/**
	 * Finds a tag with $tagName in $xml and then calls a function in the 
	 * $this->handler with the same name as the $tagName passing any
	 * params in the tag as arguments. The return value from the function
	 * is then inserted in replace of full tag found by $tagName.
	 *
	 * @param String $xml
	 * @param String $tagName
	 * @return String
	 */
	public function tag( $xml, $tagName=null, $argsAsArray=false )
	{		
	    while( true )
	    {
    	    if( !$this->handler || !$tagName && !$this->getTagName() ) return $xml;
    	    if( $tagName ) $this->setTagName( $tagName );
    	    
    		$start = strpos( $xml, TAG_START_MARKER.$this->getTagName() );
    		if( $start === false ) return $xml;
    		
    		$end = strpos( $xml, TAG_END_MARKER, $start ) + strlen( TAG_END_MARKER );
    		if( $end < $start ) return $xml;
    		
    		$tag = substr( $xml, $start, $end-$start );
    		$args = $this->getTagArgs( $tag );
    		
    		$xml = str_replace( $tag, $this->call( $this->getTagName(), $args, $argsAsArray ), $xml );
    		
    		self::$tagReplaceCount++;
	    }
	}
	
	/**
	 * Finds an element with $tagName in $xml and then calls a function in the 
	 * $this->handler with the same name as the $tagName passing any
	 * params in the tag as arguments. The return value from the function
	 * is then inserted in replace of full element found by $tagName.
	 *
	 * @param String $xml
	 * @param String $tagName
	 * @return String
	 */
	public function element( $xml, $tagName=null, $argsAsArray=false )
	{		
	    while( true )
	    {
    	    if( !$this->handler || !$tagName && !$this->getTagName() ) return $xml;
    	    if( $tagName ) $this->setTagName( $tagName );
    	    
    	    $startMarker = TAG_START_MARKER.$this->getTagName();
    		$endMarker = TAG_END_START_MARKER.$this->getTagName().TAG_START_END_MARKER;
    		
    	    $start = strpos( $xml, $startMarker );
    		if( $start === false ) return $xml;
    		
    		$endTag = strpos( $xml, TAG_START_END_MARKER, $start ) + strlen( TAG_START_END_MARKER );
    		if( $endTag < $start ) return $xml;
    		
    		$end = strpos( $xml, $endMarker, $start ) + strlen( $endMarker );
    		
    		if( $end < $start ) return $xml;
    		
    		$tag = substr( $xml, $start, $endTag-$start );
    		$args = $this->getTagArgs( $tag );
    		
    		$element = substr( $xml, $start, $end-$start );
    		$content = $this->getElementContent( $element, $tag );
    		
    		$args = array_merge( array( 'content'=>$content ), $args );
    		
    	    $xml = str_replace( $element, $this->call( $this->getTagName(), $args, $argsAsArray ), $xml );
    		
    	    self::$elementReplaceCount++;
	    }
	}
	
	private function getTagArgs( $tag )
	{
	    $tag = str_replace( TAG_START_MARKER.$this->getTagName(), '', $tag );
	    $tag = str_replace( TAG_END_MARKER, '', $tag );
	    $tag = str_replace( TAG_START_END_MARKER, '', $tag ); // TODO: Hmm, is this safe for all use-cases?
	    
	    $result = array(); $join = '';
	    $args = explode( ' ', $tag );
	    
	    foreach( $args as $arg )
	    {
	        if( $join ) $arg = $join.' '.$arg;
	        
	        if( trim( $arg ) && strpos( $arg, '=' ) !== false )
	        {
    	        list( $key, $value ) = explode( '=', $arg, 2 );
    	        
    	        if( substr( $value, -1 ) != '"' )
    	        {
    	            $join = $key.'='.$value;
    	        }
    	        else
    	        {
    	            $result[$key] = str_replace( '"', '', $value );
    	            $join = '';
    	        }
	        }
	    }

	    return $result;
	}
	
	private function getElementContent( $element, $tag )
	{
	    $element = str_replace( $tag, '', $element );
	    $element = str_replace( TAG_END_START_MARKER.$this->getTagName().TAG_START_END_MARKER, '', $element );
	    
	    return $element;
	}
	
	private function call( $method, $args=array(), $argsAsArray=false )
	{
	    $method = str_replace( ':', '_', $method );
	    
	    if( $argsAsArray ) $args = array( $args );
	    
	    return call_user_func_array( array( $this->handler, $method ), $args );
	}
	
	private function setTagName( $tag ){ $this->tag = $tag; }
	
	private function getTagName(){ return $this->tag; }
}
?>