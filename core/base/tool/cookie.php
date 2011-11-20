<?php
/**
 * @version $Id: DataCookie.php
 * @package CapeCode DataObjects for PHP5
 * @author: Richard Allinson
 * @copyright Copyright (C) 2006 Richard Allinson. All rights reserved.
 * @email: dataobjects@capecodehq.com
 * @license: GNU General Public License (GPL)
 */
class Cookie extends Object
{
    private $stored = false;
    
    private $instance = 'object-cookie-root';
    
    private $lifeTime = null;
    private $path = '/';
    private $domain = null;
    private $secure = null;
    
    public function setCookieControls( $lifeTime, $path='/', $domain=null, $secure=null )
    {
        if( $this->stored )
            trigger_error( 'Cookie ERROR, cannot set Cookie Controls after "Cookie::store()" has been called', E_USER_ERROR );
        
        $this->lifeTime = $lifeTime;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        
        return $this;
    }
    
    public function instance( $instance=null )
    {
        if( !$instance ) return $this->instance;
        
        if( $this->stored )
            trigger_error( 'Cookie ERROR, cannot set Instance after "Cookie::store()" has been called', E_USER_ERROR );
            
        $this->instance = $instance;
        
        if( !isset( $_COOKIE[$this->instance()] ) ) return;
        
        $this->properties( $this->decode( $_COOKIE[$this->instance()] ) );
        
        return $this;
    }
    
    public function store()
    {
        if( $this->stored ) return;
         
        if( headers_sent() )
            trigger_error( 'Cookie ERROR, cannot call "Cookie::store()" after HTTP Headers have been sent.', E_USER_ERROR );
        
        $ret = setrawcookie( $this->instance(), $this->encode( $this->properties() ), $this->lifeTime, $this->path, $this->domain, $this->secure );
        
        if ($ret) izlog('Cookie set for '.$this->instance);
        
        $this->stored = true;
        
        return $this;
    }
    
    public function kill()
    {
        if( headers_sent() )
            trigger_error( 'Cookie ERROR, cannot call "Cookie::kill()" after HTTP Headers have been sent.', E_USER_ERROR );
        
        setrawcookie( $this->instance(), '', time()-42000, $this->path, $this->domain, $this->secure );
        
        $this->properties( array() );
        
        $this->stored = true;
        
        return $this;
    }
    
    protected function encode( $data )
    {
        $data = $this->http_build_query( $data );
        
        $data = base64_encode( $data );
        
        return $data;
    }
    
    protected function decode( $data )
    {
        $data = base64_decode( $data );
        
        parse_str( $data, $result );
        
        return $result;
    }
    
    public function __set( $key, $value )
    {    	
        if( $value && !is_scalar( $value ) )
            trigger_error( 'Cookie ERROR, Cookie can only contain integer, float, string or boolean values.', E_USER_ERROR );
        
        if( $this->stored )
            trigger_error( 'Cookie ERROR, cannot set data after "Cookie::store()" has been called', E_USER_ERROR );
        
        parent::__set( $key, $value );
    }
    
    function http_build_query( $formdata, $numeric_prefix = null, $key = null ) {
        $res = array();
        foreach ((array)$formdata as $k=>$v) {
            $tmp_key = urlencode(is_int($k) ? $numeric_prefix.$k : $k);
            if ($key) {
                $tmp_key = $key.'['.$tmp_key.']';
            }
            if ( is_array($v) || is_object($v) ) {
                $res[] = http_build_query($v, null, $tmp_key);
            } else {
                $res[] = $tmp_key."=".urlencode($v);
            }
        }
        return implode("&", $res);
    }
}
?>