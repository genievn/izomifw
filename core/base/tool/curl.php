<?php
/**
 * @version $Id: Curl.php
 * @package CapeCodeHQ o.php
 * @author: Richard Allinson
 * @copyright Copyright (C) 2006 Richard Allinson. All rights reserved.
 * @email: ophp@capecodehq.com
 * @license: GNU General Public License (GPL)
 *
 * http://www.gnu.org/licenses/gpl.html
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Class Curl
 * @package CapeCodeHQ o.php
 */
class Curl extends Object
{
  private $handle = null;
  private $count  = 0;
  private $stack  = array();

  /**
   * Returns a CallBack Object referancing the result of calling the passed $url
   *
   * @param String $url
   * @param String $type
   * @return CallBack
   */
  public function get( $url, $type='string' )
  {
    return $this->post( $url, null, $type );
  }

  /**
   * Returns a CallBack Object referancing the result of calling the passed $url
   *
   * @param String $url
   * @param String $postArgs
   * @param String $type
   * @return CallBack
   */
  public function post( $url, $postArgs=null, $type='string' )
  {
    $args = func_get_args();
    $curlKey = implode( '', $args );

    if( !$this->hasCurlKey( $curlKey ) )
    {
      $curl = curl_init();
       
      curl_setopt( $curl, CURLOPT_URL, $url );
      curl_setopt( $curl, CURLOPT_HEADER, false );
      curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
      curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
      curl_setopt( $curl, CURLOPT_MAXREDIRS, 3 );

      if( $postArgs )
      {
        curl_setopt( $curl, CURLOPT_POST, true );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $postArgs );
      }

      $this->addCurlKey( $curlKey, $curl );
    }

    return lambda( $this, 'call', $curlKey, $type );
  }

  /**
   * Returns the result of $curlKey in the format of $type
   *
   * @param String $curlKey
   * @param String $type
   * @return String | Array
   */
  public function call( $curlKey, $type )
  {
    if( is_resource( $this->stack[$curlKey] ) ) $this->process();

    return $this->parse( $this->stack[$curlKey], $type );
  }

  /**
   * Executes any CURL operations stacked so far
   */
  private function process()
  {
    $active = null;
    
    do
    {
      $mrc = curl_multi_exec( $this->gethandle(), $active );
    }
    while( $mrc == CURLM_CALL_MULTI_PERFORM );

    while( $active && $mrc == CURLM_OK )
    {
      if( curl_multi_select( $this->gethandle() ) != -1 )
      {
        do
        {
          $mrc = curl_multi_exec( $this->gethandle(), $active );
        }
        while( $mrc == CURLM_CALL_MULTI_PERFORM );
      }
    }

    foreach( $this->getStack() as $curlKey => $curl )
    {
      if( is_resource( $curl ) ) $this->getContent( $curlKey, $curl );
    }

    //error_log( "Curl reset count: ".$this->count ); // TODO: make a real log here
  }
  
  private function getContent( $curlKey, $curl )
  {
    $this->stack[$curlKey] = curl_multi_getcontent( $curl );

    curl_multi_remove_handle( $this->handle, $curl );

    $this->count--;

    if( $this->count == 0 ) curl_multi_close( $this->handle );
  }

  private function getStack()
  {
    return $this->stack;
  }

  private function gethandle()
  {
    return $this->handle;
  }

  private function hasCurlKey( $curlKey )
  {
    return isset( $this->stack[$curlKey] );
  }

  private function addCurlKey( $curlKey, $curl )
  {
    if( $this->count == 0 ) $this->handle = curl_multi_init();

    curl_multi_add_handle( $this->handle, $curl );

    $this->stack[$curlKey] = $curl;

    $this->count++;

    //error_log( "Curl add count: ".$this->count ); // TODO: make a real log here
  }

  /**
   * Convert $data to $type
   *
   * @param String $data
   * @param String $type
   * @return String | Array
   */
  private function parse( $data, $type='string' )
  {
    switch( strtolower( $type ) )
    {
      case 'sphp':
        return $this->unserializePhp( $data );
      case 'json':
        return $this->unserializeJson( $data );
      default:
        return $this->unserialize( $data );
    }
  }

  /**
   * Method to unserialize php, can be overriden with a Lambda
   *
   * @param String $$data
   * @return Object | Array
   */
  public function unserializePhp( $data )
  {
    if( $lambda = $this->__lambda( 'unserializephp' ) )
    {
      return $lambda->call( $data );
    }

    return @unserialize( $data );
  }

  /**
   * Method to unserialize json, can be overriden with a Lambda
   *
   * @param String $$data
   * @return Object | Array
   */
  public function unserializeJson( $data )
  {
    if( $lambda = $this->__lambda( 'unserializejson' ) )
    {
      return $lambda->call( $data );
    }

    return @json_decode( $data, true );
  }

  /**
   * Method to unserialize string, can be overriden with a Lambda
   *
   * @param String $$data
   * @return Object | Array | String
   */
  public function unserialize( $data )
  {
    if( $lambda = $this->__lambda( 'unserialize' ) )
    {
      return $lambda->call( $data );
    }

    return $data;
  }
}
?>