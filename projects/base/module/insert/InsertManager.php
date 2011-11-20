<?php
/**
 * This module for parsing the partials (embed views in view)
 *
 */
class InsertManager extends Object
{
	public function doInsert( $xml )
	{
		// Replace all the tags with content
		$xml = $this->getReplacer()->tag( $xml, 'iz:insert:module' );
		$xml = $this->getReplacer()->tag( $xml, 'iz:insert:config' );
		$xml = $this->getReplacer()->tag( $xml, 'iz:insert:event' );
		$xml = $this->getReplacer()->tag( $xml, 'iz:insert:url' );
		$xml = $this->getReplacer()->tag( $xml, 'iz:insert:uri' );
		
		return $xml;
	}
	
	public function iz_insert_module( $module=null, $method=null, $params=null )
	{
		if( !$module ) return;
		
		$action = object( 'izAction' );
		$action->setModule( $module );
		$action->setMethod( $method );
		$action->setParams( explode( ',', $params ) );
		
		return $this->dispatch( $action )->getContent();
	}
	
	/**
	 * Returns the site URL
	 * This is a wrapper for <iz:layout:config path="root.uri" />
	 *
	 * @return String
	 */
	public function iz_insert_uri( $sub=null )
	{
		$url = $this->iz_insert_config( 'root.uri' );
		
		if( $sub )
		{
			$start = strpos( $url, '/@')+2;
			
			if( $start == 2 )
			{
				$start = strlen( $url )+1;
				$url.= '@'.config( 'root.host' ).'/';
			}
			
			$lenght = strpos( $url, '/', $start )-$start;
			$domain = substr( $url, $start, $lenght );
			
			$url = str_replace( '/@'.$domain, '/@'.$this->subDomainReplace( $domain, $sub ), $url );
		}
		
		return $url;
	}
	
	/**
	 * Returns the site URI
	 * This is a wrapper for <iz:layout:config path="root.url" />
	 *
	 * @return String
	 */
	public function iz_insert_url( $sub=null )
	{    	
		$url = $this->iz_insert_config( 'root.url' );
		
		if( $sub )
		{
			$start = strpos( $url, '://')+3;
			$lenght = strpos( $url, '/', $start )-$start;
			$domain = substr( $url, $start, $lenght );
			
			$url = str_replace( '://'.$domain, '://'.$this->subDomainReplace( $domain, $sub ), $url );
		}
		
		return $url;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $path
	 * @return unknown
	 */
	public function iz_insert_config( $path=null, $default=null )
	{    	
		if( !$path ) return;        
		return config( $path, $default );
	}
	
	public function iz_insert_event( $fire=null )
	{
		if( !$fire ) return;
		
		return Event::fire( $fire );
	}
	
	private function subDomainReplace( $domain, $sub )
	{
		$domain = explode( '.', $domain );
		$sub = explode( '.', $sub );
		
		if( !is_array( $domain ) ) $domain = array( $domain );
		if( !is_array( $sub ) ) $sub = array( $sub );
		
		return implode( '.', array_merge( $sub, array_slice( $domain, count( $sub ) ) ) );
	}
	
	/**
	 * @return ReplaceHelper
	 */
	private function getReplacer()
	{
		if( !$this->replacer )
		{
			$this->replacer = object( 'Replace' );
			$this->replacer->setHandler( $this );
		}
		
		return $this->replacer;
	}
}
?>